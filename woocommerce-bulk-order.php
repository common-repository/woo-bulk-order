<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://quanticedgesolutions.com/
 * @since             3.0.0
 * @package           Woocommerce_Bulk_Order
 *
 * @wordpress-plugin
 * Plugin Name:       Bulk Order Woocommerce
 * Plugin URI:        https://quanticedgesolutions.com/
 * Description:       "Bulk Order Woocommerce" allows your customers to order multiple products on single page.
 * Version:           3.0.0
 * Author:            QuanticEdge Software Solutions LLP
 * Author URI:        https://quanticedgesolutions.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-bulk-order
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WOOCOMMERCE_BULK_ORDER_VERSION', '3.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wbo-woocommerce-bulk-order-activator.php
 */
function wbo_activate_woocommerce_bulk_order()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wbo-woocommerce-bulk-order-activator.php';
    WBO_Woocommerce_Bulk_Order_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wbo-woocommerce-bulk-order-deactivator.php
 */
function wbo_deactivate_woocommerce_bulk_order()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wbo-woocommerce-bulk-order-deactivator.php';
    WBO_Woocommerce_Bulk_Order_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'wbo_activate_woocommerce_bulk_order');
register_deactivation_hook(__FILE__, 'wbo_deactivate_woocommerce_bulk_order');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wbo-woocommerce-bulk-order.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wbo_run_woocommerce_bulk_order()
{
    $plugin = new WBO_Woocommerce_Bulk_Order();
    $plugin->run();
}
wbo_run_woocommerce_bulk_order();


/**
 * Create or update the Order Now page upon plugin activation or settings update , Dynamically build the shortcode
 * Author: Aakash
 * Dt: 17/09/2024
 */
function wbo_create_or_update_order_now_page()
{
    $page_title = 'Order Now';
    $bulk_order_settings = get_option('bulk_order_settings', array());
    $default_settings = array(
        'sort_by' => 'title',
        'sort_order' => 'ASC',
        'product_ids' => '',
        'add_cat_filter' => 'No',
        'cat_label' => 'Category'
    );

    $latest_settings = !empty($bulk_order_settings) ? end($bulk_order_settings) : $default_settings;

    $shortcode = '[wbo_woo_bulk_order';
    $shortcode .= ' orderby="' . esc_attr($latest_settings['sort_by']) . '"';
    $shortcode .= ' order="' . esc_attr($latest_settings['sort_order']) . '"';
    if (!empty($latest_settings['product_ids'])) {
        $shortcode .= ' ids="' . esc_attr($latest_settings['product_ids']) . '"';
    }
    if ($latest_settings['add_cat_filter'] === 'Yes') {
        $shortcode .= ' category_filter="true" cat_label="' . esc_attr($latest_settings['cat_label']) . '"';
    } else {
        $shortcode .= ' category_filter="false"';
    }
    $shortcode .= ']';

    $query = new WP_Query(array(
        'post_type'  => 'page',
        'title'      => $page_title,
        'posts_per_page' => 1,
    ));

    if (!$query->have_posts()) {
        $new_page_id = wp_insert_post(array(
            'post_title'  => $page_title,
            'post_content' => $shortcode,
            'post_status' => 'publish',
            'post_type'   => 'page',
        ));
        update_option('wbo_order_now_page_id', $new_page_id);
    } else {
        $page_id = $query->post->ID;
        wp_update_post(array(
            'ID' => $page_id,
            'post_content' => $shortcode,
        ));
        update_option('wbo_order_now_page_id', $page_id);
    }

    wp_reset_postdata();
}
add_action('update_option_bulk_order_settings', 'wbo_create_or_update_order_now_page');
register_activation_hook(__FILE__, 'wbo_create_or_update_order_now_page');


/**
 * Remove the Order Now page upon plugin deactivation.
 * Author: Aakash
 * Dt: 17/09/2024
 */
function wbo_delete_order_now_page()
{
    $page_id = get_option('wbo_order_now_page_id');

    if ($page_id) {
        $page = get_post($page_id);

        if ($page && $page->post_type === 'page') {
            wp_delete_post($page_id, true);
        }
        delete_option('wbo_order_now_page_id');
    }
}
register_deactivation_hook(__FILE__, 'wbo_delete_order_now_page');

/**
 * Plugin action links
 * Author: Aakash
 * Dt: 17/09/2024
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wbo_action_links');
function wbo_action_links($links)
{
    $links[] = '<a href="https://quanticedgesolutions.com/">Woocommerce Expert</a>';
    return $links;
}
