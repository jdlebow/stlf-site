<?php
/**
 * Plugin Name: St. Louis Ford Inventory
 * Description: Builds the new and used inventory pages, vehicle pages and schema from a daily inventory file. Hides prices when the file is stale.
 * Version: 0.5.10
 */
if (!defined('ABSPATH')) exit;

const STLF_PHONE = '(314) 248-0949';
const STLF_TEL = 'tel:3142480949';
const STLF_STALE_HOURS = 30;
const STLF_PER_PAGE = 24;
define('STLF_MAIN_FILE', __FILE__);
require_once __DIR__ . '/updater.php';
require_once __DIR__ . '/pages.php';
require_once __DIR__ . '/models.php';

function stlf_dir() { $u = wp_upload_dir(); $d = $u['basedir'] . '/stlf'; if (!is_dir($d)) wp_mkdir_p($d); return $d; }
function stlf_file() { return stlf_dir() . '/inventory.json'; }
function stlf_age_hours() { $f = stlf_file(); return file_exists($f) ? (time() - filemtime($f)) / 3600 : 9999; }
function stlf_is_stale() { return stlf_age_hours() > STLF_STALE_HOURS; }

function stlf_save_json($raw) {
    $d = json_decode($raw, true);
    $prev = 0; if (file_exists(stlf_file())) { $pd = json_decode(file_get_contents(stlf_file()), true); $prev = is_array($pd) ? count($pd) : 0; }
    $min = $prev ? (int)floor($prev * 0.6) : 20;
    if (!is_array($d) || count($d) < $min) return 'Rejected: file has ' . (is_array($d) ? count($d) : 0) . ' vehicles, need at least ' . $min . '. Old file kept.';
    file_put_contents(stlf_file(), $raw);
    wp_cache_delete('stlf_inv', 'stlf');
    return 'Saved ' . count($d) . ' vehicles.';
}
function stlf_fetch_now() {
    $url = get_option('stlf_source_url');
    if (!$url) return 'No source URL set.';
    $r = wp_remote_get($url, ['timeout' => 60]);
    if (is_wp_error($r)) return 'Fetch failed: ' . $r->get_error_message();
    if (wp_remote_retrieve_response_code($r) !== 200) return 'Fetch failed: HTTP ' . wp_remote_retrieve_response_code($r);
    return stlf_save_json(wp_remote_retrieve_body($r));
}
add_action('stlf_daily', 'stlf_fetch_now');
register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('stlf_daily')) wp_schedule_event(time() + 600, 'twicedaily', 'stlf_daily');
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, function () { wp_clear_scheduled_hook('stlf_daily'); });

