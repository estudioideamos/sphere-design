<?php
// Clear dependent public listings after editorial changes.
function sphere_invalidate_public_cache()
{
    static $scheduled = false;
    if ($scheduled) {
        return;
    }
    $scheduled = true;
    add_action(
        "shutdown",
        function () {
            if (has_action("litespeed_purge_all")) {
                do_action("litespeed_purge_all");
            }
            if (function_exists("wp_cache_clear_cache")) {
                wp_cache_clear_cache();
            }
        },
        99,
    );
}
add_action("save_post", function ($id) {
    if (
        !wp_is_post_revision($id) &&
        in_array(get_post_type($id), ["page", "post", "sphere_project"])
    ) {
        sphere_invalidate_public_cache();
    }
});
add_action("deleted_post", "sphere_invalidate_public_cache");
add_action("updated_option", function ($name) {
    if ($name === "sphere_site_info") {
        sphere_invalidate_public_cache();
    }
});
add_action(
    "template_redirect",
    function () {
        if (is_page("contact")) {
            do_action("litespeed_control_set_nocache", "Sphere contact form");
            if (!defined("DONOTCACHEPAGE")) {
                define("DONOTCACHEPAGE", true);
            }
            nocache_headers();
        }
    },
    0,
);

// This classic, custom theme does not use WordPress emoji detection or block-editor styles.
add_action("init", function () {
    remove_action("wp_head", "print_emoji_detection_script", 7);
    remove_action("wp_print_styles", "print_emoji_styles");
    remove_action("wp_enqueue_scripts", "wp_enqueue_emoji_styles");
});
add_action(
    "wp_enqueue_scripts",
    function () {
        wp_dequeue_style("wp-block-library");
        wp_dequeue_style("global-styles");
        wp_dequeue_style("classic-theme-styles");
    },
    100,
);
add_action(
    "wp_head",
    function () {
        $uri = get_template_directory_uri();
        foreach (["dm-sans-latin", "italiana-latin"] as $font) {
            echo '<link rel="preload" href="' .
                esc_url($uri . "/assets/fonts/" . $font . ".woff2") .
                '" as="font" type="font/woff2" crossorigin>';
        }
        if (is_front_page()) {
            echo '<link rel="preload" as="image" href="' .
                esc_url($uri . "/optimized/hero-mobile-poster.webp") .
                '" media="(max-width:700px)" fetchpriority="high">';
            echo '<link rel="preload" as="image" href="' .
                esc_url($uri . "/assets/hero-poster.webp?v=20260921") .
                '" media="(min-width:701px)" fetchpriority="high">';
        }
    },
    1,
);
