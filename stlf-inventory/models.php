<?php
if (!defined('ABSPATH')) exit;

/* Model landing pages, hub, specials and home hero. */

function stlf_models() {
    return [
        'f-150' => ['label' => 'F-150', 'full' => 'Ford F-150', 'rx' => '/^F-150$/', 'type' => 'truck',
            'intro' => 'The F-150 is the truck most St. Louis shoppers start with, and the lot below shows what is on the ground at 7466 S. Lindbergh right now. Cab style, bed length and engine change what each truck really is, so compare the exact build, not just the trim name.',
            'who' => 'It suits drivers who tow a trailer or boat on weekends, haul for work, or want a full size crew cab for the family. If you only need a smaller bed for home projects, look at the Maverick or Ranger too.',
            'cross' => ['super-duty' => 'Towing a heavy fifth wheel or equipment trailer? Look at Super Duty.', 'maverick' => 'Want a smaller, lower priced truck? See Maverick.'],
            'faq' => [['Which F-150 trim should I pick?', 'XL and STX are the work and value trims, XLT is the everyday step up, and Lariat and above add leather and more technology. The trims in stock right now are listed in the table on this page.'], ['Which engine is in the F-150s you have?', '%ENGINES%'], ['Can I check the real towing capacity of a specific truck?', 'Towing depends on the engine, axle ratio, cab, bed and package. Open the truck you are considering and ask us to confirm its rating before you buy.']]],
        'bronco-sport' => ['label' => 'Bronco Sport', 'full' => 'Ford Bronco Sport', 'rx' => '/^Bronco Sport$/', 'type' => 'SUV',
            'intro' => 'Bronco Sport is a compact SUV with standard all wheel drive and a boxy, roomy cabin. These are the Bronco Sports on our lot today.',
            'who' => 'It fits couples and small families who want one vehicle for the commute and weekend trips, with easy parking and room for gear.',
            'cross' => ['bronco' => 'Want more trail capability and a removable top? See Bronco.', 'explorer' => 'Need a third row? See Explorer.'],
            'faq' => [['What trims does the Bronco Sport come in?', 'The trims in stock right now are listed in the table on this page, with the lowest sale price for each.'], ['Does the Bronco Sport come with all wheel drive?', '%DRIVE%'], ['What is the difference between Bronco and Bronco Sport?', 'Bronco is the larger body on frame model with removable roof and doors. Bronco Sport is a smaller, car based SUV built for daily driving with light trail use.']]],
        'maverick' => ['label' => 'Maverick', 'full' => 'Ford Maverick', 'rx' => '/^Maverick$/', 'type' => 'truck',
            'intro' => 'Maverick is the compact pickup that costs less than most trucks and fits in a regular parking space. Here is every Maverick on our lot with its sale price.',
            'who' => 'It works for city and suburban drivers who want a bed for mulch, bikes and weekend projects without paying for a full size truck.',
            'cross' => ['f-150' => 'Need more towing and bed length? See F-150.', 'ranger' => 'Want a midsize truck? See Ranger.'],
            'faq' => [['Which Maverick trims are in stock?', 'The trims on our lot right now are listed in the table on this page, each with its lowest sale price.'], ['Which Maverick engines are available?', '%ENGINES%'], ['Is the Maverick a good first truck?', 'Many buyers choose it as a first truck because of the lower price and easy size. Bring your trade and we will show you real numbers.']]],
        'explorer' => ['label' => 'Explorer', 'full' => 'Ford Explorer', 'rx' => '/^Explorer$/', 'type' => 'SUV',
            'intro' => 'Explorer is Ford\'s three row family SUV. These are the Explorers on our lot, with the discount and sale price on each.',
            'who' => 'It suits families who need seating for up to seven, plus drivers who tow a small trailer or boat.',
            'cross' => ['expedition' => 'Need even more room and towing? See Expedition.', 'bronco-sport' => 'Want something smaller? See Bronco Sport.'],
            'faq' => [['Which Explorer trims do you have?', 'The trims in stock right now are in the table on this page with the lowest sale price for each.'], ['Does the Explorer have a third row?', 'Yes, the Explorer is a three row SUV. Seating layout varies by trim, so ask us to confirm on the exact vehicle.'], ['What engines are in the Explorers on your lot?', '%ENGINES%']]],
        'bronco' => ['label' => 'Bronco', 'full' => 'Ford Bronco', 'rx' => '/^Bronco$/', 'type' => 'SUV',
            'intro' => 'Bronco is the body on frame off road SUV with a removable roof and doors. These are the Broncos on our lot today.',
            'who' => 'It fits drivers who want an SUV that can take on trails and still handle the daily drive, and who like the open air feel.',
            'cross' => ['bronco-sport' => 'Want a smaller, easier daily driver? See Bronco Sport.', 'explorer' => 'Need three rows? See Explorer.'],
            'faq' => [['Which Bronco trims are in stock?', 'The trims in stock right now are in the table on this page with the lowest sale price for each.'], ['Does the Bronco have removable doors and a removable roof?', 'Yes. The Bronco is built with a removable roof and doors. Roof options vary by build, so ask us to confirm on the exact vehicle.'], ['Which engines are on your Broncos?', '%ENGINES%']]],
        'super-duty' => ['label' => 'Super Duty', 'full' => 'Ford Super Duty', 'rx' => '/^Super Duty/', 'type' => 'truck',
            'intro' => 'Super Duty trucks are built for heavy towing and hauling. This page covers the F-250, F-350 and F-550 models on our lot.',
            'who' => 'It is the right class for fifth wheel campers, large equipment trailers and work trucks that carry heavy loads every day.',
            'cross' => ['f-150' => 'Need a half ton? See F-150.', 'commercial' => 'Looking for vans and cutaways? See commercial vehicles.'],
            'faq' => [['What is the difference between an F-150 and a Super Duty?', 'Super Duty trucks are built for heavier loads and bigger trailers than a half ton F-150. The right one depends on what you tow and haul.'], ['Which Super Duty models do you have?', 'The models and trims in stock right now are in the table on this page.'], ['Do you have single and dual rear wheel trucks?', 'Our inventory includes both. The name of each truck shows SRW for single rear wheel or DRW for dual rear wheel.']]],
        'expedition' => ['label' => 'Expedition', 'full' => 'Ford Expedition', 'rx' => '/^Expedition/', 'type' => 'SUV',
            'intro' => 'Expedition is Ford\'s full size three row SUV, with a longer MAX version. These are the Expeditions on our lot.',
            'who' => 'It fits large families and anyone who tows a camper or boat and still wants a quiet, roomy cabin.',
            'cross' => ['explorer' => 'Want a smaller three row? See Explorer.', 'super-duty' => 'Need to tow even heavier? See Super Duty.'],
            'faq' => [['What is the difference between Expedition and Expedition MAX?', 'Expedition MAX is the longer wheelbase version with more cargo space behind the third row.'], ['Which Expedition trims are in stock?', 'The trims on our lot right now are in the table on this page.'], ['Which engine is in the Expeditions you have?', '%ENGINES%']]],
        'ranger' => ['label' => 'Ranger', 'full' => 'Ford Ranger', 'rx' => '/^Ranger$/', 'type' => 'truck',
            'intro' => 'Ranger is Ford\'s midsize pickup. Here are the Rangers on our lot with their sale prices.',
            'who' => 'It fits drivers who want more truck than a Maverick but an easier size to park and drive than a full size F-150.',
            'cross' => ['maverick' => 'Want a smaller truck? See Maverick.', 'f-150' => 'Want a full size truck? See F-150.'],
            'faq' => [['Which Ranger trims are in stock?', 'The trims on our lot right now are in the table on this page.'], ['Which engine is in the Rangers you have?', '%ENGINES%'], ['How is a Ranger different from an F-150?', 'Ranger is a midsize truck. F-150 is full size, with more cab and bed choices and higher towing options.']]],
        'mustang' => ['label' => 'Mustang', 'full' => 'Ford Mustang', 'rx' => '/^Mustang$/', 'type' => 'car',
            'intro' => 'Mustang is the two door coupe and convertible with a V8 and EcoBoost lineup. These are the Mustangs on our lot.',
            'who' => 'It is for drivers who want a performance car they can also use every day.',
            'cross' => ['f-150' => 'Need a truck instead? See F-150.', 'explorer' => 'Need more seats? See Explorer.'],
            'faq' => [['Which Mustangs are in stock?', 'The trims on our lot right now are in the table on this page.'], ['Which engines are on your Mustangs?', '%ENGINES%'], ['Can I trade in my current car on a Mustang?', 'Yes. Use the trade page for a value and we will apply it to the Mustang you choose.']]],
        'commercial' => ['label' => 'Commercial vans and cutaways', 'full' => 'Ford Transit and E-Series', 'rx' => '/^(Transit|E-Series)/', 'type' => 'van',
            'intro' => 'Transit vans and E-Series cutaways for business use. These are the commercial vehicles on our lot.',
            'who' => 'They suit contractors, delivery fleets, shuttle operators and anyone who needs a van or a cutaway chassis to upfit.',
            'cross' => ['super-duty' => 'Need a work truck? See Super Duty.', 'f-150' => 'Need a half ton? See F-150.'],
            'faq' => [['Which commercial vehicles are in stock?', 'The models and trims on our lot right now are in the table on this page.'], ['Can these be upfitted?', 'Cutaway chassis are built to be upfitted. Call us with what you need and we will tell you what fits.'], ['Do you offer fleet pricing?', 'Call ' . STLF_PHONE . ' and ask for a business quote.']]],
    ];
}
function stlf_model_items($inv, $m) {
    return array_values(array_filter($inv, fn($v) => !$v['used'] && $v['make'] === 'Ford' && preg_match($m['rx'], $v['model'])));
}
function stlf_money_range($items) {
    $p = array_map(fn($v) => $v['price'], $items); return [min($p), max($p)];
}
function stlf_model_page($key, $inv) {
    $all = stlf_models(); if (!isset($all[$key])) return null;
    $m = $all[$key]; $items = stlf_model_items($inv, $m); if (!$items) return null;
    usort($items, fn($a, $b) => $a['price'] <=> $b['price']);
    $n = count($items); $stale = stlf_is_stale();
    [$lo, $hi] = stlf_money_range($items);
    $price = $stale ? '' : ' Sale prices run from ' . stlf_m($lo) . ' to ' . stlf_m($hi) . '.';
    $trims = []; foreach ($items as $v) { $t = trim(preg_replace('/\s+(4WD|2WD|AWD|RWD|FWD|SuperCrew|SuperCab|Regular Cab|\d+(\.\d+)?[\'\x{2032}] Box).*/u', '', $v['trim'])) ?: $v['trim']; $trims[$v['model'] . ' ' . $t][] = $v; }
    ksort($trims);
    $rows = ''; foreach ($trims as $name => $g) { $gp = array_map(fn($x) => $x['price'], $g); $rows .= '<tr><td>' . stlf_e($name) . '</td><td>' . count($g) . ' in stock</td><td>' . ($stale ? 'Call for current price' : 'from ' . stlf_m(min($gp))) . '</td></tr>'; }
    $eng = array_values(array_unique(array_filter(array_map(fn($v) => trim($v['en']), $items)))); sort($eng);
    $drv = array_values(array_unique(array_filter(array_map(fn($v) => trim($v['dt']), $items)))); sort($drv);
    $engTxt = $eng ? 'The ' . $m['label'] . ' vehicles on our lot right now list these engines: ' . implode('; ', $eng) . '. Engine is shown on each vehicle page.' : 'The engine is shown on each vehicle page.';
    $drvTxt = $drv ? 'The ' . $m['label'] . ' vehicles on our lot show these drive types: ' . implode('; ', $drv) . '.' : 'Drive type is shown on each vehicle page.';
    $faq = []; foreach ($m['faq'] as $q) $faq[] = [$q[0], str_replace(['%ENGINES%', '%DRIVE%'], [$engTxt, $drvTxt], $q[1])];
    $faq[] = ['How do I buy a ' . $m['label'] . ' from St. Louis Ford?', 'Pick a vehicle above and send us a message, call ' . STLF_PHONE . ', or start a credit application. The vehicles are at 7466 S. Lindbergh Blvd., St. Louis, MO 63125.'];
    $faq[] = ['Can I trade in my current vehicle?', 'Yes. Use the trade page for a value, then we apply it toward the ' . $m['label'] . ' you choose.'];
    $cards = ''; foreach ($items as $v) $cards .= stlf_card($v);
    $h2s = ''; foreach ($faq as $q) $h2s .= '<h3 style="font-family:\'DM Sans\',sans-serif;text-transform:none;font-size:18px;margin:18px 0 4px">' . stlf_e($q[0]) . '</h3><p style="margin:0;color:#b4bac6;line-height:1.6">' . stlf_e($q[1]) . '</p>';
    $cross = ''; foreach ($m['cross'] as $k => $t) if (isset($all[$k]) && stlf_model_items($inv, $all[$k])) $cross .= '<a href="/new-ford-' . $k . '/" class="btn b" style="margin:4px 8px 4px 0;display:inline-flex">' . stlf_e($t) . '</a>';
    $path = 'new-ford-' . $key; $url = home_url('/' . $path . '/');
    $h1 = 'New ' . $m['full'] . ' for sale in St. Louis, MO';
    $title = 'New ' . $m['full'] . ' for Sale in St. Louis, MO | St. Louis Ford';
    $desc = $n . ' new ' . $m['full'] . ' vehicles at the Ford dealer in St. Louis, MO.' . ($stale ? '' : ' From ' . stlf_m($lo) . '.') . ' Call ' . STLF_PHONE . '.';
    $ld = [
        ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')], ['@type' => 'ListItem', 'position' => 2, 'name' => 'New Ford models', 'item' => home_url('/new-ford-models/')], ['@type' => 'ListItem', 'position' => 3, 'name' => $m['full'], 'item' => $url]]],
        ['@context' => 'https://schema.org', '@type' => 'CollectionPage', 'name' => $h1, 'url' => $url, 'about' => $m['full'], 'mainEntity' => ['@type' => 'ItemList', 'numberOfItems' => $n, 'itemListElement' => array_map(fn($v, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => home_url('/' . stlf_slug($v) . '/'), 'name' => $v['name']], array_slice($items, 0, 24), array_keys(array_slice($items, 0, 24)))]],
        ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($q) => ['@type' => 'Question', 'name' => $q[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q[1]]], $faq)],
    ];
    $ldh = ''; foreach ($ld as $o) $ldh .= '<script type="application/ld+json">' . wp_json_encode($o, JSON_UNESCAPED_SLASHES) . '</script>';
    $body = '<div class="mut">Home / <a href="/new-ford-models/" style="color:#c8ff2e">New Ford models</a> / ' . stlf_e($m['full']) . '</div><h1 style="font-size:46px;margin:8px 0 10px">' . stlf_e($h1) . '</h1>'
        . '<p style="max-width:820px;font-size:18px;line-height:1.6;color:#d6dae1">' . stlf_e('St. Louis Ford has ' . $n . ' new ' . $m['full'] . ' vehicles on the lot in south St. Louis County.' . $price) . ' ' . stlf_e($m['intro']) . '</p>'
        . '<div class="grid" style="margin-top:20px">' . $cards . '</div>'
        . '<h2 style="margin-top:36px">' . stlf_e($m['full']) . ' trims in stock</h2><table>' . $rows . '</table>'
        . '<h2 style="margin-top:36px">Who the ' . stlf_e($m['label']) . ' fits</h2><p style="max-width:820px;line-height:1.6;color:#b4bac6">' . stlf_e($m['who']) . ' We serve shoppers from Sunset Hills, Mehlville, Oakville, Arnold, Fenton and the rest of the St. Louis area.</p>'
        . '<h2 style="margin-top:36px">Questions about the ' . stlf_e($m['label']) . '</h2><div style="max-width:820px">' . $h2s . '</div>'
        . '<div class="box" style="margin-top:32px"><h2 style="margin:0">Next steps</h2><div><button type="button" class="btn k" data-lead="' . esc_attr('New ' . $m['full']) . '" style="display:inline-flex;margin:4px 8px 4px 0;border:0;cursor:pointer;font:inherit;font-weight:700">Ask about a ' . stlf_e($m['label']) . '</button><a class="btn y" style="display:inline-flex;margin:4px 8px 4px 0" href="/get-approved/">Get pre-approved</a><a class="btn b" style="display:inline-flex;margin:4px 8px 4px 0" href="/value-your-trade/">Value your trade</a><a class="btn b" style="display:inline-flex;margin:4px 8px 4px 0" href="' . STLF_TEL . '">Call ' . STLF_PHONE . '</a></div><div>' . $cross . '</div></div>';
    return stlf_head($title, $desc, '<link rel="canonical" href="' . esc_url($url) . '">' . $ldh) . stlf_hdr() . '<div class="wrap">' . $body . '</div>' . stlf_ft();
}
function stlf_models_hub($inv) {
    $stale = stlf_is_stale(); $tiles = '';
    foreach (stlf_models() as $k => $m) {
        $it = stlf_model_items($inv, $m); if (!$it) continue; usort($it, fn($a, $b) => $a['price'] <=> $b['price']);
        $ph = ''; foreach ($it as $v) if (!empty($v['ph'])) { $ph = '<img src="' . esc_url(stlf_img($v['ph'][0], 640)) . '" alt="' . esc_attr($v['name']) . '" loading="lazy">'; break; }
        $tiles .= '<a class="card" href="/new-ford-' . $k . '/">' . $ph . '<div class="cb"><div class="d" style="font-weight:700;font-size:24px">' . stlf_e($m['full']) . '</div><div class="mut">' . count($it) . ' in stock' . ($stale ? '' : ' &middot; from ' . stlf_m($it[0]['price'])) . '</div></div></a>';
    }
    return stlf_page('Shop New Ford Models in St. Louis, MO | St. Louis Ford', 'Shop new Ford trucks, SUVs, cars and vans by model at the Ford dealer in St. Louis, MO. Call ' . STLF_PHONE . '.', 'new-ford-models', 'Shop new Ford models', '<p class="mut" style="max-width:820px;font-size:18px">Pick a model to see every one on our lot, the trims in stock and answers to common questions.</p>' . (file_exists(plugin_dir_path(__FILE__) . 'models.jpg') ? '<img src="' . esc_url(plugins_url('models.jpg', __FILE__)) . '" alt="New Ford F-150, Maverick, Bronco and Explorer on display in a Ford showroom" width="1264" height="1264" style="width:100%;max-height:520px;object-fit:cover;object-position:center 60%;border-radius:16px;margin:0 0 22px;display:block" loading="eager">' : '') . '<div class="grid">' . $tiles . '</div>');
}
function stlf_specials($inv) {
    if (stlf_is_stale()) return stlf_page('New Ford Specials in St. Louis, MO | St. Louis Ford', 'Current savings on new Fords at St. Louis Ford.', 'specials', 'New Ford specials', '<p class="mut" style="font-size:18px">Our savings change daily. Call ' . STLF_PHONE . ' for today\'s numbers.</p>');
    $new = array_values(array_filter($inv, fn($v) => !$v['used'] && !empty($v['p']["Dave's Discount"])));
    usort($new, fn($a, $b) => $b['p']["Dave's Discount"] <=> $a['p']["Dave's Discount"]);
    $cnt = count($new); $max = $cnt ? $new[0]['p']["Dave's Discount"] : 0;
    $avg = $cnt ? round(array_sum(array_map(fn($v) => $v['p']["Dave's Discount"], $new)) / $cnt) : 0;
    $rows = ''; foreach (stlf_models() as $k => $m) { $it = array_values(array_filter(stlf_model_items($inv, $m), fn($v) => !empty($v['p']["Dave's Discount"]))); if (!$it) continue; $d = array_map(fn($v) => $v['p']["Dave's Discount"], $it); usort($it, fn($a, $b) => $a['price'] <=> $b['price']);
        $rows .= '<tr><td><a href="/new-ford-' . $k . '/" style="color:#c8ff2e">' . stlf_e($m['full']) . '</a></td><td>' . count($it) . '</td><td>' . stlf_m(max($d)) . '</td><td>' . stlf_m($it[0]['price']) . '</td></tr>'; }
    $cards = ''; foreach (array_slice($new, 0, 12) as $v) $cards .= stlf_card($v);
    $body = '<p style="max-width:820px;font-size:18px;line-height:1.6;color:#d6dae1">' . stlf_e('Right now ' . $cnt . ' new Fords on our lot carry a Discount off MSRP. The biggest is ' . stlf_m($max) . ' and the average is ' . stlf_m($avg) . '. These come straight from the vehicles in stock and change as cars sell.') . ' Ford rebates, special rates and programs such as Ford X Plan, military and first responder pricing may apply on top. Ask us what you qualify for.</p>'
        . '<h2 style="margin-top:28px">Savings by model</h2><table><tr><td><b>Model</b></td><td><b>Discounted units</b></td><td><b>Biggest discount</b></td><td><b>Lowest sale price</b></td></tr>' . $rows . '</table>'
        . '<h2 style="margin-top:28px">Biggest discounts on the lot</h2><div class="grid">' . $cards . '</div>'
        . '<p class="mut" style="margin-top:20px">Prices exclude tax, title, license and a dealer administrative fee shown on each vehicle. All vehicles subject to prior sale.</p>';
    return stlf_page('New Ford Specials and Discounts in St. Louis, MO | St. Louis Ford', $cnt . ' new Fords with discounts off MSRP at the Ford dealer in St. Louis, MO. Biggest savings ' . stlf_m($max) . '. Call ' . STLF_PHONE . '.', 'specials', 'New Ford specials', $body);
}
function stlf_hero($inv) {
    $new = array_values(array_filter($inv, fn($v) => !$v['used'])); $used = count($inv) - count($new); $stale = stlf_is_stale();
    $feat = null; $best = -1;
    foreach ($new as $v) { if (empty($v['ph'])) continue; $d = $v['p']["Dave's Discount"] ?? 0; if (preg_match('/^(F-150|Bronco|Maverick|Explorer)/', $v['model']) && $d > $best) { $best = $d; $feat = $v; } }
    $disc = []; foreach ($new as $v) if (!empty($v['p']["Dave's Discount"])) $disc[] = $v['p']["Dave's Discount"];
    $chips = '<span class="chip"><b>' . count($new) . '</b> new Fords</span><span class="chip"><b>' . $used . '</b> used vehicles</span>' . (!$stale && $disc ? '<span class="chip">Up to <b>' . stlf_m(max($disc)) . '</b> off MSRP</span>' : '');
    $img = $feat ? '<a href="/' . stlf_slug($feat) . '/" class="heroimg"><img src="' . esc_url(stlf_img($feat['ph'][0], 1200)) . '" alt="' . esc_attr($feat['name']) . '" fetchpriority="high"><div class="herotag"><div class="d" style="font-size:18px;font-weight:700">' . stlf_e($feat['name']) . '</div>' . (!$stale ? '<div style="display:flex;gap:14px;align-items:baseline"><span class="mut">Sale Price</span><span class="price" style="color:#c8ff2e">' . stlf_m($feat['price']) . '</span></div>' : '<div class="mut">Call for current price</div>') . '</div></a>' : '';
    $hf = plugin_dir_path(__FILE__) . 'hero.jpg'; $bg = file_exists($hf) ? esc_url(plugins_url('hero.jpg', __FILE__)) : '';
    $bgcss = '#07080a';
    $hbg = $bg ? '.hero:before{content:"";position:absolute;top:0;right:0;bottom:0;width:62%;background:url(' . $bg . ') center/cover no-repeat;-webkit-mask-image:linear-gradient(90deg,transparent 0,#000 38%),linear-gradient(0deg,transparent 0,#000 22%);-webkit-mask-composite:source-in;mask-image:linear-gradient(90deg,transparent 0,#000 38%),linear-gradient(0deg,transparent 0,#000 22%);mask-composite:intersect;z-index:0}.hero .in{position:relative;z-index:1}@media(max-width:720px){.hero:before{width:100%;height:240px;bottom:auto;-webkit-mask-image:linear-gradient(0deg,transparent 0,#000 45%);mask-image:linear-gradient(0deg,transparent 0,#000 45%)}}' : '';
    $badge = $feat && !$stale ? '<a href="/' . stlf_slug($feat) . '/" class="fbadge"><span class="mut">Featured &middot; ' . stlf_e($feat['year'] . ' ' . $feat['model']) . '</span><span class="d" style="font-size:22px;color:#c8ff2e;font-weight:700">' . stlf_m($feat['price']) . '</span></a>' : '';
    return '<style>' . $hbg . '.hero{position:relative;overflow:hidden;min-height:min(78vh,720px);display:flex;align-items:center;background:' . $bgcss . ';border-bottom:1px solid #2a2e37}.hero .in{width:100%;max-width:1280px;margin:0 auto;padding:56px max(16px,env(safe-area-inset-right,0px)) 56px max(16px,env(safe-area-inset-left,0px))}.hero .txt{max-width:640px}.eyebrow{color:#c8ff2e;font-weight:700;letter-spacing:.12em;text-transform:uppercase;font-size:13px}.hero h1{font-size:clamp(42px,7vw,88px);line-height:.96;margin:10px 0 16px}.hero h1 em{font-style:normal;color:#c8ff2e}.chip{display:inline-block;background:rgba(20,22,27,.85);border:1px solid #2a2e37;border-radius:999px;padding:8px 14px;margin:0 8px 8px 0;font-size:14px;color:#b4bac6}.chip b{color:#f4f5f7}.fbadge{display:inline-flex;gap:12px;align-items:baseline;margin-top:6px;padding:10px 16px;border:1px solid #2a2e37;border-radius:14px;background:rgba(7,8,10,.7)}@media(max-width:720px){.hero{min-height:0;align-items:flex-end;}.hero .in{padding-top:230px}}</style>'
        . '<section class="hero"><div class="in"><div class="txt"><div class="eyebrow">South County, St. Louis &middot; 7466 S. Lindbergh Blvd.</div><h1>Ford vehicles for sale in <em>St. Louis</em></h1><p style="font-size:19px;line-height:1.55;color:#c3c8d2;margin:0 0 20px">Real trucks and SUVs on the lot, with the Discount and Sale Price shown on every one. Get pre-approved, value your trade, or call and we will have it ready.</p><div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px"><a class="btn y" href="/new-ford-vehicles/">Shop new</a><a class="btn b" href="/used-vehicles/">Shop used</a><a class="btn b" href="/get-approved/">Get pre-approved</a></div><div>' . $chips . '</div>' . $badge . '</div></div></section>';
}
function stlf_home_tiles($inv) {
    $o = ''; foreach (stlf_models() as $k => $m) { $c = count(stlf_model_items($inv, $m)); if ($c) $o .= '<a class="btn b" style="display:inline-flex;margin:4px 8px 4px 0" href="/new-ford-' . $k . '/">' . stlf_e($m['label']) . ' <span class="mut" style="margin-left:8px">' . $c . '</span></a>'; }
    return '<h2 style="margin:8px 0 10px">Shop new by model</h2><div style="margin-bottom:28px">' . $o . '</div>';
}
function stlf_model_paths($inv) { $o = []; foreach (stlf_models() as $k => $m) if (stlf_model_items($inv, $m)) $o[] = 'new-ford-' . $k; return $o; }

add_action('template_redirect', function () {
    if (is_admin()) return;
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if (!in_array($path, ['new-ford-models', 'specials'], true) && !preg_match('#^new-ford-([a-z0-9-]+)$#', $path, $mm)) return;
    $inv = stlf_inv(); if (!$inv) return;
    $h = null;
    if ($path === 'new-ford-models') $h = stlf_models_hub($inv);
    elseif ($path === 'specials') $h = stlf_specials($inv);
    elseif (isset($mm[1]) && $mm[1] !== 'vehicles') $h = stlf_model_page($mm[1], $inv);
    if ($h) { status_header(200); header('Content-Type: text/html; charset=utf-8'); echo $h; exit; }
}, 6);
