<?php
/** Plugin Name: Sphere Security */
if (!defined('ABSPATH')) { exit; }
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', function ($methods) {
    unset($methods['pingback.ping'], $methods['pingback.extensions.getPingbacks']);
    return $methods;
});
add_filter('wp_headers', function ($headers) {
    $headers['X-Content-Type-Options'] = 'nosniff';
    $headers['X-Frame-Options'] = 'SAMEORIGIN';
    $headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
    $headers['Permissions-Policy'] = 'camera=(), microphone=(), geolocation=()';
    $headers['Content-Security-Policy'] = "frame-ancestors 'self'; upgrade-insecure-requests";
    if (is_ssl()) { $headers['Strict-Transport-Security'] = 'max-age=15552000'; }
    unset($headers['X-Pingback']);
    return $headers;
});
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// Short, source-IP scoped lockouts: no global account lock and no client-supplied proxy headers.
function sphere_login_bucket() {
    return 'sphere_login_' . hash_hmac('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown', wp_salt('auth'));
}
add_filter('authenticate', function ($user, $username, $password) {
    if ((defined('WP_CLI') && WP_CLI) || $username === '' || $password === '') { return $user; }
    $state = get_transient(sphere_login_bucket());
    if (is_array($state) && $state['count'] >= 10 && $state['until'] > time()) {
        return new WP_Error('sphere_login_limited', 'Too many login attempts. Please try again in 15 minutes.');
    }
    return $user;
}, 100, 3);
add_action('wp_login_failed', function ($username, $error) {
    if (defined('WP_CLI') && WP_CLI) { return; }
    if ($error instanceof WP_Error && $error->get_error_code() === 'sphere_login_limited') { return; }
    $key = sphere_login_bucket();
    $state = get_transient($key);
    if (!is_array($state) || $state['until'] <= time()) {
        $state = ['count' => 0, 'until' => time() + 15 * MINUTE_IN_SECONDS];
    }
    $state['count']++;
    set_transient($key, $state, max(1, $state['until'] - time()));
    if ($state['count'] === 10) { error_log('Sphere security: login_rate_limit_reached'); }
}, 10, 2);
add_action('wp_login', function () { delete_transient(sphere_login_bucket()); });
add_filter('login_errors', function ($message) {
    return __('Login could not be completed. Check your credentials or try again later.', 'sphere');
});
