<?php
if (!defined('ABSPATH')) exit;

/* Self update from a public GitHub repo. Repo holds version.json and a release asset stlf-inventory.zip. */

function stlf_repo() { return trim((string)get_option('stlf_repo', 'jdlebow/stlf-site')); }

function stlf_remote_version() {
    $repo = stlf_repo(); if (!$repo || !preg_match('#^[\w.-]+/[\w.-]+$#', $repo)) return null;
    $c = get_site_transient('stlf_remote_v'); if ($c !== false) return $c ?: null;
    $r = wp_remote_get('https://raw.githubusercontent.com/' . $repo . '/main/version.json', ['timeout' => 8]);
    $d = !is_wp_error($r) && wp_remote_retrieve_response_code($r) === 200 ? json_decode(wp_remote_retrieve_body($r), true) : null;
    if (!is_array($d) || empty($d['version'])) { set_site_transient('stlf_remote_v', '', 600); return null; }
    $d['zip'] = !empty($d['zip']) ? $d['zip'] : 'https://raw.githubusercontent.com/' . $repo . '/main/stlf-inventory.zip';
    set_site_transient('stlf_remote_v', $d, 3600); return $d;
}
add_filter('pre_set_site_transient_update_plugins', function ($t) {
    if (!is_object($t)) return $t;
    $d = stlf_remote_version(); if (!$d) return $t;
    $slug = plugin_basename(STLF_MAIN_FILE);
    $cur = get_file_data(STLF_MAIN_FILE, ['v' => 'Version'])['v'];
    if (version_compare($d['version'], $cur, '>')) {
        $t->response[$slug] = (object)['slug' => dirname($slug), 'plugin' => $slug, 'new_version' => $d['version'], 'package' => $d['zip'], 'url' => 'https://github.com/' . stlf_repo()];
    }
    return $t;
});
// Let WordPress auto update this plugin when the site owner turns that on.
add_filter('auto_update_plugin', function ($u, $item) { return (isset($item->plugin) && $item->plugin === plugin_basename(STLF_MAIN_FILE)) ? (bool)get_option('stlf_autoupdate', 1) : $u; }, 10, 2);
// GitHub release zips unpack to the plugin folder already; keep the folder name stable.
add_filter('upgrader_source_selection', function ($src, $remote, $up, $extra) {
    if (empty($extra['plugin']) || $extra['plugin'] !== plugin_basename(STLF_MAIN_FILE)) return $src;
    global $wp_filesystem; $want = trailingslashit($remote) . dirname(plugin_basename(STLF_MAIN_FILE)) . '/';
    if (basename(untrailingslashit($src)) === dirname(plugin_basename(STLF_MAIN_FILE))) return $src;
    if ($wp_filesystem && $wp_filesystem->move($src, $want)) return $want; return $src;
}, 10, 4);
