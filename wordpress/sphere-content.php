<?php
/**
 * Plugin Name: Sphere Design — Contenido y contacto
 * Description: Portfolio administrable y recepción de consultas para Sphere Design.
 * Author: Estudio Ideamos
 * Author URI: https://ideamos.com.ar/
 * Version: 1.1.0
 */
if (!defined("ABSPATH")) {
    exit();
}
add_action("init", function () {
    register_post_type("sphere_project", [
        "labels" => [
            "name" => "Portfolio",
            "singular_name" => "Proyecto",
            "add_new_item" => "Añadir proyecto",
            "edit_item" => "Editar proyecto",
            "all_items" => "Todos los proyectos",
            "add_new" => "Añadir proyecto",
        ],
        "public" => false,
        "show_ui" => true,
        "show_in_rest" => true,
        "menu_icon" => "dashicons-format-gallery",
        "supports" => ["title", "thumbnail", "page-attributes"],
        "taxonomies" => ["sphere_category"],
    ]);
    register_taxonomy("sphere_category", "sphere_project", [
        "label" => "Categorías del portfolio",
        "show_admin_column" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "public" => false,
    ]);
});
add_action("add_meta_boxes", function () {
    add_meta_box(
        "sphere-project-details",
        "Medios del proyecto",
        "sphere_project_box",
        "sphere_project",
    );
});
function sphere_project_box($post)
{
    wp_nonce_field("sphere_project_save", "sphere_project_nonce");
    echo '<p>Elegí la portada en Imagen destacada. Asigná una categoría: Animations para películas o VR 360° para panoramas. Usá Orden para organizar la grilla.</p><label>Video de YouTube: enlace o ID <input style="width:100%" name="sphere_youtube" value="' .
        esc_attr(get_post_meta($post->ID, "sphere_youtube", true)) .
        '"></label>';
}
add_action("save_post_sphere_project", function ($id) {
    if (
        !isset($_POST["sphere_project_nonce"]) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["sphere_project_nonce"])),
            "sphere_project_save",
        ) ||
        !current_user_can("edit_post", $id) ||
        wp_is_post_revision($id)
    ) {
        return;
    }
    $v = sanitize_text_field(wp_unslash($_POST["sphere_youtube"] ?? ""));
    if (preg_match("~(?:v=|youtu.be/|embed/)([A-Za-z0-9_-]{11})~", $v, $m)) {
        $v = $m[1];
    }
    update_post_meta(
        $id,
        "sphere_youtube",
        preg_match('/^[A-Za-z0-9_-]{11}$/', $v) ? $v : "",
    );
});
add_action("rest_api_init", function () {
    register_rest_route("sphere/v1", "/contact", [
        "methods" => "POST",
        "permission_callback" => "__return_true",
        "callback" => "sphere_contact",
    ]);
});
function sphere_contact(WP_REST_Request $r)
{
    if (!wp_verify_nonce($r->get_param("nonce"), "sphere_contact")) {
        return new WP_Error(
            "expired",
            "Please refresh this page and try again.",
            ["status" => 403],
        );
    }
    if ($r->get_param("website")) {
        return new WP_Error("invalid", "Please try again.", ["status" => 400]);
    }
    $name = sanitize_text_field($r->get_param("name"));
    $email = sanitize_email($r->get_param("email"));
    $message = sanitize_textarea_field($r->get_param("message"));
    $service = sanitize_text_field($r->get_param("service"));
    $company = sanitize_text_field($r->get_param("company"));
    if (
        !$name ||
        !is_email($email) ||
        !$message ||
        !$service ||
        strlen($message) > 12000 ||
        strlen($name) > 200 ||
        strlen($company) > 300
    ) {
        return new WP_Error(
            "invalid",
            "Please complete your name, email, service and project details.",
            ["status" => 400],
        );
    }
    $key =
        "sphere_contact_" .
        hash_hmac("sha256", $_SERVER["REMOTE_ADDR"] ?? "", wp_salt());
    $count = (int) get_transient($key);
    if ($count >= 5) {
        return new WP_Error(
            "rate",
            "Please wait a few minutes before sending another inquiry.",
            ["status" => 429],
        );
    }
    set_transient($key, $count + 1, 10 * MINUTE_IN_SECONDS);
    $ok = wp_mail(
        sphere_site_info("email"),
        "Website project inquiry — " . $name,
        "Name: $name\nEmail: $email\nCompany: $company\nService: $service\n\n$message",
        ["Reply-To: " . $email],
    );
    if (!$ok) {
        return new WP_Error(
            "mail",
            "Your inquiry could not be sent. Please use the contact email displayed on this page.",
            ["status" => 503],
        );
    }
    return rest_ensure_response([
        "message" =>
            "Thank you. Your inquiry has been submitted. We will be in touch.",
    ]);
}