add_action('admin_menu', function () {
    add_options_page('St. Louis Ford Inventory', 'St. Louis Ford Inventory', 'manage_options', 'stlf', 'stlf_admin');
});
function stlf_admin() {
    if (!current_user_can('manage_options')) return;
    $msg = '';
    if (!empty($_POST['stlf_nonce']) && wp_verify_nonce($_POST['stlf_nonce'], 'stlf')) {
        update_option('stlf_source_url', esc_url_raw($_POST['source_url'] ?? ''));
        update_option('stlf_home', empty($_POST['home']) ? 0 : 1);
        if (isset($_POST['lead_email'])) { update_option('stlf_repo', sanitize_text_field($_POST['repo'] ?? '')); update_option('stlf_autoupdate', empty($_POST['autoupdate']) ? 0 : 1); delete_site_transient('stlf_remote_v'); update_option('stlf_lead_email', sanitize_email($_POST['lead_email'])); update_option('stlf_credit_embed', wp_unslash($_POST['credit_embed'] ?? '')); update_option('stlf_trade_embed', wp_unslash($_POST['trade_embed'] ?? '')); }
        if (!empty($_POST['fetch'])) $msg = stlf_fetch_now();
        if (!empty($_FILES['upload']['tmp_name'])) $msg = stlf_save_json(file_get_contents($_FILES['upload']['tmp_name']));
        if ($msg === '') $msg = 'Settings saved.';
    }
    $n = 0; if (file_exists(stlf_file())) { $d = json_decode(file_get_contents(stlf_file()), true); $n = is_array($d) ? count($d) : 0; }
    echo '<div class="wrap"><h1>St. Louis Ford Inventory</h1>';
    if ($msg) echo '<div class="notice notice-info"><p>' . esc_html($msg) . '</p></div>';
    $ipr = wp_remote_get('https://api.ipify.org', ['timeout' => 10]);
    echo '<p>Server outbound IP: <b>' . esc_html(is_wp_error($ipr) ? 'unknown' : trim(wp_remote_retrieve_body($ipr))) . '</b></p>';
    echo '<p>Vehicles loaded: <b>' . intval($n) . '</b>. File age: <b>' . round(stlf_age_hours(), 1) . ' hours</b>. ' . (stlf_is_stale() ? '<b style="color:#b00">STALE: prices are hidden on the site.</b>' : '<b style="color:#080">Fresh: prices showing.</b>') . '</p>';
    echo '<form method="post" enctype="multipart/form-data">';
    wp_nonce_field('stlf', 'stlf_nonce');
    echo '<table class="form-table"><tr><th>Inventory file web address</th><td><input class="regular-text" name="source_url" value="' . esc_attr(get_option('stlf_source_url', '')) . '"><p class="description">Fetched twice a day. A file with under 60 percent of the current vehicle count is rejected and the old one stays.</p></td></tr>';
    echo '<tr><th>Lead email address</th><td><input class="regular-text" name="lead_email" value="' . esc_attr(get_option('stlf_lead_email', '')) . '"><p class="description">Form leads go here as an ADF message. Blank uses the site admin email.</p></td></tr><tr><th>Credit application embed</th><td><textarea name="credit_embed" rows="4" class="large-text code">' . esc_textarea(get_option('stlf_credit_embed', '')) . '</textarea><p class="description">Paste the script or iframe code. Blank shows a simple request form.</p></td></tr><tr><th>Trade tool embed (ClearCar)</th><td><textarea name="trade_embed" rows="4" class="large-text code">' . esc_textarea(get_option('stlf_trade_embed', '')) . '</textarea></td></tr><tr><th>Update source</th><td><input class="regular-text" name="repo" value="' . esc_attr(get_option('stlf_repo', 'jdlebow/stlf-site')) . '" placeholder="owner/repo"><label style="display:block;margin-top:6px"><input type="checkbox" name="autoupdate" value="1" ' . checked(get_option('stlf_autoupdate', 1), 1, false) . '> Install new versions automatically</label><p class="description">GitHub repo that holds version.json and the release zip.</p></td></tr><tr><th>Upload a file now</th><td><input type="file" name="upload"></td></tr>';
    echo '<tr><th>Use plugin home page</th><td><label><input type="checkbox" name="home" value="1" ' . checked(get_option('stlf_home', 1), 1, false) . '> Show the inventory home page at /</label></td></tr></table>';
    echo '<p><button class="button button-primary">Save</button> <button class="button" name="fetch" value="1">Save and fetch now</button></p></form></div>';
}

