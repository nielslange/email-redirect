<?php
/**
 * Admin settings class
 *
 * @package Email_Redirect
 */

declare( strict_types=1 );
defined( 'ABSPATH' ) || exit;

/**
 * Admin settings management class
 */
class Admin_Settings {

	/**
	 * Initialize admin settings
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook ): void {
		if ( 'settings_page_email-redirect' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'email-redirect-admin',
			ADMIN_CSS_URL,
			[],
			PLUGIN_DATA['Version'],
		);

		wp_enqueue_script(
			'email-redirect-admin',
			ADMIN_JS_URL,
			[],
			PLUGIN_DATA['Version'],
			true
		);
	}

	/**
	 * Add settings page to WordPress admin menu
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_options_page(
			__( 'Email Redirect', 'email-redirect' ),
			__( 'Email Redirect', 'email-redirect' ),
			'manage_options',
			'email-redirect',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register settings
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			'email_redirect_settings',
			'email_redirect_addresses',
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_addresses' ],
				'default'           => [],
			]
		);

		add_settings_section(
			'email_redirect_main',
			null,
			[ $this, 'render_section_description' ],
			'email-redirect'
		);

		add_settings_field(
			'email_redirect_addresses',
			esc_html__( 'Email Addresses', 'email-redirect' ),
			[ $this, 'render_addresses_field' ],
			'email-redirect',
			'email_redirect_main'
		);
	}

	/**
	 * Render section description
	 *
	 * @return void
	 */
	public function render_section_description(): void {
		printf(
			'<p>%s</p>',
			esc_html__( 'Redirect all outgoing emails to specific addresses for testing purposes.', 'email-redirect' )
		);
	}


	/**
	 * Sanitize email addresses array
	 *
	 * @param array $addresses The email addresses to sanitize.
	 * @return array The sanitized email addresses.
	 */
	public function sanitize_addresses( array $addresses ): array {
		if ( ! is_array( $addresses ) ) {
			return [];
		}

		$sanitized = [];
		foreach ( $addresses as $address ) {
			$clean_address = sanitize_email( trim( $address ) );
			if ( is_email( $clean_address ) ) {
				$sanitized[] = $clean_address;
			}
		}

		return array_unique( $sanitized );
	}

	/**
	 * Render email addresses field with add/remove functionality
	 */
	public function render_addresses_field(): void {
		$addresses = get_option( 'email_redirect_addresses', [] );
		if ( empty( $addresses ) ) {
			$addresses = [ '' ];
		}

		?>
		<div id="email-redirect-addresses-container">
		<?php foreach ( $addresses as $index => $address ) : ?>
				<div class="email-address-field" data-index="<?php echo esc_attr( $index ); ?>">
					<input 
						type="email" 
						name="email_redirect_addresses[<?php echo esc_attr( $index ); ?>]" 
						value="<?php echo esc_attr( $address ); ?>" 
						class="regular-text email-address-input"
						placeholder="<?php esc_attr_e( 'Enter email address', 'email-redirect' ); ?>"
					>
					<button type="button" class="button remove-email-btn" <?php echo count( $addresses ) === 1 ? 'style="display:none;"' : ''; ?>>
						<span class="dashicons dashicons-trash"></span>
					</button>
				</div>
			<?php endforeach; ?>
		</div>
		
		<button type="button" id="add-email-btn" class="button button-secondary">
			<?php esc_html_e( '+ Add Email Address', 'email-redirect' ); ?>
		</button>
		<?php
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
			<?php
			settings_fields( 'email_redirect_settings' );
			do_settings_sections( 'email-redirect' );
			submit_button( __( 'Save Settings', 'email-redirect' ) );
			?>
			</form>
		</div>
			<?php
	}
}