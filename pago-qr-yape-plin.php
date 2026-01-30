<?php
/**
 * Plugin Name: Pago QR Yape Plin
 * Plugin URI: https://example.com/pago-qr-yape-plin
 * Description: Método de pago mediante código QR para Yape y Plin. El cliente escanea el QR para realizar el pago.
 * Version: 1.0.0
 * Author: Tu Nombre
 * Author URI: https://example.com
 * Text Domain: pago-qr-yape-plin
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 4.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Verificar que WooCommerce esté activo
add_action('plugins_loaded', 'pago_qr_yape_plin_init', 11);

function pago_qr_yape_plin_init() {
    if (!class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', 'pago_qr_yape_plin_woocommerce_notice');
        return;
    }

    // Incluir la clase del gateway
    include_once plugin_dir_path(__FILE__) . 'includes/class-wc-gateway-qr-yape-plin.php';

    // Agregar el gateway a WooCommerce
    add_filter('woocommerce_payment_gateways', 'pago_qr_yape_plin_add_gateway');
}

function pago_qr_yape_plin_woocommerce_notice() {
    ?>
    <div class="error">
        <p><?php _e('Pago QR Yape Plin requiere WooCommerce para funcionar. Por favor instala y activa WooCommerce.', 'pago-qr-yape-plin'); ?></p>
    </div>
    <?php
}

function pago_qr_yape_plin_add_gateway($gateways) {
    $gateways[] = 'WC_Gateway_QR_Yape_Plin';
    return $gateways;
}

// Declarar compatibilidad con HPOS (High-Performance Order Storage)
add_action('before_woocommerce_init', function() {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});
