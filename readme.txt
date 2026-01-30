=== Pago QR Yape Plin ===
Contributors: tunombre
Tags: woocommerce, payment, qr, yape, plin, peru
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Método de pago mediante código QR para Yape y Plin en WooCommerce.

== Descripción ==

Este plugin agrega un método de pago QR para WooCommerce, permitiendo a tus clientes pagar usando Yape o Plin escaneando un código QR.

**Características:**

* Muestra tu código QR personalizado en el checkout
* El cliente escanea el QR con Yape o Plin
* El pedido queda en estado "En espera" hasta confirmar el pago
* Configuración sencilla desde el panel de WooCommerce
* Compatible con la última versión de WooCommerce
* Compatible con HPOS (High-Performance Order Storage)

== Instalación ==

1. Sube la carpeta `pago-qr-yape-plin` al directorio `/wp-content/plugins/`
2. Activa el plugin desde el menú 'Plugins' en WordPress
3. Ve a WooCommerce > Ajustes > Pagos
4. Activa "Pago QR Yape/Plin" y haz clic en "Gestionar"
5. Sube tu imagen del código QR y configura las opciones

== Configuración ==

1. **Título**: El nombre que verán los clientes (ej: "Pago con QR Yape/Plin")
2. **Descripción**: Instrucciones breves para el cliente
3. **Imagen QR**: Sube la imagen de tu código QR de Yape o Plin
4. **Instrucciones**: Texto que aparece después de completar el pedido
5. **Estado del pedido**: Estado inicial del pedido (recomendado: "En espera")

== Preguntas Frecuentes ==

= ¿Necesito WooCommerce? =
Sí, este plugin requiere WooCommerce para funcionar.

= ¿Cómo obtengo mi código QR? =
Puedes generar tu código QR desde la app de Yape o Plin en tu celular.

= ¿Cómo confirmo los pagos? =
Debes verificar manualmente los pagos recibidos y cambiar el estado del pedido a "Procesando" o "Completado".

== Changelog ==

= 1.0.0 =
* Versión inicial del plugin
* Soporte para código QR personalizado
* Integración completa con WooCommerce checkout
