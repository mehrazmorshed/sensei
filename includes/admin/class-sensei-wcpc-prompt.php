<?php
/**
 * File containing Sensei_WCPC_Prompt class.
 *
 * @package Sensei\Admin
 * @since   3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Exit survey upon plugin deactivation.
 *
 * @since 3.7.0
 */
class Sensei_WCPC_Prompt {
	const DISMISS_PROMPT_OPTION = 'sensei_dismiss_wcpc_prompt';

	/**
	 * Sensei_WCPC_Prompt constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'wcpc_prompt' ] );
		add_action( 'admin_init', [ $this, 'dismiss_prompt' ] );
	}

	/**
	 * WooCommerce Paid Courses plugin prompt.
	 *
	 * @access private
	 */
	public function wcpc_prompt() {
		if (
			get_option( self::DISMISS_PROMPT_OPTION, 0 )
			|| ! Sensei_Utils::is_woocommerce_active()
			|| ! current_user_can( 'manage_sensei' )
		) {
			return;
		}

		$dismiss_url = add_query_arg( 'sensei_dismiss_wcpc_prompt', '1' );
		$dismiss_url = wp_nonce_url( $dismiss_url, 'sensei_dismiss_wcpc_prompt' );

		?>
		<div class="notice notice-info is-dismissible">
			<p><?php esc_html_e( 'Sell and monetize your courses by installing a WooCommerce extension.', 'sensei-lms' ); ?> <a href="https://woocommerce.com/products/woocommerce-paid-courses/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'sensei-lms' ); ?></a></p>
			<p><a href="#" class="button-primary"><?php esc_html_e( 'Install now', 'sensei-lms' ); ?></a></p>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="notice-dismiss sensei-dismissible-link">
				<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'sensei-lms' ); ?></span>
			</a>
		</div>
		<?php
	}

	/**
	 * Dismiss WCPC prompt.
	 *
	 * @access private
	 */
	public function dismiss_prompt() {
		if (
			isset( $_GET['sensei_dismiss_wcpc_prompt'] )
			&& '1' === $_GET['sensei_dismiss_wcpc_prompt']
			&& isset( $_GET['_wpnonce'] )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Don't touch the nonce.
			&& wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'sensei_dismiss_wcpc_prompt' )
			&& current_user_can( 'manage_sensei' )
		) {
			update_option( self::DISMISS_PROMPT_OPTION, 1 );
		}
	}
}
