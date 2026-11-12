<?php
if (!defined('ABSPATH')) exit;

/* Extra pages: finance, trade, contact, about, specials, privacy, plus lead form, robots, llms. */

function stlf_nav() {
    $l = ['/new-ford-vehicles/' => 'New', '/new-ford-models/' => 'Shop by model', '/specials/' => 'Specials', '/used-vehicles/' => 'Used', '/get-approved/' => 'Get pre-approved', '/value-your-trade/' => 'Value your trade', '/contact-us/' => 'Contact'];
    $o = ''; foreach ($l as $h => $t) $o .= '<a href="' . $h . '" style="padding:6px 10px;font-weight:700;font-size:15px">' . $t . '</a>';
    return '<div style="background:#0b0c0f;border-bottom:1px solid #2a2e37;display:flex;flex-wrap:wrap;gap:4px;padding:6px 12px">' . $o . '</div>';
}
function stlf_lead_form($source, $extra_fields = '', $btn = 'Send') {
    return '<form class="box" method="post" action="/stlf-lead/"><input type="hidden" name="source" value="' . esc_attr($source) . '"><div style="position:absolute;left:-9999px"><label>Leave blank<input name="website" tabindex="-1" autocomplete="off"></label></div>'
        . '<label>First name<input name="first" required autocomplete="given-name"></label><label>Last name<input name="last" required autocomplete="family-name"></label><label>Phone<input name="phone" type="tel" required autocomplete="tel"></label><label>Email<input name="email" type="email" required autocomplete="email"></label>'
        . $extra_fields . '<label>Message (optional)<input name="msg"></label><button class="btn y" style="border:0;cursor:pointer;font:inherit;font-weight:700">' . esc_html($btn) . '</button><span class="mut">By sending this you agree to be contacted about your request. We do not sell your information.</span></form>';
}
function stlf_page($title, $desc, $path, $h1, $body, $ld = '') {
    $extra = '<link rel="canonical" href="' . esc_url(home_url('/' . $path . '/')) . '">' . $ld;
    return stlf_head($title, $desc, $extra) . stlf_hdr() . '<div class="wrap"><h1 style="font-size:46px;margin:0 0 10px">' . esc_html($h1) . '</h1>' . $body . '</div>' . stlf_ft();
}
function stlf_embed_or($opt, $fallback) {
    $e = trim((string)get_option($opt, ''));
    return $e !== '' ? '<div class="box" style="background:#f4f6f8;color:#111;border-radius:14px;padding:14px">' . $e . '</div>' : $fallback;
}
function stlf_extra_page($path) {
    $ph = STLF_PHONE;
    switch ($path) {
        case 'get-approved':
            $body = '<p class="mut" style="max-width:760px;font-size:18px">Start your credit application online. It takes a few minutes, and a finance manager follows up with you. Prefer to talk it through? Call ' . $ph . '.</p>'
                . stlf_embed_or('stlf_credit_embed', '<div class="box" style="max-width:640px"><h2 style="margin:0">Tell us how to reach you</h2><p class="mut">The secure credit application is being connected. Leave your details and a finance manager will send you the application link.</p></div>' . '<div style="max-width:640px;margin-top:16px">' . stlf_lead_form('Get Pre-Approved', '', 'Request my application') . '</div>');
            return stlf_page('Get Pre-Approved for Auto Financing in St. Louis, MO | St. Louis Ford', 'Start a credit application for a new or used Ford in St. Louis, MO. A finance manager follows up with you. Call ' . $ph . '.', $path, 'Get pre-approved', $body);
        case 'value-your-trade':
            $body = '<p class="mut" style="max-width:760px;font-size:18px">Find out what your car is worth before you visit. Tell us about your vehicle and we will follow up with a real offer to apply toward your next one.</p>'
                . stlf_embed_or('stlf_trade_embed', '<div style="max-width:640px">' . stlf_lead_form('Value Your Trade', '<label>Year, make and model of your trade<input name="trade"></label><label>Approximate mileage<input name="miles" inputmode="numeric"></label>', 'Get my trade offer') . '</div>');
            return stlf_page('Value Your Trade in St. Louis, MO | St. Louis Ford', 'Get a trade in value for your car, truck or SUV in St. Louis, MO. Apply it toward a new or used Ford. Call ' . $ph . '.', $path, 'Value your trade', $body);
        case 'contact-us':
            $body = '<div class="cols"><div class="main"><div class="box"><h2 style="margin:0">Sales</h2><p style="margin:0;font-size:22px"><a href="' . STLF_TEL . '" style="color:#c8ff2e">' . $ph . '</a></p><p class="mut" style="margin:0">7466 S. Lindbergh Blvd., St. Louis, MO 63125</p><h3 style="margin:14px 0 4px">Sales hours</h3><p class="mut" style="margin:0">Monday, Wednesday, Friday: 9:00 am to 7:00 pm<br>Tuesday, Thursday: 9:00 am to 6:00 pm<br>Saturday: 9:00 am to 5:00 pm<br>Sunday: Closed</p></div></div><div class="side">' . stlf_lead_form('Contact Us', '', 'Send message') . '</div></div>';
            $ld = '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@type' => 'AutoDealer', 'name' => 'St. Louis Ford', 'url' => home_url('/'), 'telephone' => '+1' . preg_replace('/\D/', '', $ph), 'address' => ['@type' => 'PostalAddress', 'streetAddress' => '7466 S. Lindbergh Blvd.', 'addressLocality' => 'St. Louis', 'addressRegion' => 'MO', 'postalCode' => '63125', 'addressCountry' => 'US'], 'openingHoursSpecification' => [['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday', 'Wednesday', 'Friday'], 'opens' => '09:00', 'closes' => '19:00'], ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Tuesday', 'Thursday'], 'opens' => '09:00', 'closes' => '18:00'], ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => 'Saturday', 'opens' => '09:00', 'closes' => '17:00']]], JSON_UNESCAPED_SLASHES) . '</script>';
            return stlf_page('Contact St. Louis Ford | Ford Dealer in St. Louis, MO', 'Call or message St. Louis Ford. 7466 S. Lindbergh Blvd., St. Louis, MO 63125. Phone ' . $ph . '.', $path, 'Contact us', $body, $ld);
        case 'about-us':
            $body = '<div class="box" style="max-width:820px"><p style="margin:0;font-size:18px">St. Louis Ford is the online home for shopping new and used Ford vehicles in south St. Louis County. The cars on this site are on the lot at 7466 S. Lindbergh Blvd.</p><p class="mut" style="margin:0">Browse new and used vehicles, start a credit application, and get a value on your trade. When you are ready to talk, call ' . $ph . '.</p></div>';
            return stlf_page('About St. Louis Ford | Ford Dealer in St. Louis, MO', 'About St. Louis Ford and the Ford inventory you can shop here. Call ' . $ph . '.', $path, 'About St. Louis Ford', $body);
        case 'privacy-policy':
            $body = '<div class="box" style="max-width:820px"><p style="margin:0">When you send a form on this site we collect the details you enter, such as your name, phone number, email and message, and use them to respond to your request, including by phone, text and email. We do not sell your personal information. We use standard analytics to understand how the site is used. To ask about or remove your information, call ' . $ph . '.</p><p style="margin:0">We work with FordDirect to provide us with advertising and related services, and analytics. This includes, but is not limited to, the deployment of tracking technologies on our properties that collect and provide information about you to FordDirect or our provision of your name, email address, and other contact details to FordDirect, including for advertising and related services and analytics. To the extent we disclose information to FordDirect, FordDirect may use that information in accordance with its own privacy statement. For details on FordDirect\'s data practices, see: <a href="https://click.mail.forddirectdealer.com/?qs=a20bf837c9a4211f38a0536f6ee308bcd5aa782f0f67ebfca0772a490d307d869aab9dc90d7f0b57a7742d9e59d17c0a74cc2478b5ffc6c8" rel="noopener" style="color:#c8ff2e">FordDirect Privacy Statement</a>.</p></div>';
            return stlf_page('Privacy Policy | St. Louis Ford', 'How St. Louis Ford collects and uses information you send through this website.', $path, 'Privacy policy', $body);
    }
    return null;
}
function stlf_extra_paths() { return ['get-approved', 'value-your-trade', 'contact-us', 'about-us', 'privacy-policy']; }

