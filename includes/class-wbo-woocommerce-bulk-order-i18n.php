<?php

/**
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://quanticedgesolutions.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Bulk_Order
 * @subpackage Woocommerce_Bulk_Order/includes
 */

class WBO_Woocommerce_Bulk_Order_i18n
{


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain(
			'woocommerce-bulk-order',
			false,
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
		);
	}
}
