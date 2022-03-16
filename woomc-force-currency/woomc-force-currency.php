<?php
/**
 * Plugin Name: WooCommerce Multi-currency - force currency
 * Plugin URI: https://github.com/TIVWP/woomc-force-currency
 * Version: 1.0.2
 * Description: Set currency upon login by the user's country
 * Author: TIV.NET INC
 * Author URI: https://profiles.wordpress.org/tivnetinc/
 */

1 && \add_action( 'woocommerce_multicurrency_loaded', function () {
	\add_action(
		'wp_login',
		function ( $user_login, $user ) {

			if ( ! \did_action( 'woocommerce_init' ) ) {
				// WooCommerce not initialized. Stop.
				return;
			}

			try {

				$customer = new \WC_Customer( $user->ID );
				$country  = $customer->get_billing_country();

				$currency_to_force = '';
				// USA and Canada will get JPY (CHANGE IT!)
				if ( in_array( $country, array( 'US', 'CA' ), true ) ) {
					// Change this:
					$currency_to_force = 'JPY';
				}

				$active_currency = \get_woocommerce_currency();
				if ( $currency_to_force && $active_currency !== $currency_to_force ) {
					\WOOMC\Currency\Detector::set_currency_cookie( $currency_to_force, true );

					// Must redirect somewhere (home, shop, etc.) and stop further processing.
					\wp_safe_redirect( '/shop/' );
					// To test:
					// \wp_safe_redirect( '/?currency=' . $currency_to_force );

					// MUST EXIT AFTER REDIRECT!
					exit();
				}

			} catch ( \Exception $e ) {
				// Whatever is wrong, we cannot continue.
				return;
			}
		},
		// Important: hook EARLY!
		\WOOMC\App::HOOK_PRIORITY_EARLY,
		2
	);
} );

