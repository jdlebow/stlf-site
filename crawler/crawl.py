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


def parse_cards(html):
    page = {}
    for m in re.finditer(r'data-event-details=["\']([A-Za-z0-9+/=]+)["\']', html):
        try:
            j = json.loads(base64.b64decode(m.group(1)))
        except Exception:
            continue
        if j.get('vin'):
            page[j['vin']] = (j.get('location') or {}).get('name', '')
    return page


def list_vins_once(path):
    """One pass over every list page. Returns ({vin: location}, advertised total or None).
    A page that comes back empty or already seen is fetched again, slowly, until the cards
    seen cover the count the site advertises, so a throttled page is not read as the end."""
    found, total = {}, None
    for n in range(1, MAX_PAGES + 1):
        url = '%s%ssrp-page-%d/' % (BASE, path, n)
        page = {}
        for tryn in range(5):
            html = get(url)
            if total is None:
                m = re.search(r'([\d,]+)\s+Vehicles\s+Found', html, re.I)
                if m:
                    total = int(m.group(1).replace(',', ''))
            page = parse_cards(html)
            fresh = [v for v in page if v not in found]
            if fresh:
                break
            if total and len(found) >= 0.95 * total:
                break
            print('page %d of %s gave %d cards, %d new, %d bytes (try %d): %s' % (
                n, path, len(page), len(fresh), len(html), tryn + 1, re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', html))[:160]))
            time.sleep(15 * (tryn + 1))
        if not [v for v in page if v not in found]:
            break
        found.update(page)
        time.sleep(1.0)
    return found, total


def list_vins(path):
    """Same as one pass, but retries until the cards seen cover the count the site advertises,
    so a half loaded list can never be mistaken for the whole list."""
    best, best_total = {}, None
    for attempt in range(4):
        found, total = list_vins_once(path)
        if len(found) > len(best):
            best, best_total = found, total
        if best_total and len(best) >= 0.95 * best_total:
            return best
        print('list %s attempt %d: %d cards, site says %s, retrying' % (path, attempt + 1, len(found), total))
        time.sleep(20)
    if best_total and len(best) < 0.95 * best_total:
        raise RuntimeError('list %s incomplete: %d cards, site says %s' % (path, len(best), best_total))
    return best


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


def probe():
    """Diagnostics only: show what the runner gets for the first used and new list pages."""
    for path in LISTS:
        seen = set()
        for n in (1, 2, 3, 4, 5, 6, 8):
            url = '%s%ssrp-page-%d/' % (BASE, path, n)
            r = S.get(url, timeout=40)
            cards = parse_cards(r.text)
            m = re.search(r'([\d,]+)\s+Vehicles\s+Found', r.text, re.I)
            print('PROBE %s page %d: HTTP %s final %s bytes %d cards %d new %d found-text %s hdr %s' % (
                path, n, r.status_code, r.url, len(r.text), len(cards), len([v for v in cards if v not in seen]),
                m.group(0) if m else None,
                {k: v for k, v in r.headers.items() if k.lower() in ('server', 'x-cache', 'cf-cache-status', 'age', 'x-served-by', 'via', 'cache-control', 'content-encoding')}))
            seen.update(cards)
            time.sleep(2)


def main():
    if os.environ.get('PROBE'):
        return probe()
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

    prev, prev_used, prev_new = 0, 0, 0
    try:
        old = json.load(open(OUT))
        prev = len(old)
        prev_used = sum(1 for r in old if r['u'])
        prev_new = prev - prev_used
    except Exception:
        pass
    used_now = sum(1 for r in rows if r['u'])
    new_now = len(rows) - used_now
    if not rows or (prev and len(rows) < prev * MIN_RATIO):
        sys.exit('Only %d vehicles now versus %d before. Not overwriting.' % (len(rows), prev))
    # each side is checked on its own so a bad used list can not hide behind a good new list
    if prev_used and used_now < prev_used * 0.8:
        sys.exit('Only %d used now versus %d before. Not overwriting.' % (used_now, prev_used))
    if prev_new and new_now < prev_new * 0.8:
        sys.exit('Only %d new now versus %d before. Not overwriting.' % (new_now, prev_new))

    rows.sort(key=lambda r: (r['u'], r['n'] or '', r['v']))
    with open(OUT, 'w', encoding='utf-8') as f:
        json.dump(rows, f, ensure_ascii=False, separators=(',', ':'))
    with open(os.path.join(os.path.dirname(OUT), 'meta.json'), 'w') as f:
        json.dump({'generated': int(time.time()), 'iso': time.strftime('%Y-%m-%dT%H:%M:%SZ', time.gmtime()), 'vehicles': len(rows), 'new': new_now, 'used': used_now}, f)
    print('wrote %d vehicles (%d new, %d used), previous %d' % (
        len(rows), sum(1 for r in rows if not r['u']), sum(1 for r in rows if r['u']), prev))


if __name__ == '__main__':
    main()
