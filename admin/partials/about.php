<?php

/**
 * HTML for the about submenu page
 */

?><div class="quanticedge_welcome_page wrap">
	<h1>QuanticEdge</h1>
	<div class="card" style="width:95%;max-width:none;">
		<h2 class="title">
			Welcome to QuanticEdge <?php
									$image_url = esc_url(plugin_dir_url(__FILE__) . '../image/quanticedge.png');
									echo '<img src="' . $image_url . '" alt="QuanticEdge-Image" style="vertical-align: middle; width: 20px; height: 20px;"/> ';
									?> Family
		</h2>

		<p>
			Thank you for choosing QuanticEdge plugins for your website! <br /><br />If something is not working as expected, <a href="https://quanticedgesolutions.com/" target="_blank"><strong>let us know here</strong></a> (I'll get it working for you!) else, you can <a href="https://wordpress.org/support/plugin/bulk-order-woocommerce/reviews/?filter=5" target="_blank"><strong>let others know here</strong></a>. :)<br /><br />If you need custom features developed, <a href="https://quanticedgesolutions.com/contact-us/" target="_blank"><strong>get in touch instantly</strong></a>. I am a <a href="https://quanticedgesolutions.com/" target="_blank"><strong>Woocommerce Certified Expert</strong></a>.
		</p>
	</div>

	<div class="card quanticedge_plugins">
		<h2 class="title">Free plugins</h2>
		<?php
		if (false === ($plugins_arr = get_transient('quanticedge_plugins'))) {
			$args    = (object) array(
				'author'   => 'vidishp',
				'per_page' => '120',
				'page'     => '1',
				'fields'   => array('slug', 'name', 'version', 'downloaded', 'active_installs')
			);
			$request = array(
				'action'  => 'query_plugins',
				'timeout' => 15,
				'request' => serialize($args)
			);
			//https://codex.wordpress.org/WordPress.org_API
			$url      = 'http://api.wordpress.org/plugins/info/1.0/';
			$response = wp_remote_post($url, array('body' => $request));
			if (! is_wp_error($response)) {
				$plugins_arr = array();
				$plugins     = unserialize($response['body']);
				if (isset($plugins->plugins) && (count($plugins->plugins) > 0)) {
					foreach ($plugins->plugins as $pl) {
						$plugins_arr[] = array(
							'slug'            => $pl->slug,
							'name'            => $pl->name,
							'version'         => $pl->version,
							'downloaded'      => $pl->downloaded,
							'active_installs' => $pl->active_installs
						);
					}
				}
				set_transient('quanticedge_plugins', $plugins_arr, 24 * HOUR_IN_SECONDS);
			}
		}
		if (is_array($plugins_arr) && (count($plugins_arr) > 0)) {
			array_multisort(array_column($plugins_arr, 'active_installs'), SORT_DESC, $plugins_arr);
			$i = 1;

			foreach ($plugins_arr as $pl) {
				if ($pl['slug'] == 'wooextend-push-notification')
					continue;
				echo '<div class="item"><a href="' . esc_url('https://wordpress.org/plugins/' . $pl['slug']) . '"><span class="num">' . intval($i) . '</span><span class="title">' . sanitize_text_field($pl['name']) . '</span><br/><span class="info">Version ' . floatval($pl['version']) . '</span><span class="downloads">' . intval($pl['active_installs']) . '+ Active users</span></a></div>';
				$i++;
			}
		}
		?>
	</div>
	<div class="card quanticedge_plugins wooextend_services">
		<h2 class="title">Our services</h2>
		<p><?php
			$plugins_arr = array(

				0	=>	array(
					'title'	=>	'Exclusive <strong>SEO</strong> package',
					'url'	=>	'https://bit.ly/3QcpjOh',
					'price'	=>	'349',
					'desc'	=>	'Monthly packages to boost your site ranking, and you can see clear results. Much more than what yoast seo does.'
				),
				1	=>	array(
					'title'	=>	'Exclusive <strong>Adwords</strong> package',
					'url'	=>	'https://bit.ly/3FZ0CjB',
					'price'	=>	'350',
					'desc'	=>	'Google certified adwords specialist to strategically design your spend and optimize performance.'
				),
				2	=>	array(
					'title'	=>	'WordPress Fully Managed Services',
					'url'	=>	'https://bit.ly/3viLvMX',
					'price'	=>	'150',
					'desc'	=>	'We\'ll keep your site secured, updated and let you focus on sales.'
				),
				3	=>	array(
					'title'	=>	'WordPress Infection Malware Virus Removal',
					'url'	=>	'https://bit.ly/3VnFXeJ',
					'price'	=>	'85',
					'desc'	=>	'Recover your hacked website, provide removal details of infected files.'
				),
				4	=>	array(
					'title'	=>	'General WordPress Support 24/7',
					'url'	=>	'https://bit.ly/3Wtl82N',
					'price'	=>	'35',
					'desc'	=>	'Troubleshoot & fix any issue in a recommended way of wordpress.'
				)
			);
			$i = 1;
			foreach ($plugins_arr as $pl) {

				echo '<div class="item"><a href="' . esc_url($pl['url']) . '" target="_blank"><span class="num">' . intval($i) . '</span><span class="title">' . sanitize_text_field($pl['title']) . '</span><br/><span class="info">' . sanitize_text_field($pl['desc']) . '</span><span class="downloads">Starting from $' . floatval($pl['price']) . '</span></a></div>';
				$i++;
			}
			?></p>
	</div>
	<div class="card quanticedge_plugins" style="max-width:700px;">
		<h2 class="title">Premium Plugins</h2>
		<p><?php
			$plugins_arr = array(
				0	=>	array(
					'title'	=>	'Woocommerce Order Promotion Pro',
					'url'	=>	'https://www.wooextend.com/product/order-promotion-woocommerce-pro/',
					'price'	=>	'30',
					'desc'	=>	'Allows you to give some free gifts, discounts to your first time & regular customers.'
				),
				1	=>	array(
					'title'	=>	'Woo Product Category Discount Pro',
					'url'	=>	'https://www.wooextend.com/product/woo-product-category-discount-pro/',
					'price'	=>	'25',
					'desc'	=>	'Apply discount in your store based on category, attributes, tags & brands. <strong>In just 1 click.</strong>'
				),
				2	=>	array(
					'title'	=>	'Group Stock Manager',
					'url'	=>	'https://www.wooextend.com/product/group-stock-manager/',
					'price'	=>	'28',
					'desc'	=>	'Share stock between multiple products or variations.'
				),
				3	=>	array(
					'title'	=>	'Woo Combo Offers Pro',
					'url'	=>	'https://www.wooextend.com/product/woocommerce-combo-offers-pro/',
					'price'	=>	'25',
					'desc'	=>	'Allow customers to purchase combo products with unlimited sub-items and raise your sales revenue.'
				)
			);
			$i = 1;
			foreach ($plugins_arr as $pl) {

				echo '<div class="item"><a href="' . esc_url($pl['url']) . '" target="_blank"><span class="num">' . intval($i) . '</span><span class="title">' . sanitize_text_field($pl['title']) . '</span><br/><span class="info">' . sanitize_text_field($pl['desc']) . '</span><span class="downloads">$' . floatval($pl['price']) . ' ONLY</span></a></div>';
				$i++;
			}
			?></p>
	</div>
</div>