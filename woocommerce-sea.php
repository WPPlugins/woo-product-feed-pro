<?php
/**
 * Plugin Name: WooCommerce Product Feed Pro 
 * Version:     1.1.4
 * Description: Easily configure and maintain your WooCommerce product feeds for Google Shopping / DRM, Facebook remarketing, Bing, Comparison shopping websites and over a 100 channels more.
 * Author:      AdTribes.io
 * Author URI:  http://www.adtribes.io
 * Developer:   Joris Verwater
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wporg
 * Domain Path: /languages
 */

/** 
 * WooCommerce Product Feed Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * WooCommerce Product Feed Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WooCommerce Product Feed Pro. If not, see <http://www.gnu.org/licenses/>.
 */

/** 
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

if (!defined('ABSPATH')) {
   exit;
}

/**
 * Plugin versionnumber, please do not override
 */
define( 'WOOCOMMERCESEA_PLUGIN_VERSION', '1.1.4' );

if ( ! defined( 'WOOCOMMERCESEA_FILE' ) ) {
        define( 'WOOCOMMERCESEA_FILE', __FILE__ );
}

if ( ! defined( 'WOOCOMMERCESEA_PATH' ) ) {
        define( 'WOOCOMMERCESEA_PATH', plugin_dir_path( WOOCOMMERCESEA_FILE ) );
}

if ( ! defined( 'WOOCOMMERCESEA_BASENAME' ) ) {
        define( 'WOOCOMMERCESEA_BASENAME', plugin_basename( WOOCOMMERCESEA_FILE ) );
}

/**
 * Enqueue css assets
 */
function woosea_styles() {
        wp_register_style( 'woosea_admin-css', plugins_url( '/css/woosea_admin.css', __FILE__ ), WOOCOMMERCESEA_PLUGIN_VERSION );
        wp_enqueue_style( 'woosea_admin-css' );

        wp_register_style( 'woosea_jquery_ui-css', plugins_url( '/css/jquery-ui.css', __FILE__ ), WOOCOMMERCESEA_PLUGIN_VERSION );
        wp_enqueue_style( 'woosea_jquery_ui-css' );
}
add_action( 'admin_enqueue_scripts' , 'woosea_styles' );

/**
 * Enqueue js assets
 */
function woosea_scripts() {

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-ui-progressbar');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-dialog');

	// JS files for ChartJS
	wp_register_script( 'woosea_chart-bundle-js', plugin_dir_url( __FILE__ ) . 'js/Chart.bundle.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_chart-bundle-js' );

	// Minimized JS files for ChartJS
	wp_register_script( 'woosea_chart-bundle-min-js', plugin_dir_url( __FILE__ ) . 'js/Chart.bundle.min.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_chart-bundle-min-js' );

	// JS for adding input field validation
	wp_register_script( 'woosea_validation-js', plugin_dir_url( __FILE__ ) . 'js/woosea_validation.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_validation-js' );

	// JS for adding table rows to the rules page
	wp_register_script( 'woosea_rules-js', plugin_dir_url( __FILE__ ) . 'js/woosea_rules.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_rules-js' );

	// JS for adding table rows to the field mappings page
	wp_register_script( 'woosea_field_mapping-js', plugin_dir_url( __FILE__ ) . 'js/woosea_field_mapping.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_field_mapping-js' );

        // JS for the drag, drop and sort functionality
        wp_register_script( 'woosea_sortable-js', plugin_dir_url( __FILE__ ) . 'js/woosea_sortable.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
        wp_enqueue_script( 'woosea_sortable-js' );

	// JS for getting channels
	wp_register_script( 'woosea_channel-js', plugin_dir_url( __FILE__ ) . 'js/woosea_channel.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_channel-js' );

	// JS for manage projects page
	wp_register_script( 'woosea_manage-js', plugin_dir_url( __FILE__ ) . 'js/woosea_manage.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_manage-js' );

	// JS for autocomplete
	wp_register_script( 'woosea_autocomplete-js', plugin_dir_url( __FILE__ ) . 'js/woosea_autocomplete.js', WOOCOMMERCESEA_PLUGIN_VERSION, true  );
	wp_enqueue_script( 'woosea_autocomplete-js' );
}
add_action( 'admin_enqueue_scripts' , 'woosea_scripts' );

