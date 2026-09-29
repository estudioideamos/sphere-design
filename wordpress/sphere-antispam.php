<?php
/** Plugin Name: Sphere Form Antispam
 * Description: First-party form checks, time limits and duplicate suppression.
 */
if (!defined('ABSPATH')) { exit; }
function sphere_form_ticket() {
    $payload = time() . '.' . wp_generate_password(24, false, false);
    return $payload . '.' . hash_hmac('sha256', $payload, wp_salt('nonce'));
}
function sphere_antispam_check(WP_REST_Request $request, $email, $message) {
    $ticket = $request->get_param('form_ticket');
    if (!is_string($ticket) || !preg_match('/^(\d{10})\.([a-zA-Z0-9]{24})\.([a-f0-9]{64})$/D', $ticket, $parts)
        || !hash_equals(hash_hmac('sha256', $parts[1] . '.' . $parts[2], wp_salt('nonce')), $parts[3])) {
        return new WP_Error('form_session', 'Please reload the contact page and try again.', ['status' => 403]);
    }
    $age = time() - (int) $parts[1];
    if ($age < 3) { return new WP_Error('form_wait', 'Please wait a few seconds before sending your inquiry.', ['status' => 429]); }
    if ($age > 12 * HOUR_IN_SECONDS) { return new WP_Error('form_expired', 'Please reload the contact page and try again.', ['status' => 403]); }
    $origin = $request->get_header('origin') ?: ($_SERVER['HTTP_ORIGIN'] ?? '');
    if ($origin && (strtolower((string) wp_parse_url($origin, PHP_URL_HOST)) !== strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST)))) {
        return new WP_Error('form_origin', 'Please send your inquiry from our contact page.', ['status' => 403]);
    }
    if (preg_match('/[\r\n]/', (string) $request->get_param('email'))) {
        return new WP_Error('invalid', 'Please enter a valid email address.', ['status' => 400]);
    }
    // Store only salted fingerprints, never addresses, messages or raw IPs.
    $duplicate = 'sphere_duplicate_' . hash_hmac('sha256', strtolower($email) . '|' . preg_replace('/\s+/u', ' ', trim($message)), wp_salt('auth'));
    if (get_transient($duplicate)) {
        return new WP_Error('duplicate', 'This inquiry was already submitted. Please wait before sending it again.', ['status' => 429]);
    }
    $key = 'sphere_sender_' . hash_hmac('sha256', strtolower($email), wp_salt('auth'));
    $state = get_transient($key);
    if (!is_array($state) || $state['until'] <= time()) { $state = ['count' => 0, 'until' => time() + 10 * MINUTE_IN_SECONDS]; }
    if ($state['count'] >= 3) { return new WP_Error('sender_rate', 'Please wait a few minutes before sending another inquiry.', ['status' => 429]); }
    $state['count']++;
    set_transient($key, $state, max(1, $state['until'] - time()));
    return $duplicate;
}
