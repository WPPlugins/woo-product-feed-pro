<?php
/**
 * Class for generating the actual feeds
 */
class WooSEA_Get_Products {

	private $feedback;
	public $feed_config;
	private $products = array();
	private $utm = array();
	public $utm_part;
	public $project_config;
	private $upload_dir;
	private $base;
	private $path;
	private $file;

        public function __construct() {
                $this->get_products = array();
        }

	/**
 	 * Get all product cats for a product by ID, including hierarchy
 	 * @param  int $product_id
 	 * @return array
 	 */
	function wc_get_product_cat_ids( $product_id ) {
        	$product_cats = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );

	        foreach ( $product_cats as $product_cat ) {
        	        $product_cats = array_merge( $product_cats, get_ancestors( $product_cat, 'product_cat' ) );
        	}
        	return $product_cats;
	}

	/**
         * Function that will create an append with Google Analytics UTM parameters
         * Removes UTM paramaters that are left blank
	 */
	public function woosea_append_utm_code ( $feed_config, $productId, $parentId ) {
		# Create Array of Google Analytics UTM codes				
		$utm = array (
			'utm_source' => $feed_config['utm_source'],
			'utm_campaign' => $feed_config['utm_campaign'],
			'utm_medium' => $feed_config['utm_medium'],
			'utm_term' => $feed_config['utm_term'],
			'utm_content' => $feed_config['utm_content']
		);
		$utm = array_filter($utm); // Filter out empty or NULL values from UTM array		
		                        
		if(array_key_exists('utm_on', $feed_config)){
			$utm_part = "";	
			foreach ($utm as $key => $value ) {
				if(($key == "utm_term") AND ($value =="id")){
					$utm_part .= "&$key=$productId";
				} else {
					$utm_part .= "&$key=$value";
				}
			}

			# Strip first & from utm 
			if($parentId > 0){
				$utm_part = "&".ltrim($utm_part, '&');
			} else {
				$utm_part = "?".ltrim($utm_part, '&');
			}
		} else {
			$utm_part = '';
		}
		return $utm_part;
	}

	/**
	 * Get all configured shipping zones
	 */
	public function woosea_get_shipping_zones () {
		if( class_exists( 'WC_Shipping_Zones' ) ) {
			$all_zones = WC_Shipping_Zones::get_zones();
			return $all_zones;
		}
		return false;
	}

	/**
	 * Get shipping cost for product
	 */
	public function woosea_get_shipping_cost ($class_cost_id, $project_config) {
        	$shipping_cost = 0;
		$shipping_zones = WooSEA_GET_Products::woosea_get_shipping_zones();
		$nr_shipping_zones = count($shipping_zones);

		$base_location = wc_get_base_location();
		$base_country = $base_location['country'];

              	foreach ( $shipping_zones as $zone){
			$zone_code = $zone['zone_locations'][0]->code;

			// When no shipping zone has been configured in project take the shops country zone id as default
			if (!array_key_exists('zone', $project_config) AND ($zone_code == $base_country)){
				$project_config['zone'] = $zone['zone_id'];
			}

			if($zone['zone_id'] == $project_config['zone']){
                     		$shipping_methods     = $zone['shipping_methods'];
                    		$arr_shipping_methods = json_decode( json_encode( $shipping_methods ), true );
                      		foreach ( $arr_shipping_methods as $shipping ) {
                     			$shipping_cost = $shipping['instance_settings']['cost'];
                            		if(isset($shipping['instance_settings'][$class_cost_id])){
                                		$shipping_cost = ($shipping['instance_settings']['cost']+$shipping['instance_settings'][$class_cost_id]);
                              		}
                   		}
			}
		}
		return $shipping_cost;
	}

	/**
         * Creates XML root and header for productfeed
	 */	
	public function woosea_create_xml_feed ( $products, $feed_config, $header ) {
		$upload_dir = wp_upload_dir();

		$base = $upload_dir['basedir'];
 		$path = $base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$file = $path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];
	
		// External location for downloading the file	
		$external_base = $upload_dir['baseurl'];
 		$external_path = $external_base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$external_file = $external_path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];

		// Check if directory in uploads exists, if not create one	
		if ( ! file_exists( $path ) ) {
    			wp_mkdir_p( $path );
		}

		// Check if file exists, if it does: delete it first so we can create a new updated one
		if ( (file_exists( $file )) AND ($header == "true") AND ($feed_config['nr_products_processed'] == 0) ) {
			unlink ( $file );
		}	

		// Check if there is a channel feed class that we need to use
		if ($feed_config['fields'] != 'standard'){
			if (!class_exists('WooSEA_'.$feed_config['fields'])){
				require plugin_dir_path(__FILE__) . '/channels/class-'.$feed_config['fields'].'.php';
				$channel_class = "WooSEA_".$feed_config['fields'];
				$channel_attributes = $channel_class::get_channel_attributes();
				update_option ('channel_attributes', $channel_attributes, 'yes');	
			} else {
				$channel_attributes = get_option('channel_attributes');
			}
		}	

		// Some channels need their own feed config and XML namespace declarations (such as Google shopping)
		if ($feed_config['taxonomy'] == 'google_shopping'){
			if ( ($header == "true") AND ($feed_config['nr_products_processed'] == 0) ) {
			   	$xml = new SimpleXMLElement('<rss xmlns:g="http://base.google.com/ns/1.0"></rss>');
			   	$xml->addAttribute('version', '2.0');
				$xml->addChild('channel');
				$xml->channel->addChild('title', $feed_config['projectname']);
				$xml->channel->addChild('link', site_url());
				$xml->channel->addChild('description', 'WooCommerce Product Feed PRO - This product feed is created with the free Advanced WooCommerce Product Feed PRO plugin from AdTribes.io. For all your support questions please email to: support@adtribes.io ');
				$xml->asXML($file);	
			} else {
				$xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
			
				$aantal = count($products);

				if ($aantal > 0){
					foreach ($products as $key => $value){
						if (is_array ( $value ) ) {
							$product = $xml->channel->addChild('item');
							foreach ($value as $k => $v){
								$product->$k = $v;
							}
						}	
					}
					$xml->asXML($file);
					unset($product);	
				}
				unset($products);
			}
			unset($xml);
		} else {
			if ( ($header == "true") AND ($feed_config['nr_products_processed'] == 0) ) {
				$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><products></products>');	
				$xml->addAttribute('version', '1.0');
				$xml->addChild('datetime', date('Y-m-d H:i:s'));
				$xml->addChild('title', $feed_config['projectname']);
				$xml->addChild('link', site_url());
				$xml->addChild('description', 'WooCommerce Product Feed PRO - This product feed is created with the free Advanced WooCommerce Product Feed PRO plugin from AdTribes.io. For all your support questions please email to: support@adtribes.io ');
				$xml->asXML($file);
			} else {
				$xml = simplexml_load_file($file);
				$aantal = count($products);

				if ($aantal > 0){
					foreach ($products as $key => $value){
						if (is_array ( $value ) ) {
							$product = $xml->addChild('product');
							foreach ($value as $k => $v){
							
								/**
								 * Check if a product resides in multiple categories
								 * id so, create multiple category child nodes
								 */							
								if ($k == "categories"){
									$category = $product->addChild('categories');
									$cat = explode("||",$v);							

									if (is_array ( $cat ) ) {
										foreach ($cat as $kk => $vv){
											$child = "category";
											$category->addChild("$child", "$vv");
										}
									}
								} else {
									if ($feed_config['fields'] != 'standard'){
	          	                                           		$k = WooSEA_Get_Products::get_alternative_key ($channel_attributes, $k);
									}
									$product->addChild("$k");
									$product->$k = $v;
								}
							}
						}
					}	
					$xml->asXML($file);
					unset($product);
				}
				unset($products);
			}
			unset($xml);
		}
	}

	/**
         * Actual creation of CSV/TXT file
         * Returns relative and absolute file path
	 */	
	public function woosea_create_csvtxt_feed ( $products, $feed_config, $header ) {
		$upload_dir = wp_upload_dir();
		$base = $upload_dir['basedir'];
 		$path = $base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$file = $path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];
	
		// External location for downloading the file	
		$external_base = $upload_dir['baseurl'];
 		$external_path = $external_base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$external_file = $external_path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];

		// Check if directory in uploads exists, if not create one	
		if ( ! file_exists( $path ) ) {
    			wp_mkdir_p( $path );
		}

		// Check if file exists, if it does: delete it first so we can create a new updated one
		if ( (file_exists( $file )) AND ($feed_config['nr_products_processed'] == 0) AND ($header == "true") ) {
			unlink ( $file );
		}	

		// Check if there is a channel feed class that we need to use
		if ($feed_config['fields'] != 'standard'){
			if (!class_exists('WooSEA_'.$feed_config['fields'])){
				require plugin_dir_path(__FILE__) . '/channels/class-'.$feed_config['fields'].'.php';
				$channel_class = "WooSEA_".$feed_config['fields'];
				$channel_attributes = $channel_class::get_channel_attributes();
				update_option ('channel_attributes', $channel_attributes, 'yes');	
			} else {
				$channel_attributes = get_option('channel_attributes');
			}
		}	
		
		// Append or write to file
		$fp = fopen($file, 'a+');

		// Write each row of the products array
		foreach ($products as $row) {

			foreach ($row as $k => $v){
				$pieces = explode ("','", $v);

				foreach ($pieces as $k => $v){
                                        if ($feed_config['fields'] != 'standard'){
						$v = WooSEA_Get_Products::get_alternative_key ($channel_attributes, $v);
					}			            

					// For CSV fileformat the keys need to get stripped of the g:
                                      	if($feed_config['fileformat'] == "csv"){
                                        	$v = str_replace("g:", "", $v);
                                     	}	

					$pieces[$k] = $v;
				}
		
				$blaat = fputcsv($fp, $pieces, $feed_config['delimiter'], '"');
			}
		}

		// Close the file
		fclose($fp);

		// Return external location of feed
		return $external_file;
	}

	/**
         * Get products that are eligable for adding to the file
	 */
	public function woosea_get_products ( $project_config ) {
		$nr_products_processed = $project_config['nr_products_processed'];
		$count_products = wp_count_posts('product', 'product_variation');
		$count_variation = wp_count_posts('product_variation');
		$count_single = wp_count_posts('product');
		$published_single = $count_single->publish;
		$published_variation = $count_variation->publish;
		$published_products = $published_single+$published_variation;		

		/**
		 * Do not change these settings, they are here to prevent running into memory issues
		 */
		if($published_products >= 150){
			$nr_batches = 15;
		} else {
			$nr_batches = 1;
		}
		$offset_step_size = ceil($published_products/$nr_batches);
	
		/**
		 * Check if the [attributes] array in the project_config is of expected format.
		 * For channels that have mandatory attribute fields (such as Google shopping) we need to rebuild the [attributes] array
		 * Only add fields to the file that the user selected
		 * Construct header line for CSV ans TXT files, for XML create the XML root and header
		 */
		if($project_config['fileformat'] != 'xml'){
			if($project_config['fields'] != 'standard'){
				foreach ($project_config['attributes'] as $key => $value){
					foreach($value as $k => $v){
						if(($k == "attribute") AND (strlen($v) > 0)){
                     	       				if(!isset($attr)){
								$attr = "'$v'";
							} else {
								$attr .= ",'$v'";
							}
						}
					}
				}
			} else {
				foreach( array_keys($project_config['attributes']) as $attribute_key ){
					if (!isset($attr)){
						if(strlen($attribute_key) > 0){
							$attr = "'$attribute_key'";
						}
					} else {
						if(strlen($attribute_key) > 0){
							$attr .= ",'$attribute_key'";
						}
					}			
				}
			}

			$attr = trim($attr, "'");
			$products[] = array ( $attr );
			if($nr_products_processed == 0){
				$file = WooSEA_Get_Products::woosea_create_csvtxt_feed ( $products, $project_config, 'true' );
			}
		} else {
			$products[] = array ();
			$file = WooSEA_Get_Products::woosea_create_xml_feed ( $products, $project_config, 'true' );
		}
		$xml_piece = "";


		// Check if we need to get just products or also product variations
		if(isset($project_config['product_variations'])){
			$post_type = array('product', 'product_variation');
		} else {
			$post_type = array('product');
		}

		// Construct WP query
		$wp_query = array(
                                'posts_per_page' => $offset_step_size,
                                'offset' => $nr_products_processed,
                                'post_type' => $post_type,
                                'post_status' => 'publish',
                                'fields' => 'ids',
                                'no_found_rows' => true
                );

		$prods = new WP_Query($wp_query);

		// COPY THIS PART
		$no_taxonomies = array("category","post_tag","nav_menu","link_category","post_format","product_type","product_visibility","product_cat","product_shipping_class","product_tag");
		$taxonomies = get_taxonomies();
		$diff_taxonomies = array_diff($taxonomies, $no_taxonomies);

	        while ($prods->have_posts()) : $prods->the_post(); 
			global $product;
			$attr_line = "";
			$catname = "";	
			$xml_product = "";

			$this->childID = get_the_ID();
            		$this->parentID = wp_get_post_parent_id($this->childID);
			$post = get_post($this->parentID);

			# get custom taxonomy values for a product
			foreach($diff_taxonomies as $tax_diff){
				$terms = get_the_terms($this->childID, $tax_diff);

				if(is_array($terms)){
					foreach ($terms as $term){
						$taxonomy_details = get_taxonomy( $term->taxonomy );
						foreach($taxonomy_details as $kk => $vv){
                        				if($kk == "labels"){
                                				foreach($vv as $kw => $kv){
                                        				if($kw == "singular_name"){
                                                				$attr_name = strtolower(str_replace(" ", "_",$kv));
                                                				$attr_name_clean = ucfirst($kv);
                                        				}
                                				}
                        				}
						}
						$product_data[$attr_name] = $term->name;
					}
				}
			}
	
			$product_data['id'] = get_the_ID();
			$product_data['title'] = $product->get_title();
			$product_data['sku'] = $product->get_sku();
                       	$product_data['item_group_id'] = $this->parentID;
	
			$categories = wc_get_product_cat_ids( $product_data['id'] );
			foreach ($categories as $key => $value){
				if (!$catname){
					$catname = get_cat_name($value);
				} else {
					$catname .= "||".get_cat_name($value);
				}
			}

			$product_data['categories'] = $catname;
			$product_data['description'] = str_replace("\r", "", $post->post_content);
			$product_data['short_description'] = str_replace("\r", "", $post->post_excerpt);

			// Strip HTML from (short) description
			$product_data['description'] = strip_tags($product_data['description']);
			$product_data['short_description'] = strip_tags($product_data['short_description']);

			/**
		 	* Check of we need to add Google Analytics UTM parameters
		 	*/
			$utm_part = WooSEA_Get_Products::woosea_append_utm_code ( $project_config, get_the_ID(), $this->parentID);

			$product_data['link'] = get_permalink()."$utm_part";
			$product_data['condition'] = "New";				
			$product_data['availability'] = $this->get_stock( $this->childID );
			$product_data['quantity'] = $this->clean_quantity( $this->childID, "_stock" );
                    	$product_data['visibility'] = $this->get_attribute_value( $this->childID,"_visibility" );
			$product_data['price'] = $product->get_price();
			$product_data['sale_price'] = $product->get_sale_price();
                        $product_data['sale_price_start_date'] = $this->get_sale_date($this->childID, "_sale_price_dates_from");
                        $product_data['sale_price_end_date'] = $this->get_sale_date($this->childID, "_sale_price_dates_to");
			$product_data['image'] = wp_get_attachment_url($product->get_image_id());
			$product_data['product_type'] = $product->get_type();
                        $product_data['rating_total'] = $product->get_rating_count();
                        $product_data['rating_average'] = $product->get_average_rating();
	                $product_data['shipping'] = 0;
	
                	$shipping_class_id = $product->get_shipping_class_id();
                	$shipping_class= $product->get_shipping_class();
			$class_cost_id = "class_cost_".$shipping_class_id;
			$product_data['shipping'] =  WooSEA_Get_Products::woosea_get_shipping_cost($class_cost_id, $project_config);

			$product_data['weight'] = ($product->get_weight()) ? $product->get_weight() : false;
                        $product_data['height'] = ($product->get_height()) ? $product->get_height() : false;
                        $product_data['length'] = ($product->get_length()) ? $product->get_length() : false;
			$product_data['width'] = ($product->get_width()) ? $product->get_width() : false;

                        // Featured Image
                        if (has_post_thumbnail($post->ID)){
                         	$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                            	$product_data['feature_image'] = $this->get_image_url($image[0]);
                        } else {
                           	$product_data['feature_image'] = $this->get_image_url($product_data['image']);
                        }

			/**
			 * Do we need to add Dynamic Attributes?
			 */
			$project_config['attributes_original'] = $project_config['attributes'];
			
			if($project_config['fields'] != 'standard'){
				//$project_config['attributes_original'] = array();
				foreach($project_config['attributes'] as $stand_key => $stand_val){
					if((isset($stand_val['mapfrom'])) AND (strlen($stand_val['mapfrom']) > 0)){
						$project_config['attributes_original'][$stand_val['mapfrom']] = "true";
					}
				}				
			}

			foreach($project_config['attributes_original'] as $key => $value){
				/**
			 	 * Versioned products need a seperate approach, get data for these products  
			 	 */
				if( ($product_data['item_group_id'] > 0) AND (!is_array($value)) AND ($key == "item_group_id") ){
					$product_variations = new WC_Product_Variation( $product_data['id'] );
    					$variations = $product_variations->get_variation_attributes();

					foreach($variations as $kk => $vv){
						$custom_key = "custom_attributes_".$kk;
						if ($project_config['productname_append'] == $custom_key){
							$append = ucfirst($vv);
							$product_data['title'] = $product_data['title']." ".$append;
						}
						$product_data[$custom_key] = $vv;
					}
                        		// Get versioned product categories	
					$categories = wc_get_product_cat_ids( $product_data['item_group_id'] );
                        		foreach ($categories as $key => $value){
                                		if (!$catname){
                                        		$catname = get_cat_name($value);
                                		} else {
                                        		$catname .= "||".get_cat_name($value);
                                		}
                        		}
                        		$product_data['categories'] = $catname;
                                } 


				if (preg_match('/^product_attributes_/', $key)){
                                        $attr_value =  array_filter( wc_get_product_terms ($product_data['id'], str_replace("product_attributes_", "pa_", $key), array( 'fields' => 'names' ) ) );
			                if(!empty($attr_value)){
                                                $product_data[$key] = $attr_value[0];
                                        } else {
                                                $product_data[$key] = "";
                                        }
                                }
			}	

			/**
			 * Check if we need to add category taxonomy mappings (Google Shopping)
			 */
			if ((array_key_exists('mappings', $project_config)) AND ($project_config['taxonomy'] != 'none')){
				$product_data = $this->woocommerce_sea_mappings( $project_config['mappings'], $product_data ); 
			}

			/**
			 * Filter rules execution
			 */
			if (array_key_exists('rules', $project_config)){
				$product_data = $this->woocommerce_sea_rules( $project_config['rules'], $product_data ); 
			}

			/**
			 * When product has passed the filter rules it can continue with the rest
			 */
			if(!empty($product_data)){
				/**
				 * Determine what fields are allowed to make it to the csv and txt productfeed
				 */
			        if (($project_config['fields'] != "standard") AND (!isset($tmp_attributes))){
                     	 		$old_attributes_config = $project_config['attributes'];
                      			$tmp_attributes = array();
					foreach ($project_config['attributes'] as $key => $value){
						if(strlen($value['mapfrom']) > 0){
							$tmp_attributes[$value['mapfrom']] = "true";
						}
					}
	                      		$project_config['attributes'] = $tmp_attributes;
				}

				foreach( array_keys($project_config['attributes']) as $attribute_key ){
					if(!isset($old_attributes_config)){
						if (array_key_exists($attribute_key, $product_data)){
							if(!$attr_line){
								$attr_line = "'".$product_data[$attribute_key]."'";
							} else {
								$attr_line .= ",'".$product_data[$attribute_key]."'";
							}
						}
					} else {
						foreach($old_attributes_config as $attr_key => $attr_value){
							if ($attr_value['mapfrom'] == $attribute_key){
								if(!$attr_line){
									if(strlen($attr_value['mapfrom'])){
										$attr_line = "'".$attr_value['prefix']." ".$product_data[$attr_value['mapfrom']]." ".$attr_value['suffix']."'";
									} else {
										$attr_line = "''";
									}
								} else {
 						                       	if (array_key_exists($attr_value['mapfrom'], $product_data)){
										$attr_line .= ",'".$attr_value['prefix']." ".$product_data[$attr_value['mapfrom']]." ".$attr_value['suffix']."'";
									} else {
										$attr_line .= ",''";
									}	
								}
							}
						}
					}	
				}
				$attr_line = trim($attr_line, "'");
				$products[] = array ( $attr_line );

				/**
				 * Build an array needed for the adding Childs in the XML productfeed
				 */
				foreach( array_keys($project_config['attributes']) as $attribute_key ){
					if(!isset($old_attributes_config)){
						if(!$xml_product){
							$xml_product = array (
								$attribute_key => $product_data[$attribute_key]
							);
						} else {
							if(isset($product_data[$attribute_key])){
								$xml_product = array_merge($xml_product, array($attribute_key => $product_data[$attribute_key]));
							}
						}
					} else {
						foreach($old_attributes_config as $attr_key => $attr_value){
							if ($attr_value['mapfrom'] == $attribute_key){
								if(!isset($xml_product)){
									$xml_product = array (
										$attr_value['attribute'] => "$attr_value[prefix] ". $product_data[$attr_value['mapfrom']] ." $attr_value[suffix]"
									);
								} else {
									if(key_exists($attr_value['mapfrom'],$product_data)){
										$xml_product[$attr_value['attribute']] = "$attr_value[prefix] ". $product_data[$attr_value['mapfrom']] ." $attr_value[suffix]";	
									}
								}
							}
						}
					}
				}

				foreach($xml_product as $key_product => $value_product){
					if (preg_match("/custom_attributes_attribute_/", $key_product)){
						$pieces = explode("custom_attributes_attribute_",$key_product);
						unset($xml_product[$key_product]);
						$xml_product[$pieces[1]] = $value_product;
					} elseif (preg_match("/product_attributes_/", $key_product)){
						$pieces = explode("product_attributes_",$key_product);
						unset($xml_product[$key_product]);
						$xml_product[$pieces[1]] = $value_product;
					}
				}

				if(!$xml_piece){
					$xml_piece = array ($xml_product);
					unset($xml_product);
				} else {
					array_push ($xml_piece, $xml_product);
					unset($xml_product);
				}
				unset($product_data);	
			}
		endwhile;
		wp_reset_query();

		/**
		 * Update processing status of project
		 */
		$project_updated = WooSEA_Get_Products::woosea_project_update($project_config['project_hash'], $offset_step_size, $xml_piece);

		/**
		 * Write row to CSV/TXT or XML file
		 */

		if($project_config['fileformat'] != 'xml'){
			unset($products[0]);
			$file = WooSEA_Get_Products::woosea_create_csvtxt_feed ( array_filter($products), $project_config, 'false' );
		} else {
			if(is_array($xml_piece)){
				$file = WooSEA_Get_Products::woosea_create_xml_feed ( array_filter($xml_piece), $project_config, 'false' );
				unset($xml_piece);
			}
			unset($products);
		}

		/**
	  	 * Ready creating file, clean up our feed configuration mess now
		 */
		 delete_option('attributes_dropdown');
		 delete_option('channel_attributes');
	}

	/**
 	* Update processing statistics of batched projects 
 	*/
	public function woosea_project_update($project_hash, $offset_step_size, $xml_piece){
        	$feed_config = get_option( 'cron_projects' );
		$nr_projects = count ($feed_config);
        	
		foreach ( $feed_config as $key => $val ) {
                	if ($val['project_hash'] == $project_hash){
				$nrpr = $feed_config[$key]['nr_products_processed'];
				$nr_prods_processed = $nrpr+$offset_step_size;

				if(is_array($xml_piece)){
					// End of processing batched feed
					if($nrpr >= $feed_config[$key]['nr_products']){
					//if($nr_prods_processed > $feed_config[$key]['nr_products']){
						// Set counters back to 0
						$feed_config[$key]['nr_products_processed'] = 0;
					
						// Set processing status on ready
						$feed_config[$key]['running'] = "ready";
						$project_data['last_updated'] = date("d M Y H:i");

						$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
						delete_option( $batch_project );

						// In 2 minutes from now check the amount of products in the feed and update the history count
						wp_schedule_single_event( time() + 120, 'woosea_update_project_stats', array($val['project_hash']) );
					} else {
						$feed_config[$key]['nr_products_processed'] = $nr_prods_processed;
						$feed_config[$key]['running'] = "processing";
			
						// Set new scheduled event for next batch in 3 seconds
        					if (! wp_next_scheduled ( 'woosea_create_batch_event', array($feed_config[$key]['project_hash']) ) ) {
							wp_schedule_single_event( time() + 3, 'woosea_create_batch_event', array($feed_config[$key]['project_hash']) );
							$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
							update_option( $batch_project, $val);
						}
					}
				} else {
					// Fall-back: no more products in xml_piece, stop batching process
				
					// Set counters back to 0
					$feed_config[$key]['nr_products_processed'] = 0;
					
					// Set processing status on ready
					$feed_config[$key]['running'] = "ready";
					$project_data['last_updated'] = date("d M Y H:i");

					$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
					delete_option( $batch_project );
				}
                	}
        	}
		$nr_projects_cron = count ( get_option ( 'cron_projects' ) );

		/**
		 * Only update the cron_project when no new project was created during the batched run otherwise the new project will be overwritten and deleted
		 */
		if ($nr_projects == $nr_projects_cron){
        		update_option( 'cron_projects', $feed_config);
		}
	}

	/**
	 * Make product availability readable
	 */
    	public function clean_availability( $available ) {
    		if ($available) {
        		if ($available == 'instock') {
                		return "in stock";
            		} elseif ($available == 'outofstock') {
                		return "out of stock";
            		}
        	}	
        	return "out of stock";
    	}

	/**
	 * Check if the channel requires unique key/field names and change when needed
	 */
	private function get_alternative_key ($channel_attributes, $original_key) {
		$alternative_key = $original_key;

		if(!empty($channel_attributes)){
			foreach ($channel_attributes as $k => $v){
				foreach ($v as $key => $value){
					if(array_key_exists("woo_suggest", $value)){				
						if ($original_key == $value['woo_suggest']){
							$alternative_key = $value['feed_name'];
						}
					}
				} 
			}
		}
		return $alternative_key;
	}	

	/**
	 * Make product quantity readable
	 */
    	public function clean_quantity( $id, $name ) {
        	$quantity = $this->get_attribute_value( $id, $name );
        	if ($quantity) {
            		return $quantity + 0;
        	}
        	return "0";
    	}

	/**
	 * Make start and end sale date readable
	 */
    	public function get_sale_date($id, $name) {
        	$date = $this->get_attribute_value($id, $name);
        	if ($date) {
            		return date("Y-m-d", $date);
        	}
        	return false;
    	}

	/**
	 * Get product stock
	 */
    	public function get_stock( $id ){
        	$status=$this->get_attribute_value($id,"_stock_status");
        	if ($status) {
            		if ($status == 'instock') {
                		return "in stock";
            		} elseif ($status == 'outofstock') {
                		return "out of stock";
            		}
        	}
        	return "out of stock";
    	}

	/**
	 * Create proper format image URL's
	 */
	public function get_image_url($image_url = ""){
        	if (!empty($image_url)) {
            		if (substr(trim($image_url), 0, 4) === "http" || substr(trim($image_url), 0,5) === "https" || substr(trim($image_url), 0, 3) === "ftp" || substr(trim($image_url), 0, 4) === "sftp") {
                		return rtrim($image_url, "/");
            		} else {
                		$base = get_site_url();
                		$image_url = $base . $image_url;
                		return rtrim($image_url, "/");
            		}
		}
        	return $image_url;
	}

	/**
     	 * Get attribute value
     	 */
    	public function get_attribute_value( $id, $name ){
        	if (strpos($name, 'attribute_pa') !== false) {
        		$taxonomy = str_replace("attribute_","",$name);
            		$meta = get_post_meta($id,$name, true);
            		$term = get_term_by('slug', $meta, $taxonomy);
            		return $term->name;
        	} else {
            		return get_post_meta($id, $name, true);
        	}
    	}
	/**
	 * Execute category taxonomy mappings
	 */
        private function woocommerce_sea_mappings( $project_mappings, $product_data ){
		$original_cat = $product_data['categories'];
		$tmp_cat = "";
		$match = "false";

		foreach ($project_mappings as $pm_key => $pm_array){
		
			// If case-sensitve is off than lowercase both the criteria value
			if (array_key_exists('cs', $pm_array)){

				if ($pm_array['cs'] != "on"){
					$value = strtolower($value);
					$pm_array['criteria'] = strtolower($pm_array['criteria']);
				}
			}				

			// Category mapping based on productname
			if($pm_array['attribute'] == "title"){
				if($pm_array['condition'] == "="){
					if($product_data['title'] == $pm_array['criteria']){
						$category_pieces = explode("-", $pm_array['map_to_category']);
						$tmp_cat = $category_pieces[0];
						$match = "true";
					} else {
						$product_data['categories'] = "";
					}
				} elseif ($pm_array['condition'] == "contains"){
					if (preg_match("/$pm_array[criteria]/i","$product_data[title]")){
						$category_pieces = explode("-", $pm_array['map_to_category']);
						$tmp_cat = $category_pieces[0];
						$match = "true";
					} else {
						$product_data['categories'] = "";
					}
				}
			// Category mapping based on category
			} else {
				if($pm_array['condition'] == "="){
					$cat_pieces = explode ("||",$original_cat);
					if (count($cat_pieces) > 0){
						foreach ($cat_pieces as $k_piece => $v_piece){
							if($v_piece == $pm_array['criteria']){
								$category_pieces = explode("-", $pm_array['map_to_category']);
								$tmp_cat = $category_pieces[0];
								$match = "true";
							} else {
								$product_data['categories'] = "";
							}
						}
					} else {
						if($original_cat == $pm_array['criteria']){
							$category_pieces = explode("-", $pm_array['map_to_category']);
							$tmp_cat = $category_pieces[0];
							$match = "true";
						}
					}
				} elseif ($pm_array['condition'] == "contains"){
					if (preg_match("/$pm_array[criteria]/i","$original_cat")){
						$category_pieces = explode("-", $pm_array['map_to_category']);
						$tmp_cat = $category_pieces[0];
						$match = "true";
					} else {
						$product_data['categories'] = "";
					}
				}
			}
		}
	
		if($match == "true"){
			$product_data['categories'] = $tmp_cat;
		}

		return $product_data;
	}

	/**
	 * Execute project rules
	 */
        private function woocommerce_sea_rules( $project_rules, $product_data ){
		$allowed = "1";

		foreach ($product_data as $pd_key => $pd_value){

			foreach ($project_rules as $pr_key => $pr_array){
				// Check is there is a rule on specific attributes
				if(in_array($pd_key, $pr_array)){

					// If case-sensitve is off than lowercase both the criteria and attribute value
					if (array_key_exists('cs', $pr_array)){
						if ($pr_array['cs'] != "on"){
							$pd_value = strtolower($pd_value);
							$pr_array['criteria'] = strtolower($pr_array['criteria']);
						}
					}				

					if (is_numeric($pd_value)){
						// Rules for numeric values	
						switch ($pr_array['condition']) {
							case($pr_array['condition'] = "contains"):
								if ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "containsnot"):
								if ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "="):
								if (($pd_value == $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value != $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "!="):
								if (($pd_value != $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value == $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = ">"):
								if (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value <= $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
    								break;
							case($pr_array['condition'] = ">="):
								if (($pd_value >= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "<"):
								if (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "=<"):
								if (($pd_value <= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							default:
								break;
						}
					} else {
						// Rules for string values
						switch ($pr_array['condition']) {
							case($pr_array['condition'] = "contains"):
								if ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "containsnot"):
								if ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "="):
								if (($pr_array['criteria'] == "$pd_value") && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pr_array['criteria'] != "$pd_value") && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "!="):
								if (($pr_array['criteria'] != "$pd_value") && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pr_array['criteria'] == "$pd_value") && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = ">"):
								// Use a lexical order on relational string operators
								if (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = ">="):
								// Use a lexical order on relational string operators
								if (($pd_value >= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "<"):
								// Use a lexical order on relational string operators
								if (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							case($pr_array['condition'] = "=<"):
								// Use a lexical order on relational string operators
								if (($pd_value <= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
									$allowed = 0;
								} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
									$allowed = 0;
								}
								break;
							default:
								break;
						}
					}
				}
			}
		}
		if ($allowed < 1){
			$product_data = array();
			$product_data = null;
		}
		return $product_data;
	}
}
?>
