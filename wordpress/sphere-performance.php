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
            if (!defined("DONOTCACHEPAGE")) {
                define("DONOTCACHEPAGE", true);
            }
            nocache_headers();
        }
    },
    0,
);
