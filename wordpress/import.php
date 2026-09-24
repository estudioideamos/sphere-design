<?php
if (!defined("WP_CLI")) {
    exit();
}
$dir = WP_CONTENT_DIR . "/themes/sphere";
$seed = json_decode(file_get_contents($dir . "/seed.json"), true);
require_once ABSPATH . "wp-admin/includes/image.php";
function sphere_import_media($asset, $alt)
{
    $existing = get_posts([
        "post_type" => "attachment",
        "post_status" => "inherit",
        "meta_key" => "sphere_source",
        "meta_value" => $asset,
        "fields" => "ids",
        "numberposts" => 1,
    ]);
    if ($existing) {
        return $existing[0];
    }
    $theme = WP_CONTENT_DIR . "/themes/sphere/assets/";
    $source = $theme . $asset . ".webp";
    if (file_exists($theme . $asset . "-xl.webp")) {
        $source = $theme . $asset . "-xl.webp";
    }
    if (!file_exists($source)) {
        WP_CLI::warning("Missing media " . $asset);
        return 0;
    }
    $up = wp_upload_dir();
    $folder = $up["basedir"] . "/sphere";
    wp_mkdir_p($folder);
    $name = str_replace("/", "-", $asset) . ".webp";
    $dest = $folder . "/" . $name;
    copy($source, $dest);
    $size = getimagesize($dest);
    $id = wp_insert_attachment(
        [
            "post_title" => $alt,
            "post_mime_type" => "image/webp",
            "post_status" => "inherit",
        ],
        $dest,
    );
    update_post_meta($id, "sphere_source", $asset);
    update_post_meta($id, "_wp_attachment_image_alt", $alt);
    $meta = [
        "width" => $size[0],
        "height" => $size[1],
        "file" => "sphere/" . $name,
        "sizes" => [],
        "image_meta" => [],
    ];
    foreach (
        [
            "medium" => [$theme . $asset . "-sm.webp", "sm"],
            "large" => [$theme . $asset . ".webp", "large"],
        ]
        as $key => $spec
    ) {
        if (!file_exists($spec[0])) {
            continue;
        }
        $n = str_replace(".webp", "-" . $spec[1] . ".webp", $name);
        copy($spec[0], $folder . "/" . $n);
        $dim = getimagesize($folder . "/" . $n);
        $meta["sizes"][$key] = [
            "file" => $n,
            "width" => $dim[0],
            "height" => $dim[1],
            "mime-type" => "image/webp",
        ];
    }
    wp_update_attachment_metadata($id, $meta);
    return $id;
}
foreach ($seed["pages"] as $p) {
    $old = get_page_by_path($p["slug"]);
    $id = wp_insert_post(
        [
            "ID" => $old ? $old->ID : 0,
            "post_type" => "page",
            "post_status" => "publish",
            "post_name" => $p["slug"],
            "post_title" => $p["title"],
            "post_content" => $p["content"],
        ],
        true,
    );
    if (is_wp_error($id)) {
        WP_CLI::error($id->get_error_message());
    }
    update_post_meta($id, "sphere_description", $p["description"]);
    if ($p["slug"] === "home") {
        update_option("page_on_front", $id);
    }
}
foreach ($seed["posts"] as $p) {
    $old = get_page_by_path($p["slug"], OBJECT, "post");
    $id = wp_insert_post(
        [
            "ID" => $old ? $old->ID : 0,
            "post_type" => "post",
            "post_status" => "publish",
            "post_name" => $p["slug"],
            "post_title" => $p["title"],
            "post_content" => $p["content"],
            "post_excerpt" => $p["excerpt"],
        ],
        true,
    );
    if (is_wp_error($id)) {
        WP_CLI::error($id->get_error_message());
    }
    $asset = preg_replace('~^assets/|\.webp$~', "", $p["cover"]);
    set_post_thumbnail($id, sphere_import_media($asset, $p["title"]));
}
foreach ($seed["portfolio"] as $i => $p) {
    $slug = sanitize_title(str_replace("/", "-", $p[0]));
    $old = get_page_by_path($slug, OBJECT, "sphere_project");
    $id = wp_insert_post(
        [
            "ID" => $old ? $old->ID : 0,
            "post_type" => "sphere_project",
            "post_status" => "publish",
            "post_name" => $slug,
            "post_title" => preg_replace('~\s*/\s*\d+$~', "", $p[2]),
            "menu_order" => $i,
        ],
        true,
    );
    if (is_wp_error($id)) {
        WP_CLI::error($id->get_error_message());
    }
    wp_set_object_terms($id, $p[1], "sphere_category");
    update_post_meta($id, "sphere_asset", $p[0]);
    update_post_meta($id, "sphere_youtube", $p[4]["youtube"] ?? "");
    set_post_thumbnail(
        $id,
        sphere_import_media($p[4]["poster"] ?? $p[0], $p[3]),
    );
}
update_option("show_on_front", "page");
update_option("blogname", "Sphere Design");
update_option(
    "blogdescription",
    "Architectural visualization — Miami & worldwide",
);
update_option("blog_public", 0);
update_option("permalink_structure", "/%postname%/");
update_option("default_comment_status", "closed");
update_option("default_ping_status", "closed");
update_option("WPLANG", "en_US");
foreach (["Hello world!", "Sample Page"] as $title) {
    $posts = get_posts([
        "post_type" => ["post", "page"],
        "post_status" => "publish",
        "title" => $title,
    ]);
    foreach ($posts as $p) {
        wp_update_post(["ID" => $p->ID, "post_status" => "draft"]);
    }
}
flush_rewrite_rules();
WP_CLI::success("Imported 5 pages, 3 articles and 79 portfolio projects.");
