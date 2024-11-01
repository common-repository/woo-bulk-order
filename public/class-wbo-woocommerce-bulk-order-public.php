<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://quanticedgesolutions.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Bulk_Order
 * @subpackage Woocommerce_Bulk_Order/public
 */

/**
 * The public-facing functionality of the plugin.
 * 
 * @package    Woocommerce_Bulk_Order
 * @subpackage Woocommerce_Bulk_Order/public
 * @author     https://quanticedgesolutions.com/ <info@quanticedge.co.in>
 */
class WBO_Woocommerce_Bulk_Order_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode('wbo_woo_bulk_order', array($this, 'wbo_woo_bulk_order_shortcode'));

		/**
		 * Initialize AJAX handler
		 * Author: Aakash
		 * Dt: 17/09/2024
		 */
		add_action('wp_ajax_wbo_get_products', array($this, 'wbo_get_products'));
		add_action('wp_ajax_nopriv_wbo_get_products', array($this, 'wbo_get_products'));
		add_action('wp_ajax_wbo_add_to_cart', array($this, 'wbo_add_to_cart'));
		add_action('wp_ajax_nopriv_wbo_add_to_cart', array($this, 'wbo_add_to_cart'));
		add_action('wp_ajax_wbo_woocommerce_get_refreshed_fragments', array($this, 'wbo_woocommerce_get_refreshed_fragments_handler'));
		add_action('wp_ajax_nopriv_wbo_woocommerce_get_refreshed_fragments', array($this, 'wbo_woocommerce_get_refreshed_fragments_handler'));
	}

	/**wbo_
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/custom.css', array(), $this->version, 'all');
		wp_enqueue_style('jsgrid-style', 'https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css');
		wp_enqueue_style('jsgrid-theme', 'https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-bulk-order-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script('jsgrid', 'https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js', array('jquery'), null, true);
		wp_enqueue_script("jsgrid.js", plugin_dir_url(__FILE__) . 'js/jsgrid.js', array('jquery'), '1.0',  true);
		wp_localize_script($this->plugin_name, 'wbo_params', array(
			'loader_image' => plugins_url('images/loading.gif', __FILE__),
			'complete_image' => plugins_url('images/complete.png', __FILE__),
			'ajax_url' => admin_url('admin-ajax.php'),
		));
	}

	/**
	 * Product category functionality.
	 * Author: Aakash
	 * Dt: 17/09/2024
	 */
	public function wbo_woo_bulk_order_shortcode() {
		ob_start();
		$settings = get_option('bulk_order_settings', array());

		if (is_array($settings) && !empty($settings)) {
			$latest_settings = end($settings);
		} else {
			$latest_settings = array();
		}

		$add_cat_filter = isset($latest_settings['add_cat_filter']) ? $latest_settings['add_cat_filter'] : 'No';
		$cat_label = isset($latest_settings['cat_label']) ? $latest_settings['cat_label'] : '';
		?><div id="message-container" style="display:none; background-color: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 10px; border: 1px solid #d6e9c6; border-radius: 4px;"></div>

		<div>
			<?php if ($add_cat_filter === 'Yes'): ?>
				<?php echo esc_html($cat_label); ?>
				<select id="category-filter">
					<option value=""><?php _e('Select Category', 'woocommerce-bulk-order'); ?></option>
					<?php
					$categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false));

					if (!is_wp_error($categories) && !empty($categories)) {
						foreach ($categories as $category) {
							echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
						}
					} else {
						echo '<option value="">No categories found</option>';
					}
					?>
				</select>
			<?php endif; ?>
		</div>
		<div id="jsGrid"></div>

		<div id="imageModal" class="modal" style="display:none;">
			<span class="close">&times;</span>
			<img class="modal-content" id="modalImage" />
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Fetch the latest bulk order settings saved by the admin , Prepare query arguments , Filter by selected category , Optionally filter by specific product IDs
	 * Author: Aakash
	 * Dt: 17/09/2024
	 */
	public function wbo_get_products()
	{
		$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
		$bulk_order_settings = get_option('bulk_order_settings', array());
		$default_settings = array(
			'sort_by' => 'title',
			'sort_order' => 'ASC',
			'product_ids' => '',
			'add_cat_filter' => 'No',
			'cat_label' => 'Category'
		);
		$latest_settings = !empty($bulk_order_settings) ? end($bulk_order_settings) : $default_settings;

		$args = array(
			'limit' => -1,
			'orderby' => $latest_settings['sort_by'],
			'order' => $latest_settings['sort_order'],
			'status' => 'publish',
			'fields' => 'all'
		);

		if ($category_id > 0) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_id,
					'operator' => 'IN',
				),
			);
		}

		if (!empty($latest_settings['product_ids'])) {
			$product_ids = explode(',', $latest_settings['product_ids']);
			$args['include'] = array_map('intval', $product_ids);
		}

		$products = wc_get_products($args);
		$product_data = array();

		foreach ($products as $product) {
			$product_name = $product->get_name();
			$product_type = $product->is_type('variable') ? 'variable' : 'simple';
			$price = '';

			$variation_prices = array();

			if ($product_type === 'variable') {
				$variations = $product->get_available_variations();
				$variation_select = '<select class="product-variations" data-product-id="' . esc_attr($product->get_id()) . '">';

				$variation_prices = array();

				foreach ($variations as $variation) {
					$variation_id = $variation['variation_id'];
					$variation_attributes = isset($variation['attributes']) ? implode(', ', $variation['attributes']) : 'No attributes';
					$variation_price = isset($variation['display_price']) ? wc_price($variation['display_price']) : ''; // Format the price here

					$variation_select .= '<option value="' . esc_attr($variation_id) . '" data-price="' . esc_attr($variation_price) . '">' . esc_html($variation_attributes) . '</option>';

					$variation_prices[$variation_id] = $variation_price;
				}
				$variation_select .= '</select>';

				$price = !empty($variation_prices) ? reset($variation_prices) : '';
				$product_name .= ' ' . $variation_select;
			} else {
				$price = wc_price($product->get_price());
			}

			$image_url = wp_get_attachment_image_url($product->get_image_id(), 'medium');
			if (!$image_url) {
				$image_url = wc_placeholder_img_src();
			}

			$product_data[] = array(
				'id' => $product->get_id(),
				'type' => $product_type,
				'image' => $image_url,
				'name' => $product_name,
				'price' => $price,
				'quantity' => 1,
				'variation_prices' => $variation_prices
			);
		}

		wp_send_json_success($product_data);
	}

	/**
	 * Handle AJAX request to add product to cart.
	 * Author: Aakash
	 * Dt: 17/09/2024
	 */
	public function wbo_add_to_cart()
	{
		if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
			$product_id = intval($_POST['product_id']);
			$quantity = intval($_POST['quantity']);

			$cart_item_data = array();
			$cart = WC()->cart;
			$cart->add_to_cart($product_id, $quantity);

			wp_send_json_success(array('message' => 'Product added to cart.'));
		} else {
			wp_send_json_error(array('message' => 'Invalid product or quantity.'));
		}
	}

	/**
	 * Get WooCommerce cart fragments , Return the updated cart fragments
	 * Author: Aakash
	 * Dt: 17/09/2024
	 */
	function wbo_woocommerce_get_refreshed_fragments_handler()
	{
		$fragments = WC_AJAX::get_refreshed_fragments();
		wp_send_json($fragments);
	}
}