/**
 * Required classes
 */
require plugin_dir_path(__FILE__) . 'classes/class-admin-pages-template.php';
require plugin_dir_path(__FILE__) . 'classes/class-cron.php';
require plugin_dir_path(__FILE__) . 'classes/class-get-products.php';
require plugin_dir_path(__FILE__) . 'classes/class-admin-notifications.php';
require plugin_dir_path(__FILE__) . 'classes/class-update-channel.php';
require plugin_dir_path(__FILE__) . 'classes/class-attributes.php';

/**
 * Hook and function that will run during plugin deactivation.
 */
function deactivate_woosea_feed(){
	require plugin_dir_path(__FILE__) . 'classes/class-deactivate-cleanup.php';
    	WooSEA_Deactivate_Cleanup::deactivate_cleanup();
}
register_deactivation_hook(__FILE__, 'deactivate_woosea_feed');

/**
 * Hook and function that will run during plugin activation.
 */
function activate_woosea_feed(){
	require plugin_dir_path(__FILE__) . 'classes/class-activate.php';
    	WooSEA_Activation::activate_checks();
}
register_activation_hook(__FILE__, 'activate_woosea_feed');

/**
 * Register own cron hook(s), it will execute the woosea_create_all_feeds that will generate all feeds on scheduled event
 */
add_action( 'woosea_cron_hook', 'woosea_create_all_feeds'); // create a cron hook

/**
 * Add WooCommerce SEA plugin to Menu
 */
function woosea_menu_addition(){
            add_menu_page(__('WooCommerce Product Feed PRO', 'woosea-feed'), __('Product Feed Pro', 'woosea-feed'), 'manage_options', __FILE__, 'woosea_generate_pages', 'dashicons-chart-bar',99);
            add_submenu_page(__FILE__, __('Feed configuration', 'woosea-feed'), __('Create feed', 'woosea-feed'), 'manage_options', __FILE__, 'woosea_generate_pages');
            add_submenu_page(__FILE__, __('Manage feeds', 'woosea-feed'), __('Manage feeds', 'woosea-feed'), 'manage_options', 'woosea_manage_feed', 'woosea_manage_feed');
}

/**
 * Get the attributes for displaying the attributes dropdown on the rules page
 * Gets all attributes, product, image and attributes
 */
function woosea_ajax() {
	$rowCount = sanitize_text_field($_POST['rowCount']);

	$attributes_dropdown = get_option('attributes_dropdown');
	if (!is_array($attributes_dropdown)){
		$attributes_obj = new WooSEA_Attributes;
		$attributes_dropdown = $attributes_obj->get_product_attributes_dropdown();
        	update_option( 'attributes_dropdown', $attributes_dropdown, '', 'yes');
	}

	$data = array (
		'rowCount' => $rowCount,
		'dropdown' => $attributes_dropdown
	);

	echo json_encode($data);
	wp_die();
}
add_action( 'wp_ajax_woosea_ajax', 'woosea_ajax' );

/**
 * Get the shipping zone countries and ID's
 */
function woosea_shipping_zones(){
	$shipping_options = "";
	$shipping_zones = WC_Shipping_Zones::get_zones();
	foreach ( $shipping_zones as $zone){
		$shipping_options .= "<option value=\"$zone[zone_id]\">$zone[zone_name]</option>";		
	}

	$data = array (
		'dropdown' => $shipping_options,
	);

	echo json_encode($data);
	wp_die();
}
add_action( 'wp_ajax_woosea_shipping_zones', 'woosea_shipping_zones' );

/**
 * Get the dynamic attributes
 */ 
