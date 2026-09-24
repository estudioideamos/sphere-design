<?php
if (!defined("ABSPATH")) {
    exit();
}
function sphere_seo_defaults()
{
    return [
        "home" => [
            "Architectural Visualization Studio in Miami | Sphere Design",
            "Architectural visualization for confident decisions before construction. Discover photorealistic renderings, films and immersive experiences by Sphere Design.",
        ],
        "about" => [
            "About Our Architectural Visualization Studio | Sphere Design",
            "Meet Sphere Design, a Miami-based architectural visualization studio helping architects, developers and designers communicate their vision worldwide.",
        ],
        "portfolio" => [
            "Architectural Rendering & Animation Portfolio | Sphere Design",
            "Explore architectural renderings, interiors, cinematic animations and 360° experiences for residential, hospitality and commercial projects by Sphere Design.",
        ],
        "insights" => [
            "Architectural Visualization Insights | Sphere Design",
            "Explore ideas on architectural visualization, materials, design and immersive experiences. Insights from the Sphere Design studio in Miami.",
        ],
        "contact" => [
            "Contact Our Visualization Studio | Sphere Design",
            "Tell Sphere Design about your architectural visualization project. Contact our Miami-based team for renderings, architectural films and immersive experiences.",
        ],
    ];
}
function sphere_seo_title()
{
    $id = get_queried_object_id();
    $custom = get_post_meta($id, "sphere_seo_title", true);
    if ($custom) {
        return $custom;
    }
    if (is_404()) {
        return "Page not found | Sphere Design";
    }
    $slug = is_front_page() ? "home" : get_post_field("post_name", $id);
    $defaults = sphere_seo_defaults();
    if (isset($defaults[$slug])) {
        $title = $defaults[$slug][0];
    } elseif (is_singular()) {
        $title = get_the_title($id) . " | Sphere Design";
    } else {
        return "";
    }
    $page = max(1, (int) get_query_var("paged"), (int) get_query_var("page"));
    return $title . ($page > 1 ? " — Page " . $page : "");
}
add_filter("pre_get_document_title", "sphere_seo_title");
function sphere_seo_description()
{
    $id = get_queried_object_id();
    $custom = get_post_meta($id, "sphere_description", true);
    if ($custom) {
        return wp_strip_all_tags($custom);
    }
    $slug = is_front_page() ? "home" : get_post_field("post_name", $id);
    $defaults = sphere_seo_defaults();
    if (isset($defaults[$slug])) {
        return $defaults[$slug][1];
    }
    $text = wp_strip_all_tags(get_the_excerpt($id));
    return $text
        ? wp_html_excerpt($text, 165, "…")
        : "Sphere Design architectural visualization studio. Based in Miami. Working worldwide.";
}
add_filter("language_attributes", function ($attributes) {
    return is_admin() ? $attributes : 'lang="en-US"';
});
add_filter("wp_robots", function ($robots) {
    if (is_search() || is_archive() || is_404() || is_attachment()) {
        $robots["noindex"] = true;
        unset($robots["index"]);
    }
    return $robots;
});
add_filter(
    "wp_sitemaps_add_provider",
    fn($provider, $name) => $name === "users" ? false : $provider,
    10,
    2,
);
add_filter("wp_sitemaps_taxonomies", fn($taxonomies) => []);
remove_action("wp_head", "rel_canonical");
add_action(
    "wp_head",
    function () {
        if (!is_singular() && !is_front_page()) {
            return;
        }
        $id = get_queried_object_id();
        $home = home_url("/");
        $url = is_front_page() ? $home : get_permalink($id);
        $page = max(
            1,
            (int) get_query_var("paged"),
            (int) get_query_var("page"),
        );
        if ($page > 1) {
            $url = trailingslashit($url) . "page/" . $page . "/";
        }
        $title = wp_get_document_title();
        $desc = sphere_seo_description();
        $cover =
            get_the_post_thumbnail_url($id, "full") ?:
            get_template_directory_uri() . "/assets/og-sphere.jpg";
        $article = is_singular("post");
        echo '<link rel="canonical" href="' .
            esc_url($url) .
            '"><meta name="description" content="' .
            esc_attr($desc) .
            '"><meta name="theme-color" content="#141513">';
        foreach (
            [
                "title" => $title,
                "description" => $desc,
                "url" => $url,
                "image" => $cover,
                "image:alt" => $article
                    ? get_the_title($id)
                    : "Sphere Design architectural visualization",
                "type" => $article ? "article" : "website",
                "site_name" => "Sphere Design",
                "locale" => "en_US",
            ]
            as $key => $value
        ) {
            echo '<meta property="og:' .
                esc_attr($key) .
                '" content="' .
                esc_attr($value) .
                '">';
        }
        echo '<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="' .
            esc_attr($title) .
            '"><meta name="twitter:description" content="' .
            esc_attr($desc) .
            '"><meta name="twitter:image" content="' .
            esc_url($cover) .
            '">';
        if (!has_site_icon()) {
            echo '<link rel="icon" href="' .
                esc_url(get_template_directory_uri() . "/assets/favicon.png") .
                '">';
        }
        $org = $home . "#organization";
        $website = $home . "#website";
        $webpage = $url . "#webpage";
        $logo =
            get_site_icon_url(512) ?:
            get_template_directory_uri() . "/assets/logo.png";
        $graph = [
            [
                "@type" => "Organization",
                "@id" => $org,
                "name" => "Sphere Design",
                "url" => $home,
                "logo" => ["@type" => "ImageObject", "url" => $logo],
                "email" => sphere_site_info("email"),
                "telephone" => sphere_site_info("phone"),
                "sameAs" => array_values(
                    array_filter(
                        array_map("sphere_site_info", [
                            "instagram",
                            "linkedin",
                            "behance",
                        ]),
                    ),
                ),
            ],
            [
                "@type" => "WebSite",
                "@id" => $website,
                "url" => $home,
                "name" => "Sphere Design",
                "publisher" => ["@id" => $org],
                "inLanguage" => "en-US",
            ],
        ];
        $type = is_page("about")
            ? "AboutPage"
            : (is_page("contact")
                ? "ContactPage"
                : (is_page(["portfolio", "insights"])
                    ? "CollectionPage"
                    : "WebPage"));
        $node = [
            "@type" => $type,
            "@id" => $webpage,
            "url" => $url,
            "name" => $title,
            "description" => $desc,
            "isPartOf" => ["@id" => $website],
            "about" => ["@id" => $org],
            "inLanguage" => "en-US",
            "primaryImageOfPage" => ["@type" => "ImageObject", "url" => $cover],
        ];
        if (!is_front_page()) {
            $items = [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "Home",
                    "item" => $home,
                ],
            ];
            if ($article) {
                $items[] = [
                    "@type" => "ListItem",
                    "position" => 2,
                    "name" => "Insights",
                    "item" => home_url("/insights/"),
                ];
            }
            $items[] = [
                "@type" => "ListItem",
                "position" => count($items) + 1,
                "name" => get_the_title($id),
                "item" => $url,
            ];
            $graph[] = [
                "@type" => "BreadcrumbList",
                "@id" => $url . "#breadcrumb",
                "itemListElement" => $items,
            ];
            $node["breadcrumb"] = ["@id" => $url . "#breadcrumb"];
        }
        $graph[] = $node;
        if ($article) {
            $published = get_the_date(DATE_W3C, $id);
            $modified = get_the_modified_date(DATE_W3C, $id);
            echo '<meta property="article:published_time" content="' .
                esc_attr($published) .
                '"><meta property="article:modified_time" content="' .
                esc_attr($modified) .
                '">';
            $graph[] = [
                "@type" => "BlogPosting",
                "@id" => $url . "#article",
                "headline" => get_the_title($id),
                "description" => $desc,
                "image" => [$cover],
                "datePublished" => $published,
                "dateModified" => $modified,
                "mainEntityOfPage" => ["@id" => $webpage],
                "author" => [
                    "@type" => "Organization",
                    "@id" => $org,
                    "name" => "Sphere Design",
                    "url" => home_url("/about/"),
                ],
                "publisher" => ["@id" => $org],
                "inLanguage" => "en-US",
            ];
        }
        echo '<script type="application/ld+json">' .
            wp_json_encode(
                ["@context" => "https://schema.org", "@graph" => $graph],
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES,
            ) .
            "</script>";
    },
    5,
);
add_action("add_meta_boxes", function () {
    add_meta_box(
        "sphere-seo",
        "Presentación en Google y redes",
        function ($post) {
            wp_nonce_field("sphere_seo_meta", "sphere_seo_nonce");
            echo '<p>Opcional: si dejás estos campos vacíos, se usa el título del artículo y su extracto. No cambia el texto visible de la entrada.</p><p><label for="sphere_seo_title"><strong>Título para buscadores</strong></label><input class="widefat" id="sphere_seo_title" name="sphere_seo_title" value="' .
                esc_attr(get_post_meta($post->ID, "sphere_seo_title", true)) .
                '"><small>Buscá un título claro de unas 50–60 letras, sin repetir palabras clave.</small></p><p><label for="sphere_seo_description"><strong>Descripción breve</strong></label><textarea class="widefat" rows="3" id="sphere_seo_description" name="sphere_seo_description">' .
                esc_textarea(
                    get_post_meta($post->ID, "sphere_description", true),
                ) .
                "</textarea><small>Como orientación, 140–160 caracteres. Google puede mostrar otro fragmento del contenido.</small></p>";
        },
        "post",
        "normal",
        "default",
    );
});
add_action("save_post_post", function ($id) {
    if (
        wp_is_post_revision($id) ||
        (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) ||
        !current_user_can("edit_post", $id) ||
        !isset($_POST["sphere_seo_nonce"]) ||
        !wp_verify_nonce($_POST["sphere_seo_nonce"], "sphere_seo_meta")
    ) {
        return;
    }
    update_post_meta(
        $id,
        "sphere_seo_title",
        sanitize_text_field(wp_unslash($_POST["sphere_seo_title"] ?? "")),
    );
    update_post_meta(
        $id,
        "sphere_description",
        sanitize_textarea_field(
            wp_unslash($_POST["sphere_seo_description"] ?? ""),
        ),
    );
});
