<?php
/**
 * Plugin Name: Sphere — Correo de Google Workspace
 * Description: Envío autenticado por Google con credenciales privadas fuera del sitio público.
 * Author: Estudio Ideamos
 * Version: 1.0.0
 */
if (!defined('ABSPATH')) { exit; }
add_action('phpmailer_init', function ($mailer) {
    // Staging installations without this private setting retain their own mail configuration.
    if (!defined('SPHERE_SMTP_CONFIG')) { return; }
    $config = json_decode((string) @file_get_contents(SPHERE_SMTP_CONFIG), true);
    if (!is_array($config) || !is_email($config['username'] ?? '') || empty($config['password'])) {
        throw new \PHPMailer\PHPMailer\Exception('Email service configuration is unavailable.');
    }
    $mailer->isSMTP();
    $mailer->Host = 'smtp.gmail.com';
    $mailer->Port = 587;
    $mailer->SMTPSecure = 'tls';
    $mailer->SMTPAuth = true;
    $mailer->Username = $config['username'];
    $mailer->Password = $config['password'];
    $mailer->Timeout = 15;
    $mailer->SMTPDebug = 0;
    $mailer->setFrom($config['username'], 'Sphere Design', false);
    $mailer->Sender = $config['username'];
    // Reply-To supplied by the contact form is intentionally preserved.
}, 100);
