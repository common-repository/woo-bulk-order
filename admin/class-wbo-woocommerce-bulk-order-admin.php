<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://quanticedgesolutions.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Bulk_Order
 * @subpackage Woocommerce_Bulk_Order/admin
 */

class WBO_Woocommerce_Bulk_Order_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', array($this, 'wbo_admin_menus'));
		add_action('wp_ajax_wbo_save_bulk_order_settings', array($this, 'wbo_save_bulk_order_settings'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-bulk-order-admin.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-about', plugin_dir_url(__FILE__) . 'css/about.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-bulk-order', plugin_dir_url(__FILE__) . 'css/bulk-order.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-bulk-order-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_script("ajax-handler.js", plugin_dir_url(__FILE__) . 'js/ajax-handler.js', array('jquery'), '1.0',  true);
		wp_localize_script('ajax-handler.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	}


	/**
	 * Add Admin Menus
	 * Author: Aakash
	 * Dt: 17/09/2024
	 */
	public function wbo_admin_menus()
	{
		$image_url = esc_url(plugin_dir_url(__FILE__) . '/image/quanticedge.png');
		add_menu_page("quanticedge", "QuanticEdge", "manage_options", "quanticedge", array($this, "wbo_welcome_content"),  $image_url, 26);
		add_submenu_page("quanticedge", "About", "About", "manage_options", "quanticedge");
		add_submenu_page('quanticedge', 'Bulk Order', 'Bulk Order', 'manage_options', 'qc-bulk-order', array($this, 'wbo_bulk_order'));
	}


	public function wbo_welcome_content()
	{
		include plugin_dir_path(__FILE__) . '/partials/about.php';
	}

	public function wbo_bulk_order()
	{
		include plugin_dir_path(__FILE__) . '/partials/bulk-order.php';
	}

	/**
	 * Extract and sanitize form data , Append new data to the existing settings , save updated options
	 * Author: Aakash
	 * Dt: 17/09/2024
	 */
	public function wbo_save_bulk_order_settings()
	{

		if (isset($_POST['form_data'])) {
			parse_str($_POST['form_data'], $form_data);

			$rdo_sort_by = sanitize_text_field($form_data['rdo_sort_by']);
			$rdo_sort_order = sanitize_text_field($form_data['rdo_sort_order']);
			$txtProductIds = sanitize_text_field($form_data['txtProductIds']);
			$rdo_add_cat_filter = sanitize_text_field($form_data['rdo_add_cat_filter']);
			$txtCatLabel = sanitize_text_field($form_data['txtCatLabel']);

			$existing_settings = get_option('bulk_order_settings', array());

			$existing_settings[] = array(
				'sort_by' => $rdo_sort_by,
				'sort_order' => $rdo_sort_order,
				'product_ids' => $txtProductIds,
				'add_cat_filter' => $rdo_add_cat_filter,
				'cat_label' => $txtCatLabel,
			);

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $wpdb->posts WHERE post_title = %s",
					'Order Now'
				)
			);

			update_option('bulk_order_settings', $existing_settings);

			wp_send_json_success('Settings saved successfully.');
		} else {
			wp_send_json_error('No data received.');
		}

		wp_die();
	}
}