function stlf_inv() {
    $c = wp_cache_get('stlf_inv', 'stlf'); if ($c !== false) return $c;
    $out = [];
    if (file_exists(stlf_file())) {
        $d = json_decode(file_get_contents(stlf_file()), true);
        foreach ((array)$d as $x) { $v = stlf_conv($x); if ($v) $out[] = $v; }
    }
    wp_cache_set('stlf_inv', $out, 'stlf'); return $out;
}
function stlf_conv($x) {
    $p = [];
    foreach ((array)($x['p'] ?? []) as $r) $p[trim($r[0])] = $r[1];
    if (!isset($p['Sale Price']) || !isset($p['Admin. Fee'])) return null;
    $used = !empty($x['u']);
    return [
        'vin' => $x['v'], 'stock' => $x['s'], 'year' => $x['y'], 'make' => $x['mk'], 'model' => $x['md'], 'trim' => $x['tr'],
        'name' => str_replace('Lincoln Lincoln', 'Lincoln', $x['n']),
        // the source labels are swapped: e holds the interior, i holds the exterior
        'ext' => trim($x['i'] ?? '') ?: 'Not listed', 'int' => trim($x['e'] ?? '') ?: 'Not listed',
        'used' => $used, 'mi' => intval($x['mi'] ?? 0), 'p' => $p, 'price' => $p['Sale Price'],
        'dt' => $x['dt'] ?? '', 'tm' => $x['tm'] ?? '', 'en' => $x['en'] ?? '', 'fu' => $x['fu'] ?? '',
        'mc' => $x['mc'] ?? '', 'mh' => $x['mh'] ?? '', 'ty' => $x['ty'] ?? '', 'ph' => $x['ph'] ?? [],
    ];
}
function stlf_slug($v) {
    $s = strtolower($v['year'] . '-' . $v['make'] . '-' . $v['model'] . '-' . ($v['trim'] ?? ''));
    $s = str_replace([' ', "'", '®', '/', '.'], ['-', '', '', '-', ''], $s);
    $s = preg_replace('/-+/', '-', $s);
    return trim($s, '-') . '-' . strtolower($v['vin']);
}
function stlf_img($id, $w = 1200) { return 'https://media-cdn-a5-jazel-tango.jazel-qa.com/media/' . $id . '?scale=' . $w . '%2F-1%2F80'; }
function stlf_m($n) { return '$' . number_format($n); }
function stlf_e($s) { return esc_html((string)$s); }
function stlf_price($v, $big = false) { return stlf_is_stale() ? 'Call for current price' : stlf_m($v['price']); }

