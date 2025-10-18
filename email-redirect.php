<?php
/**
 * Plugin Name: Email Redirect
 * Description: Enables users to redirect emails to a custom email address instead of the original recipient.
 * Version: 1.1
 * Author: Niels Lange
 * Author URI: https://nielslange.de
 * Text Domain: email-redirect
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package email-redirect
 */

declare( strict_types=1 );
defined( 'ABSPATH' ) || exit;

define( 'PLUGIN_DATA', get_plugin_data( __FILE__ ) );
define( 'PLUGIN_VERSION', PLUGIN_DATA['Version'] );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ADMIN_CSS_URL', PLUGIN_URL . 'assets/css/admin.css' );
define( 'ADMIN_JS_URL', PLUGIN_URL . 'assets/js/admin.js' );

require_once plugin_dir_path( __FILE__ ) . 'inc/class-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/class-email-filter.php';

// Initialize the plugin.
add_action( 'init', 'email_redirect_init' );

/**
 * Initialize the Email Redirect plugin
 */
function email_redirect_init(): void {
	// Initialize admin settings.
	if ( is_admin() ) {
		new Admin_Settings();
	}

	// Initialize email filter.
	new Email_Filter();
}
