<?php

/**
 * HTML for the bulk order submenu page
 */
?>
<form method="POST" id="bulk-order">
    <h2><?php _e('Bulk Order Configuration', 'woocommerce-bulk-order'); ?></h2>
    <h4><?php _e('DO NOT UPDATE SHORTCODE MANUALLY ON BULK ORDER PAGE. YOU CAN UPDATE SETTINGS HERE', 'woocommerce-bulk-order'); ?></h4>

    <table>
        <?php
        /**
         * Fetch the latest settings
         * Author: Aakash
         * Dt: 17/09/2024
         */
        $settings = get_option('bulk_order_settings', array());
        $default_settings = array(
            'sort_by' => 'title',
            'sort_order' => 'ASC',
            'product_ids' => '',
            'add_cat_filter' => 'No',
            'cat_label' => ''
        );

        /**
         * Get the latest settings or use default
         * Author: Aakash
         * Dt: 17/09/2024
         */
        $latest_settings = !empty($settings) ? end($settings) : $default_settings;

        $sort_by = isset($latest_settings['sort_by']) ? $latest_settings['sort_by'] : '';
        $sort_order = isset($latest_settings['sort_order']) ? $latest_settings['sort_order'] : '';
        $product_ids = isset($latest_settings['product_ids']) ? $latest_settings['product_ids'] : '';
        $add_cat_filter = isset($latest_settings['add_cat_filter']) ? $latest_settings['add_cat_filter'] : 'No';
        $cat_label = isset($latest_settings['cat_label']) ? $latest_settings['cat_label'] : '';
        ?>

        <tr>
            <th style="text-align:left;"><?php _e('Sort Products By', 'woocommerce-bulk-order'); ?></th>
            <td>
                <ul class="wbo-no-list">
                    <li><input type="radio" name="rdo_sort_by" value="title" id="rdo_sort_by_title" <?php checked($sort_by, 'title'); ?> /><label for="rdo_sort_by_title"><?php _e('Product Name', 'woocommerce-bulk-order'); ?></label></li>
                    <li><input type="radio" name="rdo_sort_by" value="ID" id="rdo_sort_by_id" <?php checked($sort_by, 'ID'); ?> /><label for="rdo_sort_by_id"><?php _e('ID', 'woocommerce-bulk-order'); ?></label></li>
                    <li><input type="radio" name="rdo_sort_by" value="menu_order" id="rdo_sort_by_menu_order" <?php checked($sort_by, 'menu_order'); ?> /><label for="rdo_sort_by_menu_order"><?php _e('Menu Order', 'woocommerce-bulk-order'); ?></label></li>
                    <li><input type="radio" name="rdo_sort_by" value="post_date" id="rdo_sort_by_post_date" <?php checked($sort_by, 'post_date'); ?> /><label for="rdo_sort_by_post_date"><?php _e('Post Date', 'woocommerce-bulk-order'); ?></label></li>
                </ul>
            </td>
        </tr>
        <tr>
            <th style="text-align:left;"><?php _e('Sort Order', 'woocommerce-bulk-order'); ?></th>
            <td>
                <ul class="wbo-no-list">
                    <li><input type="radio" name="rdo_sort_order" value="ASC" id="rdo_sort_by_asc" <?php checked($sort_order, 'ASC'); ?> /><label for="rdo_sort_by_asc"><?php _e('ASC', 'woocommerce-bulk-order'); ?></label></li>
                    <li><input type="radio" name="rdo_sort_order" value="DESC" id="rdo_sort_by_desc" <?php checked($sort_order, 'DESC'); ?> /><label for="rdo_sort_by_desc"><?php _e('DESC', 'woocommerce-bulk-order'); ?></label></li>
                </ul>
            </td>
        </tr>
        <tr>
            <th style="text-align:left;"><?php _e('Product IDs', 'woocommerce-bulk-order'); ?></th>
            <td>
                <input type="text" name="txtProductIds" id="txtProductIds" placeholder="Leave empty to include all products" size="30" value="<?php echo esc_attr($product_ids); ?>">
                <?php _e('Separate with comma for multiple products (Leave empty to include all products)', 'woocommerce-bulk-order'); ?>
            </td>
        </tr>
        <tr>
            <th style="text-align:left;"><?php _e('Add Category Filter', 'woocommerce-bulk-order'); ?></th>
            <td>
                <ul class="wbo-no-list">
                    <li><input type="radio" name="rdo_add_cat_filter" value="Yes" id="rdo_add_cat_filter_yes" <?php checked($add_cat_filter, 'Yes'); ?> /><label for="rdo_add_cat_filter_yes"><?php _e('Yes', 'woocommerce-bulk-order'); ?></label></li>
                    <li><input type="radio" name="rdo_add_cat_filter" value="No" id="rdo_add_cat_filter_no" <?php checked($add_cat_filter, 'No'); ?> /><label for="rdo_add_cat_filter_no"><?php _e('No', 'woocommerce-bulk-order'); ?></label></li>
                </ul>
            </td>
        </tr>
        <tr>
            <th style="text-align:left;"><?php _e('Catgory Filter Label', 'woocommerce-bulk-order'); ?></th>
            <td>
                <input type="text" name="txtCatLabel" id="txtCatLabel" placeholder="Label for Category Filter" size="30" value="<?php echo esc_attr($cat_label); ?>">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="save" value="Save" class="button-primary">
            </td>
        </tr>
    </table>
</form>