function stlf_css() { return <<<'CSS'
*{box-sizing:border-box}body{margin:0;font-family:'DM Sans',system-ui,sans-serif;color:#f4f5f7;background:#0b0c0f}a{color:inherit;text-decoration:none}h1,h2,.d{font-family:'Oswald',sans-serif;text-transform:uppercase}
.hd{background:#07080a;display:flex;align-items:center;justify-content:space-between;padding:16px max(16px,env(safe-area-inset-right,0px)) 16px max(16px,env(safe-area-inset-left,0px));border-bottom:1px solid #2a2e37;flex-wrap:wrap;gap:12px}.logo{color:#f4f5f7;font-family:'Oswald',sans-serif;font-weight:700;font-size:26px}
.btn{display:flex;align-items:center;justify-content:center;min-height:46px;border-radius:999px;font-weight:700;padding:0 22px}.y{background:#c8ff2e;color:#0b0c0f}.b{background:#1c1f26;color:#f4f5f7;border:1px solid #2a2e37}.k{background:#c8ff2e;color:#0b0c0f}
.wrap{padding:24px max(16px,env(safe-area-inset-right,0px)) 24px max(16px,env(safe-area-inset-left,0px))}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:20px}.card{background:#14161b;border:1px solid #2a2e37;border-radius:16px;overflow:hidden;display:flex;flex-direction:column}.card img{width:100%;height:210px;object-fit:cover;background:#1c1f26}
.cb{padding:16px;display:flex;flex-direction:column;gap:8px}.tag{display:inline-block;color:#0b0c0f;font-size:12px;font-weight:700;padding:4px 10px;border-radius:999px}
.row{display:flex;justify-content:space-between;gap:10px}.g{color:#b6ff3c;font-weight:700}.mut{color:#9aa1ad;font-size:14px}.price{font-family:'Oswald',sans-serif;font-weight:700;font-size:32px}
.cols{display:flex;gap:32px;flex-wrap:wrap;align-items:flex-start}.main{flex:1 1 620px;min-width:0}.side{flex:0 0 360px;max-width:100%;display:flex;flex-direction:column;gap:16px}
.main img.hero{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:16px;background:#1c1f26}.th{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:8px}.th img{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px}
.phs{background:#1c1f26;color:#9aa1ad;display:flex;align-items:center;justify-content:center;text-align:center;font-size:13px;padding:10px;border-radius:8px}.cph{height:210px;border-radius:0}.hero.phs{aspect-ratio:4/3;border-radius:16px;font-size:15px}.th .phs{aspect-ratio:4/3}
table{width:100%;border-collapse:collapse}td{padding:12px 14px;border-bottom:1px solid #2a2e37}td:first-child{color:#9aa1ad;width:40%}
.box{background:#14161b;border:1px solid #2a2e37;border-radius:16px;padding:22px;display:flex;flex-direction:column;gap:12px}input{border:1px solid #2a2e37;background:#0b0c0f;color:#f4f5f7;border-radius:8px;min-height:44px;padding:0 12px;font:inherit;width:100%}label{font-size:14px;font-weight:700}
.ft{background:#07080a;color:#b4bac6;padding:24px max(16px,env(safe-area-inset-right,0px)) 24px max(16px,env(safe-area-inset-left,0px));font-size:13px;line-height:1.5}
CSS;
}
const STLF_GTM = 'GTM-PWFDHPJ';
// Tracking and share tags load only on the real domain, never on the Flywheel staging address.
function stlf_live_host() { $h = strtolower($_SERVER['HTTP_HOST'] ?? ''); return (bool)preg_match('/(^|\.)stlouisford\.com$/', $h); }
function stlf_gtm_head() {
    if (!stlf_live_host()) return '';
    return "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . STLF_GTM . "');</script>";
}
function stlf_gtm_body() {
    if (!stlf_live_host()) return '';
    return '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . STLF_GTM . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
}
function stlf_share_tags($title, $desc, $extra) {
    $o = '';
    $path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    $url = home_url($path ?: '/');
    if (stripos($extra, 'rel="canonical"') === false && stripos($extra, 'noindex') === false && http_response_code() !== 404) $o .= '<link rel="canonical" href="' . esc_url($url) . '">';
    if (stripos($extra, 'noindex') !== false) return $o;
    $o .= '<meta property="og:type" content="website"><meta property="og:site_name" content="St. Louis Ford"><meta property="og:title" content="' . esc_attr($title) . '"><meta property="og:description" content="' . esc_attr($desc) . '"><meta property="og:url" content="' . esc_url($url) . '">';
    $hasImg = stripos($extra, 'og:image') !== false;
    $img = plugins_url('og-hero.jpg', STLF_MAIN_FILE);
    if (!$hasImg) $o .= '<meta property="og:image" content="' . esc_url($img) . '"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><meta property="og:image:alt" content="New Ford trucks and SUVs in the St. Louis Ford showroom">';
    $o .= '<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="' . esc_attr($title) . '"><meta name="twitter:description" content="' . esc_attr($desc) . '">';
    if (!$hasImg) $o .= '<meta name="twitter:image" content="' . esc_url($img) . '">';
    return $o;
}
function stlf_head($title, $desc, $extra = '') {
    return '<!doctype html><html lang="en"><head><meta charset="utf-8">' . stlf_gtm_head() . '<meta name="viewport" content="width=device-width,initial-scale=1"><title>' . stlf_e($title) . '</title><meta name="description" content="' . esc_attr($desc) . '">' . $extra . stlf_share_tags($title, $desc, $extra) . '<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet"><style>' . stlf_css() . '</style></head><body>' . stlf_gtm_body();
}
function stlf_hdr() {
    return '<div class="hd"><a class="logo" href="/"><img src="' . esc_url(plugins_url('logo.png', __FILE__)) . '" alt="St. Louis Ford" width="120" height="57" style="display:block"></a><a class="btn b" href="' . STLF_TEL . '">Call ' . STLF_PHONE . '</a></div>' . (function_exists('stlf_nav') ? stlf_nav() : '');
}
function stlf_ft() {
    return '<div class="ft">Vehicle listings on St. Louis Ford show inventory at 7466 S. Lindbergh Blvd., St. Louis, MO 63125. Prices exclude tax, title and license. Call ' . STLF_PHONE . '.</div>' . (function_exists('stlf_modal') ? stlf_modal() : '') . '</body></html>';
}
function stlf_photo($v, $i, $hero, $w) {
    if (empty($v['ph'])) return '<div class="' . ($hero ? 'hero' : 'cph') . ' phs">Photos coming soon</div>';
    $id = $v['ph'][min($i, count($v['ph']) - 1)];
    return '<img class="' . ($hero ? 'hero' : 'cph') . '" src="' . esc_url(stlf_img($id, $w)) . '" alt="' . esc_attr($v['name']) . '" loading="' . ($hero ? 'eager' : 'lazy') . '" referrerpolicy="no-referrer" style="width:100%;' . ($hero ? '' : 'height:210px;') . 'object-fit:cover;display:block">';
}
function stlf_card($v) {
    $t = $v['used'] ? '<span class="tag" style="background:#f4f5f7">Used</span>' : '<span class="tag" style="background:#c8ff2e">New</span>';
    $st = '';
    if (!$v['used'] && !stlf_is_stale()) {
        $st = '<div class="row mut"><span>MSRP</span><span style="text-decoration:line-through">' . stlf_m($v['p']['MSRP']) . '</span></div>';
        if (!empty($v['p']["Dave's Discount"])) $st .= '<div class="row g"><span>Discount</span><span>-' . stlf_m($v['p']["Dave's Discount"]) . '</span></div>';
    }
    $mi = $v['used'] ? ' &middot; ' . number_format($v['mi']) . ' miles' : '';
    return '<a class="card" href="/' . stlf_slug($v) . '/">' . stlf_photo($v, 0, false, 640) . '<div class="cb">' . $t . '<div class="d" style="font-weight:700;font-size:20px;line-height:1.15">' . stlf_e($v['name']) . '</div><div class="mut">' . stlf_e($v['ext']) . $mi . '</div>' . $st . '<div class="row" style="align-items:baseline"><b>Sale Price</b><span class="price">' . stlf_e(stlf_price($v)) . '</span></div></div></a>';
}
function stlf_vdp($v) {
    $stale = stlf_is_stale();
    $cond = $v['used'] ? 'Used' : 'New'; $p = $v['p'];
    $disc = $p["Dave's Discount"] ?? 0;
    $mpg = ($v['mc'] && $v['mc'] !== '0') ? ' It is rated ' . $v['mc'] . ' mpg city and ' . $v['mh'] . ' mpg highway.' : '';
    $mil = $v['used'] ? ' It has ' . number_format($v['mi']) . ' miles.' : '';
    $ov = 'This ' . strtolower($cond) . ' ' . $v['name'] . ' is a ' . strtolower($v['dt']) . ' ' . strtolower($v['ty'] ?: 'vehicle') . ' with a ' . $v['en'] . ' and an ' . strtolower($v['tm']) . ' transmission. It is finished in ' . $v['ext'] . ' with a ' . $v['int'] . ' interior.' . $mil . $mpg;
    $stack = '';
    if ($stale) {
        $box = '<div class="price" style="font-size:30px">Call for current price</div><div class="mut">Our prices update every day. Call ' . STLF_PHONE . ' for today\'s price on this vehicle.</div>';
    } else {
        if (!$v['used']) {
            $stack .= '<div class="row"><span>MSRP</span><span style="text-decoration:line-through">' . stlf_m($p['MSRP']) . '</span></div>';
            if ($disc) $stack .= '<div class="row g"><span>Discount</span><span>-' . stlf_m($disc) . '</span></div>';
            $stack .= '<div class="row"><span>Admin. Fee</span><span>+' . stlf_m($p['Admin. Fee']) . '</span></div>';
        } else {
            $stack .= '<div class="row"><span>Price</span><span>' . stlf_m($p['Price'] ?? ($p['Sale Price'] - $p['Admin. Fee'])) . '</span></div><div class="row"><span>Admin. Fee</span><span>+' . stlf_m($p['Admin. Fee']) . '</span></div>';
        }
        $box = '<div style="display:flex;flex-direction:column;gap:6px">' . $stack . '</div><div class="row" style="border-top:2px solid #c8ff2e;padding-top:12px;align-items:baseline"><b>Sale Price</b><span class="price" style="font-size:44px">' . stlf_m($v['price']) . '</span></div><div class="mut">Price includes the admin fee. Tax, title and license extra.</div>';
    }
    $title = $v['name'] . ' for Sale in St. Louis, MO | St. Louis Ford';
    $desc = $v['name'] . ', ' . $v['ext'] . ($stale ? '' : ', ' . stlf_m($v['price'])) . '. ' . $cond . ' Ford dealer inventory in St. Louis, MO. Call ' . STLF_PHONE . ' or check availability online.';
    $schema = ['@context' => 'https://schema.org', '@type' => 'Vehicle', 'name' => $v['name'], 'vehicleIdentificationNumber' => $v['vin'], 'sku' => $v['stock'], 'brand' => ['@type' => 'Brand', 'name' => $v['make']], 'model' => $v['model'], 'vehicleModelDate' => $v['year'], 'color' => $v['ext'], 'vehicleInteriorColor' => $v['int'], 'vehicleEngine' => ['@type' => 'EngineSpecification', 'name' => $v['en']], 'driveWheelConfiguration' => $v['dt'], 'vehicleTransmission' => $v['tm'], 'fuelType' => $v['fu'], 'itemCondition' => $v['used'] ? 'https://schema.org/UsedCondition' : 'https://schema.org/NewCondition', 'image' => array_map('stlf_img', array_slice($v['ph'], 0, 4))];
    if (!$stale) $schema['offers'] = ['@type' => 'Offer', 'price' => (string)$v['price'], 'priceCurrency' => 'USD', 'availability' => 'https://schema.org/InStock', 'seller' => ['@type' => 'AutoDealer', 'name' => 'St. Louis Ford', 'telephone' => '+1-314-248-0949', 'address' => ['@type' => 'PostalAddress', 'streetAddress' => '7466 S. Lindbergh Blvd.', 'addressLocality' => 'St. Louis', 'addressRegion' => 'MO', 'postalCode' => '63125']]];
    if ($v['used']) $schema['mileageFromOdometer'] = ['@type' => 'QuantitativeValue', 'value' => $v['mi'], 'unitCode' => 'SMI'];
    $rows = [['Engine', $v['en']], ['Drivetrain', $v['dt']], ['Transmission', $v['tm']], ['Fuel', $v['fu']], ['Exterior', $v['ext']], ['Interior', $v['int']], ['Stock', $v['stock']], ['VIN', $v['vin']]];
    if ($v['used']) array_unshift($rows, ['Mileage', number_format($v['mi']) . ' miles']);
    if ($v['mc'] && $v['mc'] !== '0') array_splice($rows, 4, 0, [['Fuel economy', $v['mc'] . ' city / ' . $v['mh'] . ' hwy']]);
    $tb = ''; foreach ($rows as $r) $tb .= '<tr><td>' . stlf_e($r[0]) . '</td><td><b>' . stlf_e($r[1]) . '</b></td></tr>';
    $th = ''; foreach (array_slice($v['ph'], 1, 5) as $k => $id) $th .= '<img src="' . esc_url(stlf_img($id, 320)) . '" alt="' . esc_attr($v['name'] . ' photo ' . ($k + 2)) . '" loading="lazy" referrerpolicy="no-referrer">';
    $ans = $stale ? 'Call ' . STLF_PHONE . ' for today\'s price.' : ($v['used'] ? 'The Sale Price is ' . stlf_m($v['price']) . ', which includes a ' . stlf_m($p['Admin. Fee']) . ' admin fee.' : 'MSRP ' . stlf_m($p['MSRP']) . ($disc ? ', less a Discount of ' . stlf_m($disc) : '') . ', plus a ' . stlf_m($p['Admin. Fee']) . ' admin fee, for a Sale Price of ' . stlf_m($v['price']) . '.');
    $faq = '<h3>Is this ' . stlf_e($v['model']) . ' available now?</h3><p>It is listed at the Ford dealer in St. Louis. Call ' . STLF_PHONE . ' or send a message to confirm it is still available.</p><h3>What is the price?</h3><p>' . stlf_e($ans) . '</p><h3>Can I value my trade?</h3><p>Yes. A trade value tool is coming to this page.</p>';
    $deep = 'https://www.davesinclairford.com/vehicle/' . rawurlencode($v['vin']) . '/';
    $body = stlf_hdr() . '<div class="wrap"><div class="mut">Home / ' . $cond . ' Ford Vehicles / ' . stlf_e($v['model']) . '</div><div class="cols" style="margin-top:12px"><div class="main"><h1 style="font-size:38px;line-height:1.05;margin:0 0 14px">' . stlf_e($v['name']) . '</h1>' . stlf_photo($v, 0, true, 1200) . '<div class="th">' . $th . '</div><div class="box">' . $box . '<a class="btn y" href="' . STLF_TEL . '">Call ' . STLF_PHONE . '</a><a class="btn b" href="/get-approved/">Get Pre-Approved</a></div><button type="button" class="btn k" data-lead="' . esc_attr($v['name'] . ' ' . $v['vin']) . '" style="border:0;cursor:pointer;font:inherit;font-weight:700;width:100%">Check availability</button><h2>Key specs</h2><table>' . $tb . '</table><h2>About this ' . stlf_e($v['make'] . ' ' . $v['model']) . '</h2><p style="font-size:17px;line-height:1.6;color:#b4bac6">' . stlf_e($ov) . '</p><p><a href="' . esc_url($deep) . '" rel="noopener" style="color:#c8ff2e;font-weight:700;text-decoration:underline">See full equipment and current offers for this vehicle</a></p><h2>Questions</h2>' . $faq . '</div><div class="side"><div class="box"><div class="d" style="font-weight:700;font-size:22px">Check availability</div><p class="mut">Lead form goes here once the CRM email is set.</p><a class="btn y" href="' . STLF_TEL . '">Call ' . STLF_PHONE . '</a></div></div></div></div>' . stlf_ft();
    $ogi = !empty($v['ph']) ? '<meta property="og:image" content="' . esc_url(stlf_img($v['ph'][0], 1200)) . '"><meta name="twitter:image" content="' . esc_url(stlf_img($v['ph'][0], 1200)) . '">' : '';
    return stlf_head($title, $desc, '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script><link rel="canonical" href="' . esc_url(home_url('/' . stlf_slug($v) . '/')) . '">' . $ogi) . $body;
}
function stlf_srp($items, $used, $pg) {
    $pages = max(1, (int)ceil(count($items) / STLF_PER_PAGE));
    if ($pg < 1 || $pg > $pages) return null;
    $base = $used ? '/used-vehicles/' : '/new-ford-vehicles/';
    $chunk = array_slice($items, ($pg - 1) * STLF_PER_PAGE, STLF_PER_PAGE);
    $nav = ''; for ($i = 1; $i <= $pages; $i++) $nav .= '<a class="btn ' . ($i == $pg ? 'y' : 'b') . '" style="min-width:46px" href="' . ($i == 1 ? $base : $base . 'page/' . $i . '/') . '">' . $i . '</a>';
    $h1 = $used ? 'Used Vehicles For Sale in St. Louis, MO' : 'New Ford Vehicles For Sale in St. Louis, MO';
    $desc = count($items) . ($used ? ' used vehicles on the lot at the Ford dealer in St. Louis.' : ' new Ford trucks, SUVs and Mustang vehicles from the Ford dealer in St. Louis.') . ' Call ' . STLF_PHONE . ' or check availability online.';
    $title = ($used ? 'Used Cars, Trucks and SUVs for Sale in St. Louis, MO' : 'New Ford Vehicles for Sale in St. Louis, MO') . ($pg > 1 ? ' (Page ' . $pg . ')' : '') . ' | St. Louis Ford';
    $cards = ''; foreach ($chunk as $v) $cards .= stlf_card($v);
    return stlf_head($title, $desc, '<link rel="canonical" href="' . esc_url(home_url($pg == 1 ? $base : $base . 'page/' . $pg . '/')) . '">') . stlf_hdr() . '<div class="wrap"><h1 style="font-size:46px;margin:0 0 6px">' . $h1 . '</h1><p class="mut" style="max-width:820px">' . stlf_e($desc) . '</p><div class="grid">' . $cards . '</div><div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:24px">' . $nav . '</div></div>' . stlf_ft();
}
function stlf_home($inv) {
    $new = array_values(array_filter($inv, fn($v) => !$v['used'])); $used = count($inv) - count($new);
    usort($new, fn($a, $b) => $a['price'] <=> $b['price']);
    $cards = ''; foreach (array_slice($new, 0, 12) as $v) $cards .= stlf_card($v);
    $org = ['@context' => 'https://schema.org', '@type' => 'AutoDealer', 'name' => 'St. Louis Ford', 'url' => home_url('/'), 'telephone' => '+1-314-248-0949', 'image' => plugins_url('og-hero.jpg', STLF_MAIN_FILE), 'logo' => plugins_url('logo.png', STLF_MAIN_FILE), 'address' => ['@type' => 'PostalAddress', 'streetAddress' => '7466 S. Lindbergh Blvd.', 'addressLocality' => 'St. Louis', 'addressRegion' => 'MO', 'postalCode' => '63125', 'addressCountry' => 'US']];
    return stlf_head('Ford Vehicles for Sale in St. Louis, MO | St. Louis Ford', 'Browse new and used Ford vehicles for sale in St. Louis, MO. Call ' . STLF_PHONE . '.', '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_SLASHES) . '</script>') . stlf_hdr() . stlf_hero($inv) . '<div class="wrap">' . stlf_home_tiles($inv) . '<h2 style="margin:0 0 12px">Lowest priced new Fords</h2><div class="grid">' . $cards . '</div></div>' . stlf_ft();
}

add_action('template_redirect', function () {
    if (is_admin()) return;
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($path === '' && !get_option('stlf_home', 1)) return;
    if ($path === 'sitemap-inventory.xml') {
        status_header(200); $inv = stlf_inv(); header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>' . esc_url(home_url('/')) . '</loc></url><url><loc>' . esc_url(home_url('/new-ford-vehicles/')) . '</loc></url><url><loc>' . esc_url(home_url('/used-vehicles/')) . '</loc></url>'; foreach (array_merge(['new-ford-models', 'specials'], stlf_model_paths($inv), stlf_extra_paths()) as $xp) if ($xp !== 'privacy-policy') echo '<url><loc>' . esc_url(home_url('/' . $xp . '/')) . '</loc></url>';
        foreach ($inv as $v) echo '<url><loc>' . esc_url(home_url('/' . stlf_slug($v) . '/')) . '</loc></url>';
        echo '</urlset>'; exit;
    }
    $inv = stlf_inv(); if (!$inv) return;
    $html = null;
    if ($path === '') { $html = stlf_home($inv); }
    elseif (preg_match('#^(new-ford-vehicles|used-vehicles)(?:/page/(\d+))?$#', $path, $m)) {
        $used = $m[1] === 'used-vehicles';
        $items = array_values(array_filter($inv, fn($v) => $v['used'] === $used));
        usort($items, fn($a, $b) => $a['price'] <=> $b['price']);
        $html = stlf_srp($items, $used, isset($m[2]) ? intval($m[2]) : 1);
    } elseif (preg_match('#^[a-z0-9-]+-([a-z0-9]{17})$#', $path, $m)) {
        foreach ($inv as $v) if (stlf_slug($v) === $path) { $html = stlf_vdp($v); break; }
        if ($html === null) { status_header(404); $html = stlf_head('This vehicle is no longer available | St. Louis Ford', 'This vehicle has been sold or removed.') . stlf_hdr() . '<div class="wrap"><h1>This vehicle is no longer available</h1><p class="mut">It may have sold. <a href="/new-ford-vehicles/" style="color:#c8ff2e">See new Ford vehicles</a> or <a href="/used-vehicles/" style="color:#c8ff2e">used vehicles</a>, or call ' . STLF_PHONE . '.</p></div>' . stlf_ft(); }
    }
    if ($html === null) return;
    status_header(200); header('Content-Type: text/html; charset=utf-8'); echo $html; exit;
});