function stlf_robots_body() {
    return "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\nDisallow: /stlf-lead/\n\nSitemap: " . home_url('/sitemap-inventory.xml') . "\n";
}
/* Flywheel serves its own default robots.txt ahead of WordPress. A real robots.txt in the web root overrides it
   (Flywheel support, 11/11/2026), so write one and keep it current. Status is stored in option stlf_robots_status. */
add_action('init', function () {
    $gate = 'stlf_robots_' . substr(md5(stlf_robots_body() . filemtime(__FILE__)), 0, 10);
    if (get_transient($gate)) return;
    set_transient($gate, 1, 6 * HOUR_IN_SECONDS);
    $want = stlf_robots_body();
    $paths = array_unique([dirname(WP_CONTENT_DIR) . '/robots.txt', '/www/robots.txt', ABSPATH . 'robots.txt']);
    $log = [];
    foreach ($paths as $f) {
        if (@file_get_contents($f) === $want) { $log[] = 'ok ' . $f; continue; }
        $ok = @file_put_contents($f, $want);
        $log[] = ($ok === false ? 'write failed ' : 'written ') . $f;
    }
    update_option('stlf_robots_status', implode(' | ', $log) . ' ' . gmdate('c'), false);
});
add_action('template_redirect', function () {
    if (is_admin()) return;
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($path === 'llms.txt' || $path === 'robots.txt') {
        status_header(200); header('Content-Type: text/plain; charset=utf-8');
        if ($path === 'robots.txt') { echo stlf_robots_body(); exit; }
        $inv = stlf_inv(); $n = count(array_filter($inv, fn($v) => !$v['used']));
        echo "# St. Louis Ford\n\n> Online home for new and used Ford inventory, 7466 S. Lindbergh Blvd., St. Louis, MO 63125. Phone " . STLF_PHONE . ".\n\n## Pages\n- " . home_url('/new-ford-vehicles/') . ": " . $n . " new Ford vehicles\n- " . home_url('/used-vehicles/') . ": used cars, trucks and SUVs\n- " . home_url('/new-ford-models/') . ": new Fords by model\n- " . home_url('/specials/') . ": discounts on new Fords\n- " . home_url('/get-approved/') . ": credit application\n- " . home_url('/value-your-trade/') . ": trade in value\n- " . home_url('/contact-us/') . ": contact\n\nPrices on vehicle pages come from a file refreshed daily. If the file is more than 30 hours old, prices are hidden and the page says to call.\n"; exit;
    }
    if ($path === 'stlf-lead' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ajax = !empty($_SERVER['HTTP_X_STLF_AJAX']);
        $ip = md5($_SERVER['REMOTE_ADDR'] ?? ''); $rk = 'stlf_rl_' . $ip; $cnt = (int)get_transient($rk);
        if (!empty($_POST['website']) || $cnt >= 5) { if ($ajax) { header('Content-Type: application/json'); echo '{"ok":true}'; exit; } wp_safe_redirect(home_url('/')); exit; }
        set_transient($rk, $cnt + 1, 600);
        $f = function ($k) { return sanitize_text_field(wp_unslash($_POST[$k] ?? '')); };
        $to = get_option('stlf_lead_email', '') ?: get_option('admin_email');
        $src = $f('source'); $vehicle = $f('vehicle');
        $adf = '<?xml version="1.0"?><?adf version="1.0"?><adf><prospect><requestdate>' . gmdate('c') . '</requestdate>' . ($vehicle ? '<vehicle interest="buy" status="used"><comments>' . esc_html($vehicle) . '</comments></vehicle>' : '') . '<customer><contact><name part="first">' . esc_html($f('first')) . '</name><name part="last">' . esc_html($f('last')) . '</name><email>' . esc_html($f('email')) . '</email><phone>' . esc_html($f('phone')) . '</phone></contact><comments>' . esc_html(trim($src . ' | ' . $f('msg') . ' | ' . $f('trade') . ' ' . $f('miles'))) . '</comments></customer><vendor><vendorname>St. Louis Ford</vendorname></vendor><provider><name>stlouisford.com</name></provider></prospect></adf>';
        wp_mail($to, 'Web lead: ' . $src . ' ' . $f('first') . ' ' . $f('last'), $adf, ['Content-Type: text/plain; charset=UTF-8']);
        if ($ajax) { header('Content-Type: application/json'); echo '{"ok":true}'; exit; }
        header('Content-Type: text/html; charset=utf-8');
        status_header(200); echo stlf_head('Thank you | St. Louis Ford', 'We received your request.', '<meta name="robots" content="noindex">') . stlf_hdr() . '<div class="wrap"><h1 style="font-size:46px">Thank you</h1><p class="mut" style="font-size:18px">We got your request and will be in touch. Need us sooner? Call ' . STLF_PHONE . '.</p><a class="btn y" style="max-width:260px" href="/new-ford-vehicles/">Keep shopping</a></div>' . stlf_ft(); exit;
    }
    if (in_array($path, stlf_extra_paths(), true)) {
        $h = stlf_extra_page($path); if ($h) { status_header(200); header('Content-Type: text/html; charset=utf-8'); echo $h; exit; }
    }
}, 5);