function woosea_special_attributes(){
	$attributes_obj = new WooSEA_Attributes;
	$special_attributes = $attributes_obj->get_special_attributes_dropdown();
	$special_attributes_clean = $attributes_obj->get_special_attributes_clean();

	$data = array (
		'dropdown' => $special_attributes,
		'clean' => $special_attributes_clean,
	); 

	echo json_encode($data);
	wp_die();
}
add_action( 'wp_ajax_woosea_special_attributes', 'woosea_special_attributes' );

/**
 * Get the available channels for a specific country
 */
function woosea_channel() {
	$country = sanitize_text_field($_POST['country']);

	$channel_obj = new WooSEA_Attributes;
	$data = $channel_obj->get_channels($country);

	echo json_encode($data);
	wp_die();
}
add_action( 'wp_ajax_woosea_channel', 'woosea_channel' );

/**
 * Delete a project from cron
 */
function woosea_project_delete(){
	$project_hash = sanitize_text_field($_POST['project_hash']);
        $feed_config = get_option( 'cron_projects' );
	$found = false;

        foreach ( $feed_config as $key => $val ) {
                if ($val['project_hash'] == $project_hash){
			$found = true;
			$found_key = $key;

                	$upload_dir = wp_upload_dir();
                	$base = $upload_dir['basedir'];
                	$path = $base . "/woo-product-feed-pro/" . $val['fileformat'];
                	$file = $path . "/" . sanitize_file_name($val['filename']) . "." . $val['fileformat'];
		}
	}

	if ($found == "true"){
		# Remove project from project array		
		unset($feed_config[$found_key]);
		
		# Update cron
		update_option('cron_projects', $feed_config);

		# Remove project file
		@unlink($file);
	}

}
add_action( 'wp_ajax_woosea_project_delete', 'woosea_project_delete' );

/**
 * Refresh a project 
 */
function woosea_project_refresh(){
	$project_hash = sanitize_text_field($_POST['project_hash']);
        $feed_config = get_option( 'cron_projects' );

        foreach ( $feed_config as $key => $val ) {
                if ($val['project_hash'] == $project_hash){
        		$batch_project = "batch_project_".$project_hash;
			
			if (!get_option( $batch_project )){
        			update_option( $batch_project, $val);
        			$final_creation = woosea_continue_batch($project_hash);
			} else {
        			$final_creation = woosea_continue_batch($project_hash);
			}
		}
	}
}
add_action( 'wp_ajax_woosea_project_refresh', 'woosea_project_refresh' );

/**
 * Change status of a project from active to inactive or visa versa
 */
function woosea_project_status() {
	$project_hash = sanitize_text_field($_POST['project_hash']);
	$active = sanitize_text_field($_POST['active']);
	$feed_config = get_option( 'cron_projects' );

        foreach ( $feed_config as $key => $val ) {
                if ($val['project_hash'] == $project_hash){
                        $feed_config[$key]['active'] = $active;

                	$upload_dir = wp_upload_dir();
                	$base = $upload_dir['basedir'];
                	$path = $base . "/woo-product-feed-pro/" . $val['fileformat'];
                	$file = $path . "/" . sanitize_file_name($val['filename']) . "." . $val['fileformat'];
                }
        }

	// When project is put on inactive, delete the product feed
	if($active == "false"){
		@unlink($file);
	}

	// Regenerate product feed
	if($active == "true"){
		$update_project = woosea_project_refresh($project_hash);
	}

	// Update cron with new project status
        update_option( 'cron_projects', $feed_config);
}
add_action( 'wp_ajax_woosea_project_status', 'woosea_project_status' );

/**
 * Set project history: amount of products in the feed
 **/
