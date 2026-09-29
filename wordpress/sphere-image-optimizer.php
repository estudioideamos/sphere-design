<?php
/** Plugin Name: Sphere Automatic Image Optimization */
if (!defined('ABSPATH')) { exit; }
function sphere_optimize_uploaded_image($upload) {
    if (!empty($upload['error']) || empty($upload['file']) || !in_array($upload['type'] ?? '', ['image/jpeg', 'image/png'], true)) { return $upload; }
    $file = $upload['file'];
    $uploads = wp_upload_dir();
    $real = realpath($file);
    $base = realpath($uploads['basedir']);
    if (!$real || !$base || !str_starts_with($real, $base . DIRECTORY_SEPARATOR)) { return $upload; }
    if (!wp_image_editor_supports(['mime_type' => 'image/webp'])) { return $upload; }
    $editor = wp_get_image_editor($file);
    if (is_wp_error($editor)) { return $upload; }
    if (method_exists($editor, 'maybe_exif_rotate')) { $editor->maybe_exif_rotate(); }
    $size = $editor->get_size();
    if (empty($size['height']) || empty($size['width'])) { return $upload; }
    // Preserve resolution and the 2:1 ratio used by the panorama viewer.
    $limit = abs($size['width'] / $size['height'] - 2) < 0.02 ? 4096 : 2880;
    if (max($size) > $limit && is_wp_error($editor->resize($limit, $limit, false))) { return $upload; }
    $editor->set_quality(84);
    $name = wp_unique_filename(dirname($file), pathinfo($file, PATHINFO_FILENAME) . '.webp');
    $saved = $editor->save(dirname($file) . '/' . $name, 'image/webp');
    if (is_wp_error($saved) || empty($saved['path']) || !is_file($saved['path'])) { return $upload; }
    // Only replace the new upload after a verified, smaller conversion. Keep existing media untouched.
    $info = wp_getimagesize($saved['path']);
    if (!$info || ($info['mime'] ?? '') !== 'image/webp' || filesize($saved['path']) >= filesize($file)) {
        wp_delete_file($saved['path']);
        return $upload;
    }
    $upload['file'] = $saved['path'];
    $upload['url'] = trailingslashit(dirname($upload['url'])) . rawurlencode(basename($saved['path']));
    $upload['type'] = 'image/webp';
    wp_delete_file($file);
    return $upload;
}
add_filter('wp_handle_upload', 'sphere_optimize_uploaded_image', 20);
add_filter('wp_handle_sideload', 'sphere_optimize_uploaded_image', 20);