function stlf_modal() {
    return '<style>#stlf-m{border:0;padding:0;background:transparent;color:#f4f5f7;max-width:min(520px,94vw);width:100%}#stlf-m::backdrop{background:rgba(0,0,0,.72)}#stlf-m .sheet{background:#14161b;border:1px solid #2a2e37;border-radius:20px;padding:22px;position:relative;max-height:90vh;overflow:auto}#stlf-m .x{position:absolute;right:12px;top:10px;background:none;border:0;color:#9aa1ad;font-size:30px;line-height:1;cursor:pointer;min-width:44px;min-height:44px}#stlf-m .box{border:0;padding:0;background:none}@media(max-width:640px){#stlf-m{max-width:100vw;margin:auto 0 0 0}#stlf-m .sheet{border-radius:20px 20px 0 0;padding-bottom:max(22px,env(safe-area-inset-bottom))}}.mbar{display:none}@media(max-width:640px){.mbar{display:flex;gap:8px;position:fixed;left:0;right:0;bottom:0;z-index:20;padding:8px 12px max(8px,env(safe-area-inset-bottom));background:rgba(7,8,10,.96);border-top:1px solid #2a2e37}.mbar .btn{flex:1}body{padding-bottom:72px}}</style>'
    . '<dialog id="stlf-m"><div class="sheet"><button class="x" type="button" aria-label="Close" data-close>&times;</button><h2 style="margin:0 0 4px" id="stlf-t">Check availability</h2><p class="mut" id="stlf-v" style="margin:0 0 12px"></p><div id="stlf-f">' . stlf_lead_form('Pop up', '', 'Send') . '</div><div id="stlf-ok" style="display:none"><p style="font-size:18px">Thank you. We got your request and will be in touch soon.</p><p class="mut">Need us sooner? Call ' . STLF_PHONE . '.</p><button type="button" class="btn y" data-close style="border:0;cursor:pointer;font:inherit;font-weight:700;width:100%">Keep shopping</button></div></div></dialog>'
    . '<div class="mbar"><a class="btn b" href="' . STLF_TEL . '">Call</a><button type="button" class="btn y" data-lead="" style="border:0;cursor:pointer;font:inherit;font-weight:700">Message us</button></div>'
    . '<script>(function(){var d=document.getElementById("stlf-m");if(!d||!d.showModal)return;var f=d.querySelector("form");function v(x){var i=f.querySelector("[name=vehicle]");if(!i){i=document.createElement("input");i.type="hidden";i.name="vehicle";f.appendChild(i)}i.value=x||"";document.getElementById("stlf-v").textContent=x?x.replace(/\s[A-HJ-NPR-Z0-9]{17}$/,""):""}document.addEventListener("click",function(e){var o=e.target.closest("[data-lead]");if(o){e.preventDefault();v(o.getAttribute("data-lead"));document.getElementById("stlf-f").style.display="";document.getElementById("stlf-ok").style.display="none";d.showModal();return}if(e.target.closest("[data-close]")||e.target===d)d.close()});f.addEventListener("submit",function(e){e.preventDefault();var b=f.querySelector("button.btn");b.disabled=true;fetch("/stlf-lead/",{method:"POST",body:new FormData(f),headers:{"X-Stlf-Ajax":"1"},credentials:"same-origin"}).then(function(r){return r.json()}).then(function(){document.getElementById("stlf-f").style.display="none";document.getElementById("stlf-ok").style.display="";f.reset();b.disabled=false;if(window.dataLayer)dataLayer.push({event:"lead_submit",lead_source:"popup"})}).catch(function(){b.disabled=false;f.submit()})})})();</script>';
}