function woosea_update_project_history($project_hash){
        $feed_config = get_option( 'cron_projects' );
  	
	foreach ( $feed_config as $key => $project ) {
	       if ($project['project_hash'] == $project_hash){
			$upload_dir = wp_upload_dir();
     			$base = $upload_dir['basedir'];
     			$path = $base . "/woo-product-feed-pro/" . $project['fileformat'];
      			$file = $path . "/" . sanitize_file_name($project['filename']) . "." . $project['fileformat'];

     			if (file_exists($file)) {
        			if(($project['fileformat'] == "csv") || ($project['fileformat'] == "txt")){
               				$fp = file($file);
                      			$raw_nr_products = count($fp);
                      			$nr_products = $raw_nr_products-1; // header row of csv
             		} else {
                     		$xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
                      		if ($project['taxonomy'] == "none"){
                         		$nr_products = count($xml->product);
                       		} else {
                          		$nr_products = count($xml->channel->item);
                      		}
            		}
  		}
        	$count_timestamp = date("d M Y H:i");
       		$number_run = array(
        		$count_timestamp => $nr_products,
      		);

     		$feed_config = get_option( 'cron_projects' );
     		foreach ( $feed_config as $key => $val ) {
      			if (($val['project_hash'] == $project['project_hash']) AND ($val['running'] == "ready")){
      				//unset($feed_config[$key]['history_products']);
             			if (array_key_exists('history_products', $feed_config[$key])){
             				$feed_config[$key]['history_products'][$count_timestamp] = $nr_products;
              			} else {
                			$feed_config[$key]['history_products'] = $number_run;
            			}
      			}
      		}
       		update_option( 'cron_projects', $feed_config);
	}	}
}
add_action( 'woosea_update_project_stats', 'woosea_update_project_history',1,1 );

/**
 * Get the dropdowns for the fieldmapping page
 */
function woosea_fieldmapping_dropdown(){
	$channel_hash = sanitize_text_field($_POST['channel_hash']);
	$rowCount = sanitize_text_field($_POST['rowCount']);
        $channel_data = WooSEA_Update_Project::get_channel_data($channel_hash);

        require plugin_dir_path(__FILE__) . '/classes/channels/class-'.$channel_data['fields'].'.php';
        $obj = "WooSEA_".$channel_data['fields'];
        $fields_obj = new $obj;
        $attributes = $fields_obj->get_channel_attributes();
	$field_options = "<option selected></option>";
 	
	foreach($attributes as $key => $value){
		$field_options .= "<option></option>";
		$field_options .= "<optgroup label='$key'><strong>$key</strong>";
		foreach($value as $k => $v){
               		$field_options .= "<option value='$v[feed_name]'>$k ($v[name])</option>";
		}
	}
 
        $attributes_obj = new WooSEA_Attributes;
        $attribute_dropdown = $attributes_obj->get_product_attributes();

	$attribute_options = "<option selected></option>";
   	foreach($attribute_dropdown as $drop_key => $drop_value){
        	$attribute_options .= "<option value='$drop_key'>$drop_value</option>";
	}

	$data = array (
		'field_options' => $field_options,
		'attribute_options' => $attribute_options,
	);

	echo json_encode($data);
	wp_die();
}
add_action( 'wp_ajax_woosea_fieldmapping_dropdown', 'woosea_fieldmapping_dropdown' );

/**
 * Get the attribute dropdowns for category mapping
 */
function woosea_autocomplete_dropdown() {
	$rowCount = sanitize_text_field($_POST['rowCount']);
	
	$mapping_obj = new WooSEA_Attributes;
	$mapping_dropdown = $mapping_obj->get_mapping_attributes_dropdown();

	$data = array (
		'rowCount' => $rowCount,
		'dropdown' => $mapping_dropdown
	);

	echo json_encode($data);
	wp_die();

}
add_action( 'wp_ajax_woosea_autocomplete_dropdown', 'woosea_autocomplete_dropdown' );

/**
 * Autosuggest categories or productnames for category mapping page
 */
