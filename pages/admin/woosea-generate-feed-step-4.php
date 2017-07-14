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
$notifications_box = $notifications_obj->get_admin_notifications ( '4', 'false' );

/**
 * Create product attribute object
 */
$attributes_obj = new WooSEA_Attributes;
$attributes = $attributes_obj->get_product_attributes();

/**
 * Update or get project configuration 
 */
if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data(sanitize_text_field($_GET['project_hash']));
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_GET['channel_hash']));
        $count_rules = count($project['rules']);
        $manage_project = "yes";
} else {
        $project = WooSEA_Update_Project::update_project($_POST);
        $channel_data = WooSEA_Update_Project::get_channel_data(sanitize_text_field($_POST['channel_hash']));
	$count_rules = 1;
}
?>
	<div class="wrap">
		<div class="form-style-2">
			<div class="form-style-2-heading">Feed filter rules</div>

                	<div class="<?php _e($notifications_box['message_type']); ?>">
                        	<p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                	</div>

			<form action="" method="post">
			<table class="woo-product-feed-pro-table" id="woosea-ajax-table" border="1">
				<thead>
            				<tr>
                				<th></th>
                				<th>IF</th>
                				<th>Condition</th>
                				<th>Value</th>
						<th>Case-sensitive</th>
                				<th>Then</th>
            				</tr>
        			</thead>
       
				<tbody class="woo-product-feed-pro-body"> 
					<?php
					for ($x = 0; $x < $count_rules; $x++) {
						if(isset($project['rules'][$x]['criteria'])){
							$criteria = $project['rules'][$x]['criteria'];
						} else {
							$criteria = "";
						}
						?>
           				 	<tr>
                					<td><input type="hidden" name="rules[<?php print "$x";?>][rowCount]" value="<?php print "$x";?>"><input type="checkbox" name="record" class="checkbox-field"></td>
                					<td>
								<select name="rules[<?php print "$x";?>][attribute]" class="select-field">
									<option></option>
									<?php
									foreach ($attributes as $k => $v){
										if (isset($project['rules'][$x]['attribute']) AND ($project['rules'][$x]['attribute'] == $k)){
											print "<option value=\"$k\" selected>$v</option>";
										} else {
											print "<option value=\"$k\">$v</option>";
										}
									}
									?>
								</select>
							</td>
                					<td>
								<select name="rules[<?php print "$x";?>][condition]" class="select-field">
									<?php
									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == "contains")){
										print "<option value=\"contains\" selected>contains</option>";
									} else {
										print "<option value=\"contains\">contains</option>";
									}
									
									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == "containsnot")){
										print "<option value=\"containsnot\" selected>doesn't contain</option>";
									} else {
										print "<option value=\"containsnot\">doesn't contain</option>";
									}

									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == "=")){
										print "<option value=\"=\" selected>is equal to</option>";
									} else {
										print "<option value=\"=\">is equal to</option>";
									}

									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == "!=")){
										print "<option value=\"!=\" selected>is not equal to</option>";
									} else {
										print "<option value=\"!=\">is not equal to</option>";
									}

									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == ">")){
										print "<option value=\">\" selected>is greater than</option>";
									} else {
										print "<option value=\">\">is greater than</option>";
									}

									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == ">=")){
										print "<option value=\">=\" selected>is greater or equal to</option>";
									} else {
										print "<option value=\">=\">is greater or equal to</option>";
									}

									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == "<")){
										print "<option value=\"<\" selected>is less than</option>";
									} else {
										print "<option value=\"<\">is less than</option>";
									}
									
									if (isset($project['rules'][$x]['condition']) AND ($project['rules'][$x]['condition'] == "=<")){
										print "<option value=\"=<\" selected>is less or equal to</option>";
									} else {
										print "<option value=\"=<\">is less or equal to</option>";
									}
									?>
								</select>	
							</td>
							<td>
								<div style="display: block;">
									<input type="text" id="rulevalue" name="rules[<?php print "$x";?>][criteria]" class="input-field-large" value="<?php print "$criteria";?>">
								</div>
							</td>
							<td>
								<?php
								if (isset($project['rules'][$x]['cs'])){
									print "<input type=\"checkbox\" name=\"rules[$x][cs]\" class=\"checkbox-field\" alt=\"Case sensitive\" checked>";
								} else {
									print "<input type=\"checkbox\" name=\"rules[$x][cs]\" class=\"checkbox-field\" alt=\"Case sensitive\">";
								}
								?>
							</td>
                					<td>
								<select name="rules[<?php print "$x";?>][than]" class="select-field">
									<optgroup label='Action'>Action:
									<?php
									if (isset($project['rules'][$x]['than']) AND ($project['rules'][$x]['than'] == "exclude")){
										print "<option value=\"exclude\" selected> Exclude</option>";
									} else {
										print "<option value=\"exclude\"> Exclude</option>";
									}
									
									if (isset($project['rules'][$x]['than']) AND ($project['rules'][$x]['than'] == "include_only")){
										print "<option value=\"include_only\" selected> Include only</option>";
									} else {
										print "<option value=\"include_only\"> Include only</option>";
									}
									?>
									</optgroup>
								</select>
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
							<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
                		                	<input type="hidden" name="step" value="100">
                       	       				<input type="button" class="delete-row" value="- Delete rules">&nbsp;<input type="button" class="add-row" value="+ Add rule">&nbsp;<input type="submit" value="Save" />
						<?php
						} else {
						?>
							<input type="hidden" name="project_hash" value="<?php print "$project[project_hash]";?>">
                		                	<input type="hidden" name="step" value="5">
                       	       				<input type="button" class="delete-row" value="- Delete rules">&nbsp;<input type="button" class="add-row" value="+ Add rule">&nbsp;<input type="submit" value="Save / Continue" />
						<?php
						}
						?>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
