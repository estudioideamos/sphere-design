<?php
/**
 * Plugin Name: Sphere Design — Datos de contacto
 * Description: Correo, teléfono y redes sociales de Sphere Design.
 * Author: Estudio Ideamos
 * Author URI: https://ideamos.com.ar/
 * Version: 1.3.0
 */
if (!defined("ABSPATH")) {
    exit();
}
function sphere_site_defaults()
{
    return [
        "email" => "info@thespheredesign.com",
        "phone" => "+1 (786) 884-4880",
        "location" => "Miami, Florida · Working worldwide",
        "instagram" => "https://www.instagram.com/thespheredesign/",
        "linkedin" => "https://www.linkedin.com/company/studiospheredesign/",
        "behance" => "https://www.behance.net/spheredesignllc",
    ];
}
function sphere_site_info($key)
{
    $values = get_option("sphere_site_info", []);
    return $values[$key] ?? (sphere_site_defaults()[$key] ?? "");
}
add_action(
    "admin_menu",
    function () {
        add_menu_page(
            "Datos de contacto",
            "Datos de contacto",
            "manage_options",
            "sphere-contact-details",
            "sphere_contact_details_page",
            "dashicons-phone",
            4,
        );
        remove_menu_page("edit.php?post_type=page");
    },
    99,
);
add_action("admin_enqueue_scripts", function ($hook) {
    if ($hook !== "toplevel_page_sphere-contact-details") {
        return;
    }
    foreach (["sphere-manual", "sphere-editor"] as $name) {
        wp_enqueue_style(
            $name,
            plugins_url($name . ".css", __FILE__),
            [],
            filemtime(__DIR__ . "/" . $name . ".css"),
        );
    }
});
function sphere_contact_details_page()
{
    if (!current_user_can("manage_options")) {
        return;
    }
    echo '<div class="sphere-guide sphere-contact-settings"><header class="sg-hero"><div><span class="sg-kicker">SPHERE DESIGN / CONTACTO</span><h1>Siempre,<br><em>en contacto.</em></h1><p>Actualizá los datos que tus clientes usan para encontrarte. Se aplican al menú, al pie y a la página de contacto.</p></div><span class="dashicons dashicons-phone" aria-hidden="true"></span></header>';
    if (isset($_GET["saved"])) {
        echo '<div class="notice notice-success inline"><p>Los datos se guardaron correctamente.</p></div>';
    }
    echo '<form method="post" action="' .
        esc_url(admin_url("admin-post.php")) .
        '"><input type="hidden" name="action" value="sphere_save_contact_details">';
    wp_nonce_field("sphere_contact_details", "sphere_contact_details_nonce");
    $groups = [
        [
            "title" => "Contacto directo",
            "intro" => "El correo también recibe las consultas del formulario.",
            "fields" => [
                "email" => [
                    "Correo electrónico",
                    "email",
                    "Ejemplo: info@empresa.com",
                ],
                "phone" => [
                    "Teléfono",
                    "tel",
                    "Incluí el código de país: +1 (786) 884-4880",
                ],
                "location" => [
                    "Ubicación y alcance",
                    "text",
                    "Texto público que describe desde dónde trabajan.",
                ],
            ],
        ],
        [
            "title" => "Redes sociales",
            "intro" =>
                "Pegá la dirección completa del perfil, empezando por https://.",
            "fields" => [
                "instagram" => ["Instagram", "url", "Perfil de Instagram"],
                "linkedin" => ["LinkedIn", "url", "Página de la empresa"],
                "behance" => ["Behance", "url", "Portfolio en Behance"],
            ],
        ],
    ];
    foreach ($groups as $i => $group) {
        echo '<section class="sg-section sc-section"><div class="sc-heading"><span class="sg-kicker">0' .
            ($i + 1) .
            "</span><h2>" .
            esc_html($group["title"]) .
            "</h2><p>" .
            esc_html($group["intro"]) .
            '</p></div><div class="sc-fields">';
        foreach ($group["fields"] as $key => [$label, $type, $help]) {
            echo '<label for="sc-' .
                $key .
                '"><span>' .
                esc_html($label) .
                '</span><input required id="sc-' .
                $key .
                '" type="' .
                $type .
                '" name="info[' .
                $key .
                ']" value="' .
                esc_attr(sphere_site_info($key)) .
                '"><small>' .
                esc_html($help) .
                "</small></label>";
        }
        echo "</div></section>";
    }
    echo '<div class="sc-save"><button class="button button-primary button-hero" type="submit">Guardar datos</button><span>Los cambios se publican al guardar.</span><a href="' .
        esc_url(home_url("/contact/")) .
        '" target="_blank" rel="noopener">Ver Contacto ↗</a></div></form><footer class="sg-credit"><span>Hecho para Sphere Design.</span><a href="https://ideamos.com.ar/" target="_blank" rel="noopener">Diseño y desarrollo por <strong>Estudio Ideamos ↗</strong></a></footer></div>';
}
add_action("admin_post_sphere_save_contact_details", function () {
    if (!current_user_can("manage_options")) {
        wp_die("Sin permisos.");
    }
    check_admin_referer(
        "sphere_contact_details",
        "sphere_contact_details_nonce",
    );
    $posted = wp_unslash($_POST["info"] ?? []);
    if (!is_array($posted)) {
        wp_die("Datos no válidos.");
    }
    $info = [];
    foreach (sphere_site_defaults() as $key => $default) {
        $value = $posted[$key] ?? "";
        if (!is_scalar($value)) {
            wp_die("Datos no válidos.");
        }
        $info[$key] =
            $key === "email"
                ? sanitize_email($value)
                : (in_array($key, ["instagram", "linkedin", "behance"])
                    ? esc_url_raw($value, ["http", "https"])
                    : sanitize_text_field($value));
    }
    if (!is_email($info["email"])) {
        wp_die("Ingresá un correo válido.");
    }
    update_option("sphere_site_info", $info);
    wp_safe_redirect(
        admin_url("admin.php?page=sphere-contact-details&saved=1"),
    );
    exit();
});
// The composed pages are fixed; editorial work is limited to posts, projects and contact data.
add_filter(
    "get_edit_post_link",
    function ($link, $id) {
        return get_post_type($id) === "page" ? "" : $link;
    },
    10,
    2,
);
add_action("admin_init", function () {
    global $pagenow;
    if (($_GET["page"] ?? "") === "sphere-content-editor") {
        wp_safe_redirect(admin_url("admin.php?page=sphere-contact-details"));
        exit();
    }
    $page_editor =
        ($pagenow === "post.php" &&
            isset($_GET["post"]) &&
            get_post_type(absint($_GET["post"])) === "page") ||
        ($pagenow === "post-new.php" &&
            ($_GET["post_type"] ?? "") === "page") ||
        ($pagenow === "edit.php" && ($_GET["post_type"] ?? "") === "page");
    if ($page_editor && current_user_can("edit_pages")) {
        wp_safe_redirect(admin_url("admin.php?page=sphere-manual"));
        exit();
    }
});