function woosea_autocomplete_mapping() {
	$query = sanitize_text_field($_POST['query']);
	$searchin = sanitize_text_field($_POST['searchin']);
	$condition = sanitize_text_field($_POST['condition']);

	$data = array();	
	$data_raw = array();

	// search on exact productname
	if (($searchin == "title") AND ($condition == "=") OR ($condition == "contains")){
        	$prods = new WP_Query(
                	array(
				's' => $query['term'],
           			'posts_per_page' => -1,
                         	'post_type' => array('product', 'product_variation'),
                              	'post_status' => 'publish',
                            	'fields' => 'ids',
                              	'no_found_rows' => true
                    	)
          	);

                while ($prods->have_posts()) : $prods->the_post();
               		global $product;
			$data_raw[] = $product->get_title();
            	endwhile;
             	wp_reset_query();	
	// search on exact categoryname
	} elseif (($searchin == "categories") AND ($condition == "=")) {
		$taxonomy     = 'product_cat';
  		$orderby      = 'name';  
  		$show_count   = 0;      // 1 for yes, 0 for no
  		$pad_counts   = 0;      // 1 for yes, 0 for no
  		$hierarchical = 1;      // 1 for yes, 0 for no  
  		$title        = '';  
  		$empty        = 0;

  		$args = array(
         		'taxonomy'     => $taxonomy,
         		'orderby'      => $orderby,
         		'show_count'   => $show_count,
         		'pad_counts'   => $pad_counts,
         		'hierarchical' => $hierarchical,
         		'title_li'     => $title,
         		'hide_empty'   => $empty
  		);

		$all_categories = get_categories( $args );
		foreach ($all_categories as $cat) {
    			if($cat->category_parent == 0) {
        			$category_id = $cat->term_id;
				$maincat = $cat->name;
      
        			$args2 = array(
                			'taxonomy'     => $taxonomy,
                			'child_of'     => 0,
                			'parent'       => $category_id,
                			'orderby'      => $orderby,
                			'show_count'   => $show_count,
                			'pad_counts'   => $pad_counts,
                			'hierarchical' => $hierarchical,
                			'title_li'     => $title,
                			'hide_empty'   => $empty
        			);
        			$sub_cats = get_categories( $args2 );
        			if($sub_cats) {
            				foreach($sub_cats as $sub_category) {
                				$maincat .= $sub_category->name;		
            				}   
        			}
    			}
			$maincat = str_replace("&amp;","&",$maincat);      
			$data_raw[] = $maincat;
		}
	} else {
		$data_raw[] = "";
	}

	foreach ($data_raw as $k => $v){
		if (preg_match("/$query[term]/i", $v)){
			$data[] = $v;
		}
	}

	$data = json_encode($data);

	echo $data;
	wp_die();
}
add_action( 'wp_ajax_woosea_autocomplete_mapping', 'woosea_autocomplete_mapping' );

/**
 * Get the category taxonomies for relevant channels
 */
function woosea_autocomplete_taxonomy() {
	$channel_hash = sanitize_text_field($_POST['channel_hash']);
	$query = sanitize_text_field($_POST['query']['term']);

	$channel_data = WooSEA_Update_Project::get_channel_data($channel_hash);
	$data = array();

	$root_dir = plugin_dir_path( __FILE__ );
        $path = $root_dir . "channels/taxonomy";
	$filename = $channel_data['taxonomy'].".txt";
	$file = $path . "/" . $filename;

	$fp = fopen($file, 'r');
	while (!feof($fp)) {
		$line=fgets($fp);
		if (preg_match("/$query/i", $line)) {
			$data[] = $line;
		}
	}

	$data_json = json_encode($data);

	echo $data_json;
	wp_die();
}
add_action( 'wp_ajax_woosea_autocomplete_taxonomy', 'woosea_autocomplete_taxonomy' );

/**
 * Function for serving different HTML templates while configuring the feed
 * Some cases are left blank for future steps and pages in the configurations process
 */
