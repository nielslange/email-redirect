<?php
/**
 * Email filter class
 *
 * @package Email_Redirect
 */

declare( strict_types=1 );
defined( 'ABSPATH' ) || exit;

/**
 * Email redirection filter class
 */
class Email_Filter {

	/**
	 * Initialize email filter
	 */
	public function __construct() {
		add_filter( 'wp_mail', [ $this, 'redirect_emails' ], 10, 1 );
	}

	/**
	 * Override email recipients
	 *
	 * @param array $atts The email attributes.
	 * @return array The email attributes.
	 */
	public function redirect_emails( array $atts ): array {
		$addresses = get_option( 'email_redirect_addresses', [] );
		if ( empty( $addresses ) ) {
			return $atts;
		}

		$atts['to'] = $addresses;
		return $atts;
	}
}
