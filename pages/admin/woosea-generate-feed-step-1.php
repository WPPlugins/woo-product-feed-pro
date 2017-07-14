<?php
/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!';
}
add_filter('admin_footer_text', 'my_footer_text');

/**
 * Create notification object
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
$notifications_box = $notifications_obj->get_admin_notifications ( '1', 'false' );

/**
 * Update project configuration 
 */
if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data(sanitize_text_field($_GET['project_hash']));
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_GET['channel_hash']));
        $manage_project = "yes";
	$nr_rows = count($project['mappings']);
} else {
        $project = WooSEA_Update_Project::update_project($_POST);
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_POST['channel_hash']));
	$nr_rows = 1;
}
?>

	<div class="wrap">
		<div class="form-style-2">
			<div class="form-style-2-heading">Category mapping</div>

                	<div class="<?php _e($notifications_box['message_type']); ?>">
                        	<p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                	</div>

			<form action="" method="post">
			<table id="woosea-ajax-mapping-table" class="woo-product-feed-pro-table" border="1">	
				<thead>
            				<tr>
                				<th></th>
                				<th>IF</th>
                				<th>Condition</th>
                				<th>Value</th>
						<th>Case-sensitive</th>
						<th><?php print "$channel_data[name]";?> category</th>
            				</tr>
        			</thead>
       
 					<tbody class="woo-product-feed-pro-body"> 
					<?php 
					for ($x = 0; $x < $nr_rows; $x++) {
						if ($nr_rows > 1){
							$criteria = $project['mappings'][$x]['criteria'];
							$map_to_category = $project['mappings'][$x]['map_to_category'];
						} else {
							$criteria = "";
							$map_to_category = "";
						}
					?> 
					<tr>
                				<td><input type="hidden" name="mappings[<?php print "$x";?>][rowCount]" value="<?php print "$x";?>"><input type="checkbox" name="record" class="checkbox-field"></td>
                				<td>
							<select name="mappings[<?php print "$x";?>][attribute]" id="mapfrom">
								<option></option>
								<optgroup label='Main attributes'><strong>Main attributes</strong>
								<?php
								if (isset($project['mappings'][$x]['attribute']) AND ($project['mappings'][$x]['attribute'] == "categories")){
									print "<option value='categories' selected>Category</option>";
									print "<option value='title'>Product name</option>";
								} elseif (isset($project['mappings'][$x]['attribute']) AND ($project['mappings'][$x]['attribute'] == "title")){
									print "<option value='categories'>Category</option>";
									print "<option value='title' selected>Product name</option>";
								} else {
									print "<option value='categories' selected>Category</option>";
									print "<option value='title'>Product name</option>";
	
								}
								?>
								</optgroup>							
							</select>	
						</td>
                				<td>
							<select name="mappings[<?php print "$x";?>][condition]" class="select-field" id="mappingcondition">
								<?php
								if (isset($project['mappings'][$x]['condition']) AND ($project['mappings'][$x]['condition'] == "=")){
									print "<option value='=' selected>is equal to</option>";
									print "<option value='contains'>contains</option>";
								} elseif (isset($project['mappings'][$x]['condition']) AND ($project['mappings'][$x]['condition'] == "contains")){
									print "<option value='='>is equal to</option>";
									print "<option value='contains' selected>contains</option>";
								} else {
									print "<option value='=' selected>is equal to</option>";
									print "<option value='contains'>contains</option>";
								}
								?>
							</select>	
						</td>
						<td>
							<div style="display: block;">
								<input type="text" id="mappingvalue" name="mappings[<?php print "$x";?>][criteria]" class="input-field-large" value="<?php print "$criteria";?>">
								<div id="mappingvalueerror"></div>
							</div>
						</td>
						<td>
							<?php
							if (isset($project['mappings'][$x]['cs'])){
								print "<input type=\"checkbox\" name=\"mappings[$x][cs]\" class=\"checkbox-field\" alt=\"Case sensitive\" checked>";
							} else {
								print "<input type=\"checkbox\" name=\"mappings[$x][cs]\" class=\"checkbox-field\" alt=\"Case sensitive\">";
							}
							?>
						</td>
						<td>
							<input type="text" id="autocomplete" name="mappings[<?php print "$x";?>][map_to_category]" class="input-field-large" value="<?php print "$map_to_category";?>">
						</td>
					
					</tr>
					<?php
					}
					?>
        			</tbody>
                                
				<tr>
				<td colspan="6">
                                <input type="hidden" id="channel_hash" name="channel_hash" value="<?php print "$project[channel_hash]";?>">
			  	<?php
                                	if(isset($manage_project)){
                                        ?>
                                             	<input type="hidden" name="project_update" id="project_update" value="yes" />
                                             	<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
                                             	<input type="hidden" name="step" value="100">
                               			<input type="button" class="delete-mapping" value="- Delete mapping">&nbsp;<input type="button" class="add-mapping" value="+ Add mapping">&nbsp;<input type="submit" value="Save mappings" />
					<?php
                                      	} else {
                                       	?>
						<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
                		                <input type="hidden" name="step" value="4">
                               			<input type="button" class="delete-mapping" value="- Delete mapping">&nbsp;<input type="button" class="add-mapping" value="+ Add mapping">&nbsp;<input type="submit" value="Save mappings" />
					<?php
					}
					?>
				</td>
				</tr>
			</table>
		</form>
	</div>
</div>
