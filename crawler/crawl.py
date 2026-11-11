#!/usr/bin/env python3
"""Daily inventory pull for the St. Louis Ford plugin.

Reads the public list pages and vehicle pages of the source dealer site,
keeps only vehicles physically at the Ford store, and writes
inventory/inventory.json in the compact format the plugin reads.

The source site's crawl key comes from the JAZEL_KEY environment variable
(a GitHub Actions secret). It is sent as the x-jzl-bypass-key header and is
never written to disk or printed.
"""
import base64, json, os, re, sys, time
from concurrent.futures import ThreadPoolExecutor
import requests

BASE = 'https://www.davesinclairford.com'
LISTS = ['/new-ford-vehicles-st-louis-mo/', '/used-cars-trucks-suvs-st-louis-mo/']
STORE = 'Dave Sinclair Ford'
OUT = os.path.join(os.path.dirname(__file__), '..', 'inventory', 'inventory.json')
MAX_PAGES = 80
MIN_RATIO = 0.70   # refuse to overwrite if the crawl finds far fewer cars than last time

KEY = os.environ.get('JAZEL_KEY', '').strip()
if not KEY:
    sys.exit('JAZEL_KEY is not set')

S = requests.Session()
S.headers.update({
    'x-jzl-bypass-key': KEY,
    'User-Agent': 'Mozilla/5.0 (compatible; STLF-inventory-sync/1.0)',
    'Accept': 'text/html,application/json',
})


def get(url, tries=4):
    last = None
    for i in range(tries):
        try:
            r = S.get(url, timeout=40)
            if r.status_code == 200:
                return r.text
            last = 'HTTP %s' % r.status_code
        except requests.RequestException as e:
            last = str(e)
        time.sleep(2 + 3 * i)
    raise RuntimeError('%s failed: %s' % (url, last))


def list_vins(path):
    """Return {vin: location} for every card on every list page of one list."""
    found = {}
    for n in range(1, MAX_PAGES + 1):
        html = get('%s%ssrp-page-%d/' % (BASE, path, n))
        page = {}
        for m in re.finditer(r'data-event-details=["\']([A-Za-z0-9+/=]+)["\']', html):
            try:
                j = json.loads(base64.b64decode(m.group(1)))
            except Exception:
                continue
            if j.get('vin'):
                page[j['vin']] = (j.get('location') or {}).get('name', '')
        if not page or all(v in found for v in page):
            break
        found.update(page)
        time.sleep(0.4)
    return found


def brace_json(t):
    i = t.find('{"accountAddress"')
    if i < 0:
        return None
    d, ins, esc = 0, False, False
    for j in range(i, len(t)):
        c = t[j]
        if ins:
            if esc: esc = False
            elif c == '\\': esc = True
            elif c == '"': ins = False
        else:
            if c == '"': ins = True
            elif c == '{': d += 1
            elif c == '}':
                d -= 1
                if d == 0:
                    return json.loads(t[i:j + 1])
    return None


def fix_engine(e):
    e = (e or '').strip()
    return e + ' engine' if re.fullmatch(r'\d(\.\d)?L', e) else e


def vehicle(vin):
    t = get('%s/vehicle/%s/' % (BASE, vin))
    r = brace_json(t)
    if not r:
        raise RuntimeError('no record for ' + vin)
    ph, seen = [], set()
    for m in re.finditer(r'data-source="[^"]*?/media/(\d{6,})', t):
        if m.group(1) not in seen:
            seen.add(m.group(1)); ph.append(m.group(1))
    return {
        'v': r['vin'], 's': r.get('stockNumber'), 'y': str(r.get('year')),
        'mk': r.get('make'), 'md': r.get('model'), 'tr': r.get('trim'), 'n': r.get('name'),
        # the source labels are swapped: extColor holds the interior, intColor the exterior.
        # The plugin already expects e = interior, i = exterior.
        'e': r.get('extColor'), 'i': r.get('intColor'),
        'u': 1 if r.get('isUsed') else 0, 'mi': r.get('mileage'),
        'p': [[x.get('Label'), x.get('Value')] for x in (r.get('pricing') or []) if x.get('Value') is not None],
        'pr': r.get('price'), 'dt': r.get('drivetrain'), 'tm': r.get('transmission'),
        'en': fix_engine(r.get('engine')), 'fu': r.get('fuelType'),
        'mc': r.get('mpgCity'), 'mh': r.get('mpgHighway'), 'ty': r.get('vehicleType'),
        'tg': r.get('tags') or '', 'ph': ph,
    }


def main():
    vins = {}
    for path in LISTS:
        for _ in range(2):   # two passes: cars shift between pages while the list changes
            vins.update(list_vins(path))
    mine = sorted(v for v, loc in vins.items() if loc == STORE)
    print('cards seen %d, at %s %d' % (len(vins), STORE, len(mine)))

    with ThreadPoolExecutor(max_workers=4) as ex:
        rows = list(ex.map(vehicle, mine))
    def has_price(r):
        return any(l in ('Sale Price', 'Price') and (v or 0) > 0 for l, v in r['p'])
    # skip cars with no photos or no real price (in transit, call for price)
    rows = [r for r in rows if r['ph'] and has_price(r)]

    prev = 0
    try:
        prev = len(json.load(open(OUT)))
    except Exception:
        pass
    if not rows or (prev and len(rows) < prev * MIN_RATIO):
        sys.exit('Only %d vehicles now versus %d before. Not overwriting.' % (len(rows), prev))

    rows.sort(key=lambda r: (r['u'], r['n'] or '', r['v']))
    with open(OUT, 'w', encoding='utf-8') as f:
        json.dump(rows, f, ensure_ascii=False, separators=(',', ':'))
    print('wrote %d vehicles (%d new, %d used), previous %d' % (
        len(rows), sum(1 for r in rows if not r['u']), sum(1 for r in rows if r['u']), prev))


if __name__ == '__main__':
    main()