function woosea_generate_pages(){
	if (!$_POST){
		$generate_step = 0;
	} else {
		$from_post = $_POST;
		
		$channel_hash = sanitize_text_field($_POST['channel_hash']);
		$step = sanitize_text_field($_POST['step']);	
		$generate_step = $step;
	}

	if (array_key_exists('step', $_GET)){
		if (array_key_exists('step', $_POST)){
			$generate_step = $step;
		} else {
			$generate_step = sanitize_text_field($_GET["step"]);
		}
	}

	if (isset($_GET['channel_hash'])){
		$channel_hash = sanitize_text_field($_GET['channel_hash']);
	}

        /**
         * Get channel information 
         */
	if ($generate_step){
        	$channel_data = WooSEA_Update_Project::get_channel_data($channel_hash);
	}

	/**
	 * Determing if we need to do field mapping or attribute picking after step 0
	 */
	if ($generate_step == 99){
		if($channel_data['fields'] == "standard"){
			$generate_step = 2;
		} else {
			$generate_step = 7;
		}
	} elseif ($generate_step == 100){
	        /**
       	 	 * Update existing feed configuration with new values from previous step
        	 */
        	$project = WooSEA_Update_Project::reconfigure_project($from_post);

	} elseif ($generate_step == 101){
		/**
         	 * Update project configuration 
         	 */
        	$project_data = WooSEA_Update_Project::update_project($from_post);

        	/**
         	 * Set some last project configs
         	 */
        	$project_data['active'] = true;
        	$project_data['last_updated'] = date("d M Y H:i");
        	$project_data['running'] = "processing";

        	$count_products = wp_count_posts('product', 'product_variation');
        	$project_data['nr_products'] = $count_products->publish;
        	$project_data['nr_products_processed'] = 0;

        	$add_to_cron = WooSEA_Update_Project::add_project_cron($project_data, "donotdo");
        	$batch_project = "batch_project_".$project_data['project_hash'];
        	
		if (!get_option( $batch_project )) {
			// Batch project hook expects a multidimentional array
        		update_option( $batch_project, $project_data);
        		$final_creation = woosea_continue_batch($project_data['project_hash']);
		} else {
        		$final_creation = woosea_continue_batch($project_data['project_hash']);
		}
	}

	/**
	 * Switch to determing what template to use during feed configuration
	 */
	
	switch($generate_step){
		case 0:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-0.php' );
			break;
		case 1:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-1.php' );
			break;
		case 2:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-2.php' );
			break;
		case 3:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-3.php' );
			break;
		case 4:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-4.php' );
			break;
		case 5:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-5.php' );
			break;
		case 6:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-6.php' );
			break;
		case 7:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-generate-feed-step-7.php' );
			break;
		case 8:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-statistics-feed.php' );
			break;
		case 100:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-manage-feed.php' );
			break;
		case 101:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-manage-feed.php' );
			break;
		default:
			load_template( plugin_dir_path( __FILE__ ) . '/pages/admin/woosea-manage-feed.php' );
			break;
	}
}

/**
 * Function used by event scheduling to create feeds 
 * Feed can automatically be generated every hour, twicedaiy or once a day
 */
