<?php
/**
 * Plugin Name: Ideamos — Visibilidad del menú
 * Description: Elegí qué opciones del menú de administración mostrar, por rol, sin modificar permisos.
 * Version: 1.0.0
 * Author: Estudio Ideamos
 * Author URI: https://ideamos.com.ar/
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) { exit; }
final class Ideamos_Menu_Visibility {
    const OPTION = 'ideamos_menu_visibility';
    const PAGE = 'ideamos-menu-visibility';
    private static $catalog = [];
    static function boot() {
        add_action('admin_menu', [__CLASS__, 'register']);
        add_action('admin_menu', [__CLASS__, 'filter_menu'], PHP_INT_MAX);
        add_action('admin_post_ideamos_menu_save', [__CLASS__, 'save']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
            array_unshift($links, '<a href="' . esc_url(admin_url('admin.php?page=' . self::PAGE)) . '">Configurar menú</a>');
            return $links;
        });
    }
    static function register() {
        add_menu_page('Visibilidad del menú', 'Visibilidad del menú', 'manage_options', self::PAGE, [__CLASS__, 'render'], 'dashicons-visibility', 99);
    }
    static function settings() {
        return wp_parse_args(get_option(self::OPTION, []), ['enabled' => true, 'roles' => array_keys(wp_roles()->roles), 'hidden' => []]);
    }
    static function label($text) {
        $text = preg_replace('~<span\b[^>]*>.*?</span>~is', '', $text);
        return trim(wp_strip_all_tags($text));
    }
    static function key($parent, $slug) { return hash('sha256', $parent . "\n" . $slug); }
    static function filter_menu() {
        global $menu, $submenu;
        foreach ((array) $menu as $item) {
            if (!current_user_can($item[1]) || empty($item[0]) || empty($item[2]) || $item[2] === self::PAGE || strpos($item[2], 'separator') === 0) { continue; }
            $slug = (string) $item[2];
            $key = self::key('', $slug);
            self::$catalog[$key] = ['parent' => '', 'slug' => $slug, 'label' => self::label($item[0])];
            foreach ($submenu[$slug] ?? [] as $child) {
                if (!current_user_can($child[1]) || empty($child[0]) || empty($child[2]) || $child[2] === self::PAGE) { continue; }
                self::$catalog[self::key($slug, $child[2])] = ['parent' => $slug, 'slug' => (string) $child[2], 'label' => self::label($child[0])];
            }
        }
        $settings = self::settings();
        if (!$settings['enabled'] || !array_intersect(wp_get_current_user()->roles, $settings['roles'])) { return; }
        foreach (self::$catalog as $key => $item) {
            if (in_array($key, $settings['hidden'], true) && $item['parent']) { remove_submenu_page($item['parent'], $item['slug']); }
        }
        foreach (self::$catalog as $key => $item) {
            if (in_array($key, $settings['hidden'], true) && !$item['parent']) { remove_menu_page($item['slug']); }
        }
    }
    static function render() {
        if (!current_user_can('manage_options')) { wp_die('No tenés permiso para administrar esta configuración.'); }
        $settings = self::settings();
        // The save handler accepts only keys from the current administrator's rendered menu.
        set_transient('ideamos_menu_catalog_' . get_current_user_id(), array_keys(self::$catalog), HOUR_IN_SECONDS);
        ?>
        <div class="wrap imv"><header class="imv-head"><span>ESTUDIO IDEAMOS · ADMINISTRACIÓN</span><h1>Un menú más simple.</h1><p>Dejá a la vista las herramientas que se usan todos los días y ocultá las demás.</p></header>
        <?php if (isset($_GET['saved'])): ?><div class="notice notice-success"><p>Configuración guardada.</p></div><?php endif; ?>
        <aside class="imv-warning" aria-labelledby="imv-warning-title"><h2 id="imv-warning-title">Configuración administrada por Estudio Ideamos</h2><p>Este menú fue configurado para facilitar la gestión del sitio y evitar cambios accidentales. <strong>Por favor, mantené las opciones tal como están y no modifiques ni restaures esta configuración sin consultar antes con Estudio Ideamos.</strong></p><p>Si necesitás acceder a alguna herramienta que no aparece, contactanos para que podamos habilitarla y orientarte.</p></aside>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="ideamos_menu_save"><?php wp_nonce_field('ideamos_menu_save'); ?>
        <section class="imv-card"><h2>1. Elegí a quién se aplica</h2><p>Las opciones ocultas desaparecen para los roles seleccionados. Si compartís el mismo usuario administrador con la clienta, ambos verán el mismo menú.</p>
        <label class="imv-enabled"><input type="checkbox" name="enabled" value="1" <?php checked($settings['enabled']); ?>> Aplicar la configuración</label>
        <div class="imv-roles"><?php foreach (wp_roles()->roles as $role => $data): ?><label><input type="checkbox" name="roles[]" value="<?php echo esc_attr($role); ?>" <?php checked(in_array($role, $settings['roles'], true)); ?>> <?php echo esc_html(translate_user_role($data['name'])); ?></label><?php endforeach; ?></div></section>
        <section class="imv-card"><h2>2. Elegí qué queda visible</h2><p>Marcado = visible. Desmarcado = oculto. Al ocultar una sección, también desaparece su submenú.</p><div class="imv-grid">
        <?php foreach (self::$catalog as $key => $item): if ($item['parent']) { continue; } ?>
        <div class="imv-group"><label class="imv-parent"><input type="checkbox" name="visible[<?php echo esc_attr($key); ?>]" value="1" <?php checked(!in_array($key, $settings['hidden'], true)); ?>> <?php echo esc_html($item['label']); ?></label>
        <?php foreach (self::$catalog as $child_key => $child): if ($child['parent'] !== $item['slug']) { continue; } ?>
        <label class="imv-child"><input type="checkbox" name="visible[<?php echo esc_attr($child_key); ?>]" value="1" <?php checked(!in_array($child_key, $settings['hidden'], true)); ?>> <?php echo esc_html($child['label']); ?></label>
        <?php endforeach; ?></div><?php endforeach; ?></div></section>
        <aside class="imv-info"><strong>Siempre podés volver.</strong> “Visibilidad del menú” permanece disponible para administradores, aunque ocultes Ajustes o Plugins. Este plugin organiza el menú: los enlaces directos y los permisos de WordPress siguen funcionando. Para impedir accesos, se necesitan permisos por rol.</aside>
        <div class="imv-actions"><button class="button button-primary button-hero" type="submit">Guardar visibilidad</button><button class="button button-secondary" type="submit" name="reset" value="1">Restaurar todo visible</button></div>
        </form></div>
        <style>.imv{max-width:1120px;color:#24302a}.imv-head{padding:30px 34px;background:#1c2923;border-radius:14px;color:#fff;margin:24px 0}.imv-head span{font-size:11px;letter-spacing:1.8px;color:#d1ddbf}.imv-head h1{color:#fff;font-size:34px;line-height:1.2;margin:14px 0;padding:0}.imv .notice p{color:#24302a}.imv-head p{font-size:16px;color:#e2e9e0}.imv-card{background:#fff;border:1px solid #dce2dc;border-radius:12px;padding:26px;margin:20px 0}.imv h2{font-size:20px;margin:0 0 10px}.imv p{line-height:1.6}.imv-roles{display:flex;gap:16px;flex-wrap:wrap;margin-top:18px}.imv-roles label{background:#f0f4ee;padding:10px 14px;border-radius:6px}.imv-enabled{display:block;font-weight:600;margin-top:18px}.imv-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-top:22px}.imv-group{border:1px solid #dae2d8;border-radius:9px;overflow:hidden;padding-bottom:10px}.imv-parent{display:block;background:#f0f4ee;font-weight:600;padding:15px}.imv-child{display:block;padding:9px 15px 5px 30px;line-height:1.5}.imv input:focus-visible{outline:3px solid #2271b1;outline-offset:3px}.imv-warning{background:#fff8e8;border:1px solid #e4cd94;border-left:4px solid #9a691d;border-radius:10px;padding:22px 26px;margin:20px 0;color:#45351c}.imv-warning h2{color:#45351c}.imv-warning p:last-child{margin-bottom:0}.imv-info{background:#eaf0e5;border-left:4px solid #65784e;padding:20px;line-height:1.7}.imv-actions{display:flex;align-items:center;gap:16px;flex-wrap:wrap;padding:22px 0 35px}@media(max-width:600px){.imv-head,.imv-card{padding:20px}.imv-grid{grid-template-columns:1fr}}</style>
        <?php
    }
    static function save() {
        if (!current_user_can('manage_options')) { wp_die('Acceso no autorizado.', '', ['response' => 403]); }
        check_admin_referer('ideamos_menu_save');
        if (!empty($_POST['reset'])) {
            delete_option(self::OPTION);
        } else {
            $catalog = get_transient('ideamos_menu_catalog_' . get_current_user_id());
            if (!is_array($catalog)) { wp_die('La sesión del formulario venció. Volvé a abrir Visibilidad del menú e intentá de nuevo.'); }
            $visible = isset($_POST['visible']) && is_array($_POST['visible']) ? array_keys($_POST['visible']) : [];
            $roles = isset($_POST['roles']) && is_array($_POST['roles']) ? array_intersect(array_keys(wp_roles()->roles), $_POST['roles']) : [];
            $previous = self::settings();
            $hidden = array_unique(array_merge(array_diff($previous['hidden'], $catalog), array_diff($catalog, $visible)));
            update_option(self::OPTION, ['enabled' => !empty($_POST['enabled']), 'roles' => array_values($roles), 'hidden' => array_values($hidden)], false);
        }
        wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE . '&saved=1'));
        exit;
    }
}
Ideamos_Menu_Visibility::boot();
