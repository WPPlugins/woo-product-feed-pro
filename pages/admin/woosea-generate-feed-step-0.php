<?php
/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!';
}
add_filter('admin_footer_text', 'my_footer_text');

/**
 * Create notification object and get message and message type as WooCommerce is inactive
 * also set variable allowed on 0 to disable submit button on step 1 of configuration
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $notifications_box = $notifications_obj->get_admin_notifications ( "9", "false" );
	$locale = "NL";
} else {
	$notifications_box = $notifications_obj->get_admin_notifications ( '0', 'false' );
	$default = wc_get_base_location();
	$locale = apply_filters( 'woocommerce_countries_base_country', $default['country'] );
}

/**
 * Get shipping zones
 */
$shipping_zones = WC_Shipping_Zones::get_zones();
$nr_shipping_zones = count($shipping_zones);

/**
 * Get channels
 */
$channel_configs = get_option ('channel_statics');

/**
 * Get countries and channels
 */
$channel_obj = new WooSEA_Attributes;
$countries = $channel_obj->get_channel_countries();
$channels = $channel_obj->get_channels($locale);
$special_attributes_clean = $channel_obj->get_special_attributes_clean();

if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data($_GET['project_hash']);
	$manage_project = "yes";
        $special_attributes = $channel_obj->get_special_attributes_dropdown();
}
?>

	<div class="wrap">
		<div class="form-style-2">

		<div class="form-style-2-heading">File name, format and channel</div>
       
		<?php 
		if(!isset($manage_project)){
		?>         
		<div class="<?php _e($notifications_box['message_type']); ?>">
                	<p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                </div>
		<?php
		}
		?>

		<form action="" id="myForm" method="post" name="myForm">
		<table class="woo-product-feed-pro-table">
			<tbody class="woo-product-feed-pro-body">
				<div id="projecterror"></div>
				<tr>
					<td width="30%"><span>Project name: <span class="required">*</span></span></td>
					<td>
						<div style="display: block;">
							<?php
							if (isset($project)){
								print"<input type=\"text\" class=\"input-field\" id=\"projectname\" name=\"projectname\" value=\"$project[projectname]\"/> <div id=\"projecterror\"></div>";
							} else {
								print"<input type=\"text\" class=\"input-field\" id=\"projectname\" name=\"projectname\"/> <div id=\"projecterror\"></div>";
							}
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td><span>Country:</span></td>
					<td>
						<?php
						if (isset($manage_project)){
							print"<select name=\"countries\" id=\"countries\" class=\"select-field\" disabled>";
						} else {
							print"<select name=\"countries\" id=\"countries\" class=\"select-field\">";
						}
						?>
						<option>Select a country</option>
						<?php
							foreach ($countries as $value){
								if((isset($project)) AND ($value == $project['countries'])){
									print "<option value=\"$value\" selected>$value</option>";
								} else {
									print "<option value=\"$value\">$value</option>";
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><span>Channel:</span></td>
					<td>
						<?php
						if (isset($manage_project)){
							print "<select name=\"channel_hash\" id=\"channel_hash\" class=\"select-field\" disabled>";
							print "<option value=\"$project[channel_hash]\" selected>$project[name]</option>";
							print "</select>";
						} else {
							$customfeed = "";
							$advertising = "";
							$marketplace = "";
							$shopping = "";
							$optgroup_customfeed = 0;
							$optgroup_advertising = 0;
							$optgroup_marketplace = 0;
							$optgroup_shopping = 0;

							print "<select name=\"channel_hash\" id=\"channel_hash\" class=\"select-field\">";

							foreach ($channels as $key=>$val){
								if ($val['type'] == "Custom Feed"){
									if ($optgroup_customfeed == 1){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$customfeed .= "<option value=\"$val[channel_hash]\" selected>$key</option>";
										} else {
											$customfeed .= "<option value=\"$val[channel_hash]\">$key</option>";
										}	
									} else {	
										$customfeed =  "<optgroup label=\"Custom Feed\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$customfeed .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$customfeed .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}									
										$optgroup_customfeed = 1;
									}
								}

								if ($val['type'] == "Advertising"){
									if ($optgroup_advertising == 1){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$advertising .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$advertising .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
									} else {	
										$advertising = "<optgroup label=\"Advertising\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$advertising .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$advertising .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
										$optgroup_advertising = 1;
									}
								}
	
								if ($val['type'] == "Marketplace"){
									if ($optgroup_marketplace == 1){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$marketplace .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$marketplace .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
									} else {	
										$marketplace = "<optgroup label=\"Marketplace\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$marketplace .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$marketplace .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
										$optgroup_marketplace = 1;
									}
								}

								if ($val['type'] == "Comparison shopping engine"){
									if ($optgroup_shopping == 0){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$shopping .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$shopping .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
									} else {	
										$shopping = "<optgroup label=\"Comparison Shopping Engine\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$shopping .= "<option value=\"$val[channel_hash]\">$key</option>";	
										} else {
											$shopping .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										}
										$optgroup_shopping = 1;
									}
								}
							}
							print "$customfeed";
							print "$advertising";
							print "$marketplace";
							print "$shopping";
							print "</select>";
						}
						?>
					</td>
				</tr>
				<?php
				if($nr_shipping_zones > 1){
				?>
				<tr id="shipping_zones">
					<td><span>Shipping zone:</span></td>
					<td>
	                                	<label class="switch">
                                                        <?php
                                                        if(isset($project['shipping_zone'])){
                                                                print "<input type=\"checkbox\" id=\"shipping_zone\" name=\"shipping_zone\" class=\"checkbox-field\" checked>";
                                                        } else {
                                                                print "<input type=\"checkbox\" id=\"shipping_zone\" name=\"shipping_zone\" class=\"checkbox-field\">";
                                                        }
                                                        ?>
                                                        <div class="slider round"></div>
                                                </label>
					</td>
				</tr>
				<?php
				}
				?>
				<tr id="product_variations">
					<td><span>Include product variations:</span></td>
					<td>
                                                <label class="switch">
                                                        <?php
                                                        if(isset($project['product_variations'])){
                                                                print "<input type=\"checkbox\" id=\"variations\" name=\"product_variations\" class=\"checkbox-field\" checked>";
                                                        } else {
                                                                print "<input type=\"checkbox\" id=\"variations\" name=\"product_variations\" class=\"checkbox-field\">";
                                                        }
                                                        ?>
                                                        <div class="slider round"></div>
                                                </label>
					</td>
				</tr>
				<?php
                             	if(isset($project['product_variations'])){
					

					print "<tr id=\"attribute_variation\">";
					print "<td><span>Product name append:</span></td>";
					print "<td>";
					print "<select name=\"productname_append\" class=\"select-field\">";
					print "<option></option>";
					print "<optgroup label=\"Custom attributes\">";

					foreach($special_attributes_clean as $key => $value){
                                        	if($key != "custom_attributes_total_sales"){
					        	$value = ucfirst(str_replace("attribute","",$value));
	
							if($project['productname_append'] == $key){
								print "<option value=\"$key\" selected>$value</option>";
							} else {
								print "<option value=\"$key\">$value</option>";
							}
						}
					}
					print "</optgroup>";	
					print "</select>";
					print "</td>";
					print "</tr>";
				}
				?>
				<tr id="file">
					<td><span>File format:</span></td>
					<td>
						<select name="fileformat" id="fileformat" class="select-field">
							<?php
							$format_arr = array("csv","txt","xml");
							foreach ($format_arr as $format){
								$format_upper = strtoupper($format);
								if ((isset($project)) AND ($format == $project['fileformat'])){
									print "<option value=\"$format\" selected>$format_upper</option>";
								} else {
									print "<option value=\"$format\">$format_upper</option>";
								}
							}	
							?>
						</select>
					</td>
				</tr>
				<tr id="delimiter">
					<td><span>Delimiter:</span></td>
					<td>
						<select name="delimiter" class="select-field">
							<?php
							$delimiter_arr = array(",","|",";");
							foreach ($delimiter_arr as $delimiter){
								if((isset($project)) AND (array_key_exists('delimiter', $project)) AND ($delimiter == $project['delimiter'])){
									print "<option value=\"$delimiter\" selected>$delimiter</option>";
								} else {
									print "<option value=\"$delimiter\">$delimiter</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><span>Refresh interval:</span></td>
					<td>
						<select name="cron" class="select-field">
							<?php
							$refresh_arr = array("daily","twicedaily","hourly");
							foreach ($refresh_arr as $refresh){
								$refresh_upper = ucfirst($refresh);
								if ((isset($project)) AND ($refresh == $project['cron'])){
									print "<option value=\"$refresh\" selected>$refresh_upper</option>";
								} else {
									print "<option value=\"$refresh\">$refresh_upper</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						if (isset($project)){
							print "<input type=\"hidden\" name=\"project_hash\" id=\"project_hash\" value=\"$project[project_hash]\" />";
							print "<input type=\"hidden\" name=\"channel_hash\" id=\"channel_hash\" value=\"$project[channel_hash]\" />";
							print "<input type=\"hidden\" name=\"project_update\" id=\"project_update\" value=\"yes\" />";
							print "<input type=\"hidden\" name=\"step\" id=\"step\" value=\"100\" />";
							print "<input type=\"submit\" id=\"goforit\" value=\"Save\" />";
					
						} else {
							print "<input type=\"hidden\" name=\"step\" id=\"step\" value=\"99\" />";
							print "<input type=\"submit\" id=\"goforit\" value=\"Save & continue\" />";
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		</div>
	</div>