// Keep upload guidance visible both before and after selecting a featured image.
add_filter('admin_post_thumbnail_html', function ($html, $post_id) {
    $type = get_post_type($post_id);
    if (!in_array($type, ['post', 'sphere_project'], true)) { return $html; }
    $details = $type === 'post'
        ? '<li><strong>Dimensiones:</strong> horizontal, de 1600 a 2000 px de ancho. Por ejemplo: 1920 × 1080 px.</li><li><strong>Peso recomendado:</strong> entre 200 y 600 KB.</li>'
        : '<li><strong>Dimensiones:</strong> 2400–2880 px en el lado mayor, conservando la proporción original. Para VR 360°: 4096 × 2048 px.</li><li><strong>Peso recomendado:</strong> menos de 1 MB.</li>';
    $notice = '<div class="sphere-featured-image-guide" style="background:#f0f4ee;border:1px solid #d3dfcc;border-left:3px solid #536945;border-radius:6px;padding:12px;margin:4px 0 14px;color:#263321;font-size:12px;line-height:1.6"><strong style="font-size:13px">Cómo preparar esta imagen</strong><ul style="padding-left:16px;margin:8px 0;list-style:disc"><li><strong>Formato:</strong> WebP recomendado; también JPG/JPEG. PNG solo si necesitás transparencia.</li>' . $details . '<li><strong>Límite de carga:</strong> 3 MB y hasta 4096 px por lado.</li></ul><p style="margin:8px 0 0;font-size:12px">No agrandes imágenes pequeñas. Dejá margen alrededor del contenido importante: las miniaturas pueden recortarse. Completá el texto alternativo al elegir la imagen.</p></div>';
    return $notice . $html;
}, 10, 2);
