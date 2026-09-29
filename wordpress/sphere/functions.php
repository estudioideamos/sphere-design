<?php
if (!defined("ABSPATH")) {
    exit();
}
add_action("after_setup_theme", function () {
    add_theme_support("title-tag");
    add_theme_support("post-thumbnails");
    add_theme_support("html5", [
        "search-form",
        "gallery",
        "caption",
        "style",
        "script",
    ]);
});
add_action("wp_enqueue_scripts", function () {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    foreach (file_exists($dir . "/generated/site.css") ? ["generated/site"] : ["design", "motion", "wordpress"] as $s) {
        wp_enqueue_style(
            "sphere-" . $s,
            $uri . "/" . $s . ".css",
            [],
            filemtime($dir . "/" . $s . ".css"),
        );
    }
    foreach (["app", "motion", "contact"] as $s) {
        $asset = file_exists($dir . "/generated/" . $s . ".js") ? "generated/" . $s : $s;
        wp_enqueue_script(
            "sphere-" . $s,
            $uri . "/" . $asset . ".js",
            $s === "app" ? [] : ["sphere-app"],
            filemtime($dir . "/" . $asset . ".js"),
            ["strategy" => "defer", "in_footer" => true],
        );
    }
    wp_add_inline_script(
        "sphere-app",
        "window.SPHERE=" .
            wp_json_encode([
                "assets" => $uri . "/assets/",
                "optimized" => $uri . "/optimized/",
                "contact" => rest_url("sphere/v1/contact"),
                "nonce" => wp_create_nonce("sphere_contact"),
            ]) .
            ";",
        "before",
    );
});
function sphere_markup($html)
{
    // Preserve original full-size gallery media while serving appropriately sized previews.
    $uri = get_template_directory_uri();
    foreach (["interior2", "exterior2", "interior1", "hospitality"] as $asset) {
        $html = preg_replace_callback(
            '~<img\b[^>]*src="(?:[^" ]*/)?assets/' . $asset . '\.webp"[^>]*>~',
            function ($match) use ($asset, $uri) {
                $tag = new WP_HTML_Tag_Processor($match[0]);
                if ($tag->next_tag("IMG")) {
                    $tag->set_attribute(
                        "src",
                        $uri . "/optimized/" . $asset . "-1200.webp",
                    );
                    $tag->set_attribute(
                        "srcset",
                        $uri .
                            "/optimized/" .
                            $asset .
                            "-720.webp 720w, " .
                            $uri .
                            "/optimized/" .
                            $asset .
                            "-1200.webp 1200w, " .
                            $uri .
                            "/optimized/" .
                            $asset .
                            "-1800.webp 1800w, " .
                            $uri .
                            "/assets/" .
                            $asset .
                            "-xl.webp 2880w",
                    );
                    $tag->set_attribute("decoding", "async");
                }
                return $tag->get_updated_html();
            },
            $html,
        );
    }
    if (str_contains($html, 'id="hero-video"')) {
        $picture =
            '<picture class="hero-poster"><source media="(max-width:700px)" srcset="' .
            esc_url($uri . "/optimized/hero-mobile-poster.webp") .
            '"><img src="' .
            esc_url($uri . "/assets/hero-poster.webp?v=20260921") .
            '" alt="" width="1920" height="1080" fetchpriority="high" decoding="async"></picture>';
        $html = str_replace(
            '<div class="hero-media">',
            '<div class="hero-media">' . $picture,
            $html,
        );
        $html = preg_replace(
            '~(id="hero-video"[^>]*?) poster="[^"]*"~',
            '$1',
            $html,
        );
    }

    if (function_exists("sphere_site_info")) {
        foreach (sphere_site_defaults() as $key => $value) {
            $replacement = sphere_site_info($key);
            $html = str_replace($value, esc_attr($replacement), $html);
        }
        $html = str_replace(
            "tel:+17868844880",
            "tel:" . preg_replace("/[^+0-9]/", "", sphere_site_info("phone")),
            $html,
        );
    }

    $html = preg_replace(
        "~(?<![A-Za-z0-9/_-])assets/~",
        trailingslashit(get_template_directory_uri()) . "assets/",
        $html,
    );
    if (is_page("contact")) {
        $html = str_replace(
            '<form id="contact-form">',
            '<form id="contact-form" method="post" action="' .
                esc_url(admin_url("admin-post.php")) .
                '"><input type="hidden" name="action" value="sphere_contact"><input type="hidden" name="nonce" value="' .
                esc_attr(wp_create_nonce("sphere_contact")) .
                '"><input type="hidden" name="form_ticket" value="' . esc_attr(sphere_form_ticket()) . '"><label class="sphere-honeypot" aria-hidden="true">Website <input name="website" tabindex="-1" autocomplete="off"></label>',
            $html,
        );
        $html = str_replace(
            "This demo opens your email app with your project details. Nothing is sent automatically.",
            "Tell us about your project. We will reply by email.",
            $html,
        );
        $html = str_replace(
            "Prepare project inquiry",
            "Send project inquiry",
            $html,
        );
        if (isset($_GET["inquiry"])) {
            $html = str_replace(
                '<p id="form-status" aria-live="polite"></p>',
                '<p id="form-status" aria-live="polite">' .
                    ($_GET["inquiry"] === "sent"
                        ? "Thank you. Your inquiry has been submitted."
                        : "Your inquiry could not be sent. Please try again or email info@thespheredesign.com.") .
                    "</p>",
                $html,
            );
        }
    }
    return preg_replace_callback(
        '/(?<=href=")([a-z-]+)\.html/',
        function ($m) {
            return esc_url(
                home_url($m[1] === "index" ? "/" : "/" . $m[1] . "/"),
            );
        },
        $html,
    );
}
function sphere_fragment($name)
{
    return sphere_markup(
        file_get_contents(get_template_directory() . "/" . $name . ".html"),
    );
}
function sphere_projects()
{
    return get_posts([
        "post_type" => "sphere_project",
        "numberposts" => -1,
        "orderby" => ["menu_order" => "ASC", "ID" => "ASC"],
    ]);
}
function sphere_project_card($p, $i)
{
    $terms = wp_get_object_terms($p->ID, "sphere_category");
    $cat = !is_wp_error($terms) && $terms ? $terms[0]->name : "Residential";
    $asset = get_post_meta($p->ID, "sphere_asset", true);
    $yt = get_post_meta($p->ID, "sphere_youtube", true);
    $thumb = get_post_thumbnail_id($p);
    $full = wp_get_attachment_image_url($thumb, "full");
    $alt =
        get_post_meta($thumb, "_wp_attachment_image_alt", true) ?:
        get_the_title($p);
    $img = wp_get_attachment_image($thumb, "large", false, [
        "alt" => $alt,
        "sizes" => "(max-width: 700px) 100vw, 50vw",
        "loading" => "lazy",
    ]);
    $label =
        $cat === "Animations"
            ? "Play film"
            : ($cat === "VR 360°"
                ? "Explore 360°"
                : "View image");
    return '<button class="project reveal" type="button" data-project="' .
        (int) $i .
        '" data-category="' .
        esc_attr($cat) .
        '" data-asset="' .
        esc_attr($asset ?: "custom") .
        '" data-full="' .
        esc_url($full) .
        '" data-youtube="' .
        esc_attr($yt) .
        '" aria-label="' .
        esc_attr($label . ": " . get_the_title($p)) .
        '"><div class="project-image">' .
        $img .
        '<span class="view">' .
        $label .
        ' <span aria-hidden="true">↗</span></span></div><div class="project-meta"><h3>' .
        esc_html(get_the_title($p)) .
        "</h3></div></button>";
}
function sphere_portfolio()
{
    $projects = sphere_projects();
    $cats = [];
    foreach ($projects as $p) {
        $t = wp_get_object_terms($p->ID, "sphere_category");
        if (!is_wp_error($t) && $t) {
            $cats[] = $t[0]->name;
        }
    }
    $cats = array_unique($cats);
    echo sphere_fragment("portfolio-intro");
    echo '<section class="portfolio wrap"><div class="filters" aria-label="Filter portfolio"><button type="button" data-filter="All" aria-pressed="true">All</button>';
    foreach ($cats as $cat) {
        echo '<button type="button" data-filter="' .
            esc_attr($cat) .
            '" aria-pressed="false">' .
            esc_html($cat) .
            "</button>";
    }
    echo '</div><p class="result-count" aria-live="polite"></p><div class="portfolio-grid">';
    foreach ($projects as $i => $p) {
        echo sphere_project_card($p, $i);
    }
    echo '</div><button class="portfolio-more" type="button">Load more projects <span aria-hidden="true">↗</span></button></section>';
}
function sphere_journal()
{
    echo '<section class="page-title wrap"><p class="eyebrow">INSIGHTS — THE SPHERE PERSPECTIVE</p><h1>Ideas behind<br><em>the image.</em></h1><div class="title-bottom"><p>Notes on architecture, atmosphere<br>and the art of seeing what comes next.</p></div></section><section class="journal wrap"><div class="journal-label"><span>THE JOURNAL</span><span>Sphere Design / Insights</span></div><div class="journal-grid">';
    $q = new WP_Query([
        "post_type" => "post",
        "posts_per_page" => 12,
        "paged" => max(1, get_query_var("paged"), get_query_var("page")),
    ]);
    while ($q->have_posts()) {
        $q->the_post();
        echo '<a class="journal-card" href="' .
            esc_url(get_permalink()) .
            '"><div class="journal-image">' .
            get_the_post_thumbnail(null, "large", ["loading" => "lazy"]) .
            '<span aria-hidden="true">↗</span></div><div class="journal-meta"><span>Perspective</span></div><h2>' .
            esc_html(get_the_title()) .
            "</h2><p>" .
            esc_html(get_the_excerpt()) .
            '</p><span class="journal-read">Read story <span aria-hidden="true">↗</span></span></a>';
    }
    echo '</div><nav class="journal-pagination">' .
        paginate_links(["total" => $q->max_num_pages]) .
        "</nav></section>";
    wp_reset_postdata();
}
require_once __DIR__ . "/seo.php";
add_action("template_redirect", function () {
    if (
        preg_match(
            '~/([a-z-]+)\.html$~',
            parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
            $m,
        )
    ) {
        wp_safe_redirect(
            home_url($m[1] === "index" ? "/" : "/" . $m[1] . "/"),
            301,
        );
        exit();
    }
});

add_action("template_redirect", function () {
    if (is_page("contact")) {
        nocache_headers();
    }
});