function woosea_create_all_feeds(){
	$feed_config = get_option( 'cron_projects' );
	$nr_projects = count($feed_config);
	$cron_start_date = date("d M Y H:i");	
	$cron_start_time = time();
	$hour = date('H');

	// Update project configurations with the latest amount of live products
        $count_products = wp_count_posts('product', 'product_variation');
        $nr_products = $count_products->publish;
	
	if(!empty($feed_config)){	
		foreach ( $feed_config as $key => $val ) {

			// Force garbage collection dump
			gc_enable();
			gc_collect_cycles();

			// Only process projects that are active
			if(($val['active'] == "true") AND (!empty($val))){		
		
				if (($val['cron'] == "daily") AND ($hour == 07)){
					$batch_project = "batch_project_".$val['project_hash'];
                        		if (!get_option( $batch_project )){
                                		update_option( $batch_project, $val);
						$start_project = woosea_continue_batch($val['project_hash']);
					} else {
						$start_project = woosea_continue_batch($val['project_hash']);
					}
					unset($start_project);	
				} elseif (($val['cron'] == "twicedaily") AND ($hour == 19 || $hour == 07)){
					$batch_project = "batch_project_".$val['project_hash'];
                        		if (!get_option( $batch_project )){
                                		update_option( $batch_project, $val);
						$start_project = woosea_continue_batch($val['project_hash']);
					} else {
						$start_project = woosea_continue_batch($val['project_hash']);
					}
					unset($start_project);	
				} elseif (($val['cron'] == "twicedaily" || $val['cron'] == "daily") AND ($val['running'] == "processing")){
					// Re-start daily and twicedaily projects that are hanging
					$batch_project = "batch_project_".$val['project_hash'];
                        		if (!get_option( $batch_project )){
                                		update_option( $batch_project, $val);
						$start_project = woosea_continue_batch($val['project_hash']);
					} else {
						$start_project = woosea_continue_batch($val['project_hash']);
					}
					unset($start_project);	
				} elseif ($val['cron'] == "hourly") {
					$batch_project = "batch_project_".$val['project_hash'];
                        		if (!get_option( $batch_project )){
                                		update_option( $batch_project, $val);
						$start_project = woosea_continue_batch($val['project_hash']);
					} else {
						$start_project = woosea_continue_batch($val['project_hash']);
					}
					unset($start_project);	
				}
			}
		}
	}
}

/**
 * Update product amounts for project
 */
function woosea_nr_products($project_hash, $nr_products){
	$feed_config = get_option( 'cron_projects' );

	foreach ( $feed_config as $key => $val ) {
		if ($val['project_hash'] == $project_hash){
			$feed_config[$key]['nr_products'] = $nr_products;
		}
	}
	update_option( 'cron_projects', $feed_config);
}

/**
 * Update cron projects with last update timestamp
 */
function woosea_last_updated($project_hash){
	$feed_config = get_option( 'cron_projects' );

	$last_updated = date("d M Y H:i");

	foreach ( $feed_config as $key => $val ) {
		if ($val['project_hash'] == $project_hash){
        		$upload_dir = wp_upload_dir();
        		$base = $upload_dir['basedir'];
        		$path = $base . "/woo-product-feed-pro/" . $val['fileformat'];
        		$file = $path . "/" . sanitize_file_name($val['filename']) . "." . $val['fileformat'];

			$last_updated = date("d M Y H:i");

			if (file_exists($file)) {
				$last_updated = date("d M Y H:i", filemtime($file));
				$feed_config[$key]['last_updated'] = date("d M Y H:i", filemtime($file));
			} else {
				$feed_config[$key]['last_updated'] = date("d M Y H:i");
			}
		}
	}

	update_option( 'cron_projects', $feed_config);
	return $last_updated;
}

/**
 * Process next batch for product feed
 */
function woosea_continue_batch($project_hash){
	$batch_project = "batch_project_".$project_hash;
	$val = get_option( $batch_project );

	if (!empty($val)){

		$line = new WooSEA_Get_Products;
       		$final_creation = $line->woosea_get_products( $val );
        	$last_updated = woosea_last_updated( $val['project_hash'] );

		// Clean up the single event project configuration
		unset($line);
		unset($final_creation);
		unset($last_updated);
	}
}
add_action( 'woosea_create_batch_event','woosea_continue_batch', 1, 1);

/**
 * Function with initialisation of class for managing existing feeds
 */
function woosea_manage_feed(){
	$html = new Construct_Admin_Pages();
	$html->set_page("woosea-manage-feed");
	echo $html->get_page();
}

/**
 * Function for emptying all projects in cron at once
 * Kill-switch for all configured projects, be carefull!
 */
function woosea_clear(){
	$html = new Construct_Admin_Pages();
	$html->set_page("woosea-clear");
	delete_option( 'cron_projects' );
	echo $html->get_page();
}

/**
 * Add plugin links to Wordpress menu
 */
add_action( 'admin_menu' , 'woosea_menu_addition' );
?>
