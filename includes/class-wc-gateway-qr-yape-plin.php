<?php
/**
 * WooCommerce Gateway QR Yape Plin
 *
 * @class WC_Gateway_QR_Yape_Plin
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_Gateway_QR_Yape_Plin extends WC_Payment_Gateway {

    /**
     * Constructor del gateway
     */
    public function __construct() {
        $this->id                 = 'qr_yape_plin';
        $this->icon               = '';
        $this->has_fields         = true;
        $this->method_title       = __('Pago QR Yape/Plin', 'pago-qr-yape-plin');
        $this->method_description = __('Permite a los clientes pagar escaneando un código QR con Yape o Plin.', 'pago-qr-yape-plin');

        // Cargar configuración
        $this->init_form_fields();
        $this->init_settings();

        // Definir variables del usuario
        $this->title        = $this->get_option('title');
        $this->description  = $this->get_option('description');
        $this->instructions = $this->get_option('instructions');
        $this->qr_image     = $this->get_option('qr_image');
        $this->order_status = $this->get_option('order_status', 'on-hold');

        // Acciones
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);

        // Cargar estilos y scripts
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    /**
     * Inicializar campos del formulario de configuración
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __('Activar/Desactivar', 'pago-qr-yape-plin'),
                'type'    => 'checkbox',
                'label'   => __('Activar Pago QR Yape/Plin', 'pago-qr-yape-plin'),
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => __('Título', 'pago-qr-yape-plin'),
                'type'        => 'text',
                'description' => __('Título que el cliente verá durante el checkout.', 'pago-qr-yape-plin'),
                'default'     => __('Pago con QR (Yape/Plin)', 'pago-qr-yape-plin'),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __('Descripción', 'pago-qr-yape-plin'),
                'type'        => 'textarea',
                'description' => __('Descripción que el cliente verá durante el checkout.', 'pago-qr-yape-plin'),
                'default'     => __('Escanea el código QR con tu app de Yape o Plin para realizar el pago.', 'pago-qr-yape-plin'),
                'desc_tip'    => true,
            ),
            'qr_image' => array(
                'title'       => __('Imagen del Código QR', 'pago-qr-yape-plin'),
                'type'        => 'qr_image_upload',
                'description' => __('Sube la imagen de tu código QR de Yape o Plin.', 'pago-qr-yape-plin'),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'instructions' => array(
                'title'       => __('Instrucciones', 'pago-qr-yape-plin'),
                'type'        => 'textarea',
                'description' => __('Instrucciones que se mostrarán en la página de agradecimiento y en el email.', 'pago-qr-yape-plin'),
                'default'     => __('Por favor realiza el pago escaneando el código QR. Tu pedido será procesado una vez confirmemos el pago.', 'pago-qr-yape-plin'),
                'desc_tip'    => true,
            ),
            'order_status' => array(
                'title'       => __('Estado del pedido', 'pago-qr-yape-plin'),
                'type'        => 'select',
                'description' => __('Estado que tendrá el pedido después de realizar la compra.', 'pago-qr-yape-plin'),
                'default'     => 'on-hold',
                'desc_tip'    => true,
                'options'     => array(
                    'on-hold'    => __('En espera', 'pago-qr-yape-plin'),
                    'pending'    => __('Pendiente de pago', 'pago-qr-yape-plin'),
                    'processing' => __('Procesando', 'pago-qr-yape-plin'),
                )
            ),
        );
    }

    /**
     * Generar campo personalizado para subir imagen QR
     */
    public function generate_qr_image_upload_html($key, $data) {
        $field_key = $this->get_field_key($key);
        $value = $this->get_option($key);

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <div class="qr-image-upload-wrapper">
                        <input type="hidden"
                               name="<?php echo esc_attr($field_key); ?>"
                               id="<?php echo esc_attr($field_key); ?>"
                               value="<?php echo esc_attr($value); ?>" />

                        <div id="qr-image-preview" style="margin-bottom: 10px;">
                            <?php if ($value): ?>
                                <img src="<?php echo esc_url($value); ?>" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;" />
                            <?php endif; ?>
                        </div>

                        <button type="button" class="button" id="qr-image-upload-btn">
                            <?php _e('Subir Imagen QR', 'pago-qr-yape-plin'); ?>
                        </button>

                        <?php if ($value): ?>
                            <button type="button" class="button" id="qr-image-remove-btn" style="margin-left: 5px;">
                                <?php _e('Eliminar', 'pago-qr-yape-plin'); ?>
                            </button>
                        <?php endif; ?>

                        <p class="description"><?php echo wp_kses_post($data['description']); ?></p>
                    </div>
                </fieldset>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Scripts para el admin
     */
    public function admin_scripts($hook) {
        if ('woocommerce_page_wc-settings' !== $hook) {
            return;
        }

        if (!isset($_GET['section']) || $_GET['section'] !== $this->id) {
            return;
        }

        wp_enqueue_media();

        $inline_script = "
        jQuery(document).ready(function($) {
            var mediaUploader;

            $('#qr-image-upload-btn').on('click', function(e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: '" . esc_js(__('Seleccionar imagen QR', 'pago-qr-yape-plin')) . "',
                    button: {
                        text: '" . esc_js(__('Usar esta imagen', 'pago-qr-yape-plin')) . "'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });

                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#" . esc_js($this->get_field_key('qr_image')) . "').val(attachment.url);
                    $('#qr-image-preview').html('<img src=\"' + attachment.url + '\" style=\"max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px;\" />');

                    if ($('#qr-image-remove-btn').length === 0) {
                        $('#qr-image-upload-btn').after('<button type=\"button\" class=\"button\" id=\"qr-image-remove-btn\" style=\"margin-left: 5px;\">" . esc_js(__('Eliminar', 'pago-qr-yape-plin')) . "</button>');
                    }
                });

                mediaUploader.open();
            });

            $(document).on('click', '#qr-image-remove-btn', function(e) {
                e.preventDefault();
                $('#" . esc_js($this->get_field_key('qr_image')) . "').val('');
                $('#qr-image-preview').html('');
                $(this).remove();
            });
        });
        ";

        wp_add_inline_script('jquery', $inline_script);
    }

    /**
     * Scripts para el frontend
     */
    public function payment_scripts() {
        if (!is_checkout()) {
            return;
        }

        wp_enqueue_style(
            'qr-yape-plin-checkout',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/checkout.css',
            array(),
            '1.0.0'
        );
    }

    /**
     * Mostrar campos de pago en el checkout
     */
    public function payment_fields() {
        if ($this->description) {
            echo wpautop(wptexturize($this->description));
        }

        if ($this->qr_image) {
            ?>
            <div class="qr-payment-container">
                <div class="qr-image-wrapper">
                    <img src="<?php echo esc_url($this->qr_image); ?>"
                         alt="<?php esc_attr_e('Código QR para pago', 'pago-qr-yape-plin'); ?>"
                         class="qr-payment-image" />
                </div>
                <p class="qr-instruction">
                    <?php _e('Escanea este código QR con tu app de Yape o Plin para realizar el pago.', 'pago-qr-yape-plin'); ?>
                </p>
            </div>
            <?php
        } else {
            ?>
            <p class="qr-no-image">
                <?php _e('El código QR no está configurado. Por favor contacta al administrador.', 'pago-qr-yape-plin'); ?>
            </p>
            <?php
        }
    }

    /**
     * Procesar el pago
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        // Establecer el estado del pedido
        $order->update_status($this->order_status, __('Esperando confirmación de pago QR (Yape/Plin).', 'pago-qr-yape-plin'));

        // Reducir stock
        wc_reduce_stock_levels($order_id);

        // Vaciar carrito
        WC()->cart->empty_cart();

        // Retornar éxito y redireccionar
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url($order)
        );
    }

    /**
     * Mostrar instrucciones en la página de agradecimiento
     */
    public function thankyou_page($order_id) {
        if ($this->instructions) {
            echo wpautop(wptexturize($this->instructions));
        }

        if ($this->qr_image) {
            ?>
            <div class="qr-thankyou-container">
                <h3><?php _e('Código QR para el pago', 'pago-qr-yape-plin'); ?></h3>
                <div class="qr-image-wrapper">
                    <img src="<?php echo esc_url($this->qr_image); ?>"
                         alt="<?php esc_attr_e('Código QR para pago', 'pago-qr-yape-plin'); ?>"
                         class="qr-payment-image" />
                </div>
                <p><?php _e('Si aún no has realizado el pago, escanea el código QR con tu app de Yape o Plin.', 'pago-qr-yape-plin'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Agregar instrucciones al email
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false) {
        if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method()) {
            echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
        }
    }
}