function sphere_contact_fallback()
{
    $request = new WP_REST_Request("POST", "/sphere/v1/contact");
    $request->set_body_params(wp_unslash($_POST));
    $result = sphere_contact($request);
    wp_safe_redirect(
        add_query_arg(
            "inquiry",
            is_wp_error($result) ? "error" : "sent",
            home_url("/contact/"),
        ) . "#form-status",
        303,
    );
    exit();
}
add_action("admin_post_nopriv_sphere_contact", "sphere_contact_fallback");
add_action("admin_post_sphere_contact", "sphere_contact_fallback");

// Preserve the resolution needed for project images and 2:1 panoramas.
add_filter(
    "big_image_size_threshold",
    function ($threshold, $dimensions) {
        if (empty($dimensions[1])) {
            return $threshold;
        }
        return abs($dimensions[0] / $dimensions[1] - 2) < 0.02 ? 4096 : 2880;
    },
    10,
    2,
);

/** Reject heavy source files before the hosting attempts to process them. */
function sphere_upload_limits($file)
{
    if (!empty($file["error"])) {
        return $file;
    }
    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (
        in_array($extension, [
            "mp4",
            "mov",
            "avi",
            "mkv",
            "webm",
            "m4v",
            "mpeg",
            "mpg",
        ])
    ) {
        $file["error"] =
            "Para proteger la velocidad y el espacio del hosting, los videos no se suben a Medios. Usá un enlace de YouTube en Portfolio. Consultá Manual del sitio → Imágenes y videos.";
        return $file;
    }
    if (
        in_array($extension, [
            "psd",
            "tif",
            "tiff",
            "raw",
            "dng",
            "heic",
            "heif",
            "bmp",
        ])
    ) {
        $file["error"] =
            "Exportá esta imagen a WebP o JPEG antes de subirla. No se admiten archivos de trabajo pesados.";
        return $file;
    }
    $image = in_array($extension, [
        "jpg",
        "jpeg",
        "png",
        "webp",
        "avif",
        "gif",
    ]);
    $limit = $image ? 3 * MB_IN_BYTES : 5 * MB_IN_BYTES;
    $bytes =
        $file["size"] ??
        (!empty($file["tmp_name"]) && is_file($file["tmp_name"])
            ? filesize($file["tmp_name"])
            : 0);
    if ($bytes > $limit) {
        $file["error"] = $image
            ? "La imagen supera 3 MB. Optimizala antes de subirla; para la mayoría de las fotos recomendamos menos de 1 MB."
            : "El archivo supera 5 MB. Comprimilo o enlazalo desde un servicio externo.";
        return $file;
    }
    if ($image && !empty($file["tmp_name"])) {
        $dimensions = @getimagesize($file["tmp_name"]);
        if ($dimensions && max($dimensions[0], $dimensions[1]) > 4096) {
            $file["error"] =
                "La imagen supera 4096 px en su lado mayor. Exportala a 2400–2880 px para portfolio, 1600–2000 px para Insights o 4096 × 2048 px para panoramas 360°.";
        }
    }
    return $file;
}
add_filter("wp_handle_upload_prefilter", "sphere_upload_limits");
add_filter("wp_handle_sideload_prefilter", "sphere_upload_limits");
// Visual identification in the portfolio list.
add_filter("manage_sphere_project_posts_columns", function ($columns) {
    $out = [];
    foreach ($columns as $key => $label) {
        $out[$key] = $label;
        if ($key === "cb") {
            $out["sphere_preview"] = "Imagen";
        }
    }
    return $out;
});
add_action(
    "manage_sphere_project_posts_custom_column",
    function ($column, $id) {
        if ($column === "sphere_preview") {
            echo get_the_post_thumbnail($id, "thumbnail", [
                "style" =>
                    "width:80px;height:58px;object-fit:cover;border-radius:5px;",
            ]);
        }
    },
    10,
    2,
);
add_filter(
    "enter_title_here",
    function ($placeholder, $post) {
        return $post->post_type === "sphere_project"
            ? "Nombre del proyecto"
            : $placeholder;
    },
    10,
    2,
);
add_action("admin_head", function () {
    if (get_current_screen()?->post_type !== "sphere_project") {
        return;
    }
    echo "<style>.column-sphere_preview{width:94px}body.post-type-sphere_project .postbox{border:1px solid #dce2d4;border-radius:10px;overflow:hidden;box-shadow:0 3px 12px #20301a05}body.post-type-sphere_project #title{padding:12px 16px;height:auto;border:1px solid #c9d4be;border-radius:8px}body.post-type-sphere_project #sphere-project-details .inside{padding:12px 20px 22px;line-height:1.8}body.post-type-sphere_project #sphere-project-details input{padding:9px 12px;border-radius:6px;margin-top:8px}body.post-type-sphere_project .wp-list-table .row-title{font-size:14px}body.post-type-sphere_project #postimagediv img{max-height:300px;object-fit:contain}body.post-type-sphere_project .button-primary{background:#344b2b;border-color:#344b2b}</style>";
});
