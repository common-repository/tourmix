<?php

/**
 * Firstly check if WooCommerce is active
 * 
 * If WooCommerce is active extends WC_Shipping_Method by WC_Tourmix_Delivery_Shipping_Method
 *
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    add_action( 'woocommerce_shipping_init', 'tourmix_shipping_method_init' );
	function tourmix_shipping_method_init () {
		class TourmixShippingMethod extends WC_Shipping_Method {
			private $price;
			private $minimum;

			public function __construct() {
				$this->id                 = 'tourmix_delivery_shipping'; // Id for your shipping method. Should be uunique.
				$this->method_title       = __( 'TOURMIX' );  // Title shown in admin
				$this->method_description = __( 'TOURMIX - Házhozszállítás' ); // Description shown in admin
				$this->title              = "TOURMIX - Házhozszállítás"; // This shown in the shop checkout page

				$this->init();
			}

			function init() {
				// Load the settings API
				$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
				$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

				$this->enabled = $this->settings['enabled'];
				$this->price   = $this->settings['price'];
				$this->minimum = $this->settings['minimumprice'];

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			function init_form_fields() {
				$this->form_fields = array(
					'enabled' => array(
						'title'         => "Szállítási mód engedélyezése",
						'type'          => 'checkbox',
						//'description'   => "",
						'default'       => "yes",
						//'desc_tip'      => true,
					),
					'price' => array(
						'title'         => "Nettó egységár",
						'type'          => 'number',
						//'description'   => "",
						'default'       => 1094.49,
						//'desc_tip'      => true,
					),
					'minimumprice' => array(
						'title'         => "Minimális kosárérték",
						'type'          => 'number',
						'description'   => "A minimális kosárérték az, ami felett Ön nem számol fel szállítási költséget a vásárlójának.",
						'default'       => 20000.00,
						//'desc_tip'      => true,
					)
				);
		   }

			/**
			 * calculate_shipping function.
			 *
			 * @param mixed $package = []
			 */
			public function calculate_shipping($package = []) {
				$cost = $this->price;

				$rate = array(
					'label' => $this->title,
					'cost' => $this->calcPrice($package, $this->price),
					'taxes' => '',
					'calc_tax' => 'per_order'
				);

				// Register the rate
				$this->add_rate( $rate );
			}

			private function calcPrice ($package, $default) {
				$total = 0;

				foreach($package['contents'] as $item){
					$total += $item['line_subtotal'];
				}

				if($total >= $this->minimum) {
					return 0.0;
				}

				return $default;
			}

			function admin_options() {
				echo
					"<table class='form-table'>" .
						$this->generate_settings_html() .
					"</table>";
			}
		}
	}


    add_filter( 'woocommerce_shipping_methods', 'add_tourmix_shipping_method' );
	function add_tourmix_shipping_method( $methods ) {
		$methods['tourmix_delivery_shipping_method'] = 'TourmixShippingMethod';
		return $methods;
	}
}