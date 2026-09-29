<?php
/** Plugin Name: Sphere Discovery */
if (!defined('ABSPATH')) { exit; }
// Supplemental machine-readable directory. Public HTML and the XML sitemap remain authoritative.
add_action('template_redirect', function () {
    $path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($path !== wp_parse_url(home_url('/llms.txt'), PHP_URL_PATH)) { return; }
    status_header(200);
    header('Content-Type: text/plain; charset=utf-8');
    if (!(int) get_option('blog_public')) { header('X-Robots-Tag: noindex'); }
    echo "# Sphere Design\n\n> Architectural visualization studio based in Miami, working worldwide.\n\n";
    echo "Architectural renderings, interiors, animation, 3D floor plans and CAD/BIM modeling.\n\n## Official pages\n";
    foreach (['/' => 'Home', '/about/' => 'About the studio', '/portfolio/' => 'Portfolio', '/insights/' => 'Insights', '/contact/' => 'Contact'] as $slug => $label) {
        echo '- [' . $label . '](' . esc_url_raw(home_url($slug)) . ")\n";
    }
    echo "\n## Recent insights\n";
    foreach (get_posts(['post_type'=>'post','post_status'=>'publish','numberposts'=>20]) as $post) {
        echo '- [' . str_replace(["\n", '[', ']'], ' ', wp_strip_all_tags($post->post_title)) . '](' . esc_url_raw(get_permalink($post)) . ")\n";
    }
    echo "\n## Contact\n" . sanitize_email(sphere_site_info('email')) . "\n" . sanitize_text_field(sphere_site_info('phone')) . "\n\n## Sitemap\n" . esc_url_raw(home_url('/wp-sitemap.xml')) . "\n";
    exit;
}, 0);
