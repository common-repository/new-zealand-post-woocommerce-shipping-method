<?php

class WC_New_Zealand_Post_Shipping_Method extends WC_Shipping_Method{



	public $postageParcelURL = 'http://api.nzpost.co.nz/ratefinder/rate.json';
	public $api_key = '123'; //demo api key


	public function __construct( $instance_id = 0 ){
		$this->id = 'nzpost';
		$this->method_title = __('New Zealand Post','woocommerce-new-zealand-post-shipping-method');
		$this->title = __('New Zealand Post','woocommerce-new-zealand-post-shipping-method');
		$this->instance_id = absint( $instance_id );

		$this->init_form_fields();
		$this->init_settings();
		$this->supports  = array(
 			'shipping-zones',
 			'instance-settings',
 		);

		$this->enabled = $this->get_option('enabled');
		$this->title = $this->get_option('title');
		$this->api_key = $this->get_option('api_key');
		$this->shop_post_code = $this->get_option('shop_post_code');


		$this->default_weight = $this->get_option('default_weight');
		$this->default_thickness = $this->get_option('default_thickness');
		$this->default_length = $this->get_option('default_length');
		$this->default_height = $this->get_option('default_height');

		$this->debug_mode = $this->get_option('debug_mode');

		add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));

	}


	public function init_form_fields(){

				$dimensions_unit = strtolower( get_option( 'woocommerce_dimension_unit' ) );
				$weight_unit = strtolower( get_option( 'woocommerce_weight_unit' ) );

				$this->instance_form_fields = array(
					'title' => array(
						'title' 		=> __( 'Method Title', 'woocommerce' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This controls the title', 'woocommerce' ),
						'default'		=> __( 'New Zealand Post Shipping', 'woocommerce' ),
						'desc_tip'		=> true,
					),
					'api_key' => array(
							'title'             => __( 'API Key', 'woocommerce-new-zealand-post-shipping-method' ),
							'type'              => 'text',
							'description'       => __( 'Get your key from <a target="_blank" href="https://www.nzpost.co.nz/business/developer-centre/rate-finder-api/get-a-rate-finder-api-key">here</a>', 'woocommerce-new-zealand-post-shipping-method' ),
							'default'           => $this->api_key
					),
					'shop_post_code' => array(
							'title'             => __( 'Shop Origin Post Code', 'woocommerce-new-zealand-post-shipping-method' ),
							'type'              => 'text',
							'description'       => __( 'Enter your Shop postcode.', 'woocommerce-new-zealand-post-shipping-method' ),
							'default'           => '2000'
					),
					'default_weight' => array(
							'title'             => __( 'Default Package Weight', 'woocommerce-new-zealand-post-shipping-method' ),
							'type'              => 'text',
							'default'           => '0.5',
							'css'				=> 'width:70px;',
							'description'       => __( $weight_unit , 'woocommerce-new-zealand-post-shipping-method' ),
					),
					'default_thickness' => array(
							'title'             => __( 'Default Package Thickness (Width)', 'woocommerce-new-zealand-post-shipping-method' ),
							'type'              => 'text',
							'default'           => '5',
							'css'				=> 'width:70px;',
							'description'       => __( $dimensions_unit, 'woocommerce-new-zealand-post-shipping-method' ),
					),
					'default_height' => array(
							'title'             => __( 'Default Package Height', 'woocommerce-new-zealand-post-shipping-method' ),
							'type'              => 'text',
							'default'           => '5',
							'css'				=> 'width:70px;',
							'description'       => __( $dimensions_unit, 'woocommerce-new-zealand-post-shipping-method' ),
					),
					'default_length' => array(
							'title'             => __( 'Default Package Length', 'woocommerce-new-zealand-post-shipping-method' ),
							'type'              => 'text',
							'default'           => '10',
							'css'				=> 'width:70px;',
							'description'       => __( $dimensions_unit, 'woocommerce-new-zealand-post-shipping-method' ),
					),
					'debug_mode' => array(
						'title' 		=> __( 'Enable Debug Mode', 'woocommerce' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Enable ', 'woocommerce' ),
						'default' 		=> 'no',
						'description'	=> __('If debug mode is enabled, the shipping method will be activated just for the administrator.'),
					),
			 );
	}



	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_options() {

		?>
		<h3><?php _e( 'New Zealand Post Settings', 'woocommerce-new-zealand-post-shipping-method' ); ?></h3>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<?php if($this->debug_mode == 'yes'): ?>
							<div class="updated woocommerce-message">
						    	<p><?php _e( 'New Zealand Post debug mode is activated, only administrators can use it.', 'woocommerce-new-zealand-post-shipping-method' ); ?></p>
						    </div>
						<?php endif; ?>
						<?php if(version_compare(WC()->version, '2.6.0', 'lt')): ?>
							<div class="error woocommerce-message">
						    	<p><?php _e( 'This version only supports WooCommerce 2.6+ Please install a lower version or upgrade WooCommerce to 2.6+', 'woocommerce-new-zealand-post-shipping-method' ); ?></p>
						    </div>
						<?php endif; ?>
						<table class="form-table">
							<?php
								echo $this->get_admin_options_html();
							?>
						</table><!--/.form-table-->
					</div>
					<div id="postbox-container-1" class="postbox-container">
	                        <div id="side-sortables" class="meta-box-sortables ui-sortable">

	                            <div class="postbox rss-postbox">
	    								<h3 class="hndle"><span><i class="fa fa-wordpress"></i>&nbsp;&nbsp;WPRuby Blog</span></h3>
                                    <hr>
	    								<div class="inside">
											<div class="rss-widget">
												<?php
	    											wp_widget_rss_output(array(
	    													'url' => 'https://wpruby.com/feed/',
	    													'title' => 'WPRuby Blog',
	    													'items' => 3,
	    													'show_summary' => 0,
	    													'show_author' => 0,
	    													'show_date' => 1,
	    											));
	    										?>
	    									</div>
	    								</div>
	    						</div>

	                        </div>
	                    </div>
                    </div>
				</div>
				<div class="clear"></div>
				<style type="text/css">
				.wpruby_button{
					background-color:#4CAF50 !important;
					border-color:#4CAF50 !important;
					color:#ffffff !important;
					width:100%;
					padding:5px !important;
					text-align:center;
					height:35px !important;
					font-size:12pt !important;
				}
				</style>
				<?php
	}

	public function is_available( $package ){
		// Debug mode
		if($this->debug_mode === 'yes'){
			return current_user_can('administrator');
		}

		if($package['destination']['country'] != 'NZ') return false;


		return true;


	}

	public function calculate_shipping( $package = array() ){
		$package_details  =  $this->get_package_details( $package );

		$this->debug(print_r($package_details, true));
		$this->rates = array();


		$weight = 0;
		$length = 0;
		$thickness = 0;
		$height = 0;
		$rates = [];
		foreach($package_details as  $pack){

			$weight = $pack['weight'];
			$height = $pack['height'];
			$thickness 	= $pack['thickness'];
			$length = $pack['length'];
			$rates = $this->get_rates($rates, $weight, $height, $thickness, $length, $package['destination']['postcode'] );

		}

		if(!empty($rates)){
			foreach ($rates as $key => $rate) {
				$rate['package'] = $package;
				$this->add_rate($rate);
			}
		}


	}

	private function get_rates( $old_rates, $weight, $height, $thickness, $length, $destination ){

		$rates = array();
		$query_params['postcode_src'] = $this->shop_post_code;
		$query_params['postcode_dest'] = $destination;
		$query_params['length'] = $length;
		$query_params['thickness'] = $thickness;
		$query_params['height'] = $height;
		$query_params['weight'] = $weight;
		$query_params['carrier'] = 'nzpost';
		$query_params['format'] = 'json';
		$query_params['api_key'] = $this->api_key;
		$query_params['rural_options'] = 'exclude';

		$response = wp_remote_get( $this->postageParcelURL.'?'.http_build_query($query_params));
		if(is_wp_error( $response )){
			return array('error' => 'Unknown Problem. Please Contact the admin');
		}
		$nz_response = json_decode(wp_remote_retrieve_body($response));

		$this->debug(print_r($nz_response, true));


		if($nz_response->success === true){
		// add the rate if the API request succeeded
			foreach($nz_response->products as $product){
				if($product->packaging != 'postage_only') continue;
				#if($product->service!='COU' && $product->service!='STD') continue;
                $old_rate = (isset($old_rates[$product->service_group_description]))? $old_rates[$product->service_group_description]['cost'] :0;
				$rates[$product->description] = array(
						'id' => $product->description,
						'label' => $this->title . ' ' . $product->service_group_description, //( '.$service->delivery_time.' )
						'cost' =>  ($product->cost ) + $old_rate,
					);
				}
		}
		return $rates;
	}


	/**
	 * get_min_dimension function.
	 * get the minimum dimension of the package, so we multiply it with the quantity
	 * @access private
	 * @param number $thickness
	 * @param number $length
	 * @param number $height
	 * @return string $result
	 */
	private function get_min_dimension($thickness, $length, $height){

		$dimensions = array('thickness'=>$thickness,'length'=>$length,'height'=>$height);
		$result = array_keys($dimensions, min($dimensions));
		return $result[0];
	}


	/**
	 * get_package_details function.
	 *
	 * @access private
	 *
	 * @param mixed $package
	 *
	 * @return array
	 */
    private function get_package_details( $package ) {

	    $parcel   = array();
	    $requests = array();
    	$weight   = 0;
    	$volume   = 0;
    	$value    = 0;
    	$products = array();
    	// Get weight of order
    	foreach ( $package['contents'] as $item_id => $values ) {

		    /** @var $_product WC_Product */
		    $_product = $values['data'];
		    if ($_product->is_virtual()) {
			    continue;
		    }
    		$weight += wc_get_weight( $_product->get_weight(), 'kg' ) * $values['quantity'];
    		$value  += $_product->get_price() * $values['quantity'];

    		$length = wc_get_dimension( ($_product->get_length()=='')?$this->default_length:$_product->get_length(), 'mm' );
    		$height = wc_get_dimension( ($_product->get_height()=='')?$this->default_height:$_product->get_height(), 'mm' );
    		$thickness = wc_get_dimension( ($_product->get_width()=='')?$this->default_thickness:$_product->get_width(), 'mm' );
    		$min_dimension = $this->get_min_dimension( $thickness, $length, $height );
			$$min_dimension = $$min_dimension * $values['quantity'];
    		$products[] = array('weight'=> wc_get_weight( $_product->get_weight(), 'kg' ),
    							'quantity'=> $values['quantity'],
    							'length'=> $length,
    							'height'=> $height,
    							'thickness'=> $thickness,
    							'item_id'=> $item_id,
    						);
    		$volume += ( $length * $height * $thickness );
    	}

    	$max_weight = $this->get_max_weight($package);

	    	$pack = array();
			$packs_count = 1;
			$pack[$packs_count]['weight'] = 0;
			$pack[$packs_count]['length'] = 0;
			$pack[$packs_count]['height'] = 0;
			$pack[$packs_count]['thickness'] = 0;
			$pack[$packs_count]['quantity'] = 0;
			foreach ($products as $product){
				while ($product['quantity'] != 0) {
					$pack[$packs_count]['weight'] += $product['weight'];
					$pack[$packs_count]['length'] = $product['length'];
					$pack[$packs_count]['height'] = $product['height'];
					$pack[$packs_count]['thickness']  =  $product['thickness'];
					$pack[$packs_count]['item_id'] =  $product['item_id'];
					$pack[$packs_count]['quantity'] +=  $product['quantity'];


					if($pack[$packs_count]['weight'] > $max_weight){
						$pack[$packs_count]['weight'] -=  $product['weight'];
						$pack[$packs_count]['quantity'] -=  $product['quantity'];
						$packs_count++;
						$pack[$packs_count]['weight'] = $product['weight'];
						$pack[$packs_count]['length'] = $product['length'];
						$pack[$packs_count]['height'] = $product['height'];
						$pack[$packs_count]['thickness'] = $product['thickness'];
						$pack[$packs_count]['item_id'] =  $product['item_id'];
						$pack[$packs_count]['quantity'] =  $product['quantity'];

					}
					$product['quantity']--;
				}
			}

    	return $pack;
    }



    private function get_max_weight( $package){
    	$max = ( $package['destination']['country'] == 'NZ' )? 25:20;
    	$store_unit = strtolower( get_option('woocommerce_weight_unit') );

    	if($store_unit == 'kg')
    		return $max;
    	if($store_unit == 'g')
    		return $max * 1000;
    	if($store_unit == 'lbs')
    		return $max * 0.453592;
    	if($store_unit == 'oz')
    		return $max * 0.0283495;

    	return $max;

    }

	/**
	 * Output a message
	 *
	 * @param $message
	 * @param string $type
	 */
	private function debug($message, $type = 'notice')
	{
		if ($this->debug_mode == 'yes' && current_user_can('manage_options')) {
			wc_add_notice(sprintf('<pre>%s</pre>', $message), $type);
		}
	}


}
