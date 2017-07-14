<?php
$cron_projects = get_option( 'cron_projects' );
$error = "false";

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
} else {
        $notifications_box = $notifications_obj->get_admin_notifications ( '8', 'false' );
}
?>
<div class="wrap">
        <div class="form-style-2">
        <table class="woo-product-feed-pro-table">
                <tbody class="woo-product-feed-pro-body">
                        <div class="form-style-2-heading">Manage feeds</div>

                        <div class="<?php _e($notifications_box['message_type']); ?>">
                                <p><?php _e($notifications_box['message'], 'sample-text-domain' ); ?></p>
                        </div>
	
			<div id="dialog" title="Confirmation required">
  				<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span> Are you sure you want to delete this project and remove the product feed?
			</div>
	
			<div id="refresh-dialog" title="Confirmation required">
  				<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span> Are you sure you want to refresh the product feed?
			</div>
	
			<tr>
				<td><strong>Active</strong></td>
				<td><strong>Channel</strong></td>
				<td><strong>Project name</strong></td>
				<td><strong>Format</strong></td>
				<td><strong>Refresh interval</strong></td>
				<td><strong>Status</strong></td>
				<td></td>
			</tr>
	
			<?php
			if($cron_projects){
				$toggle_count = 1;
				$class = "";

				foreach ($cron_projects as $key=>$val){

					if($val['active'] == "true"){
						$checked = "checked";
						$class = "header";
					} else {
						$checked = "";
						$class = "header strikethrough";
					}

					if($val['filename']){
						$projectname = ucfirst($val['projectname']);
					?>
					<form action="" method="post">
					<tr class="<?php print "$class";?>">
						<td>
                                                <label class="switch">
                                                        <input type="hidden" name="manage_record" value="<?php print "$val[project_hash]";?>"><input type="checkbox" id="project_active" name="project_active[]" class="checkbox-field" value="<?php print "$val[project_hash]";?>" <?php print "$checked";?>>
                                                        <div class="slider round"></div>
                                                </label>
						</td>
						<td><span><?php print "$val[name]";?></span></td>
						<td><span><?php print "$projectname";?></span></td>
						<td><span><?php print "$val[fileformat]";?></span></td>
						<td><span><?php print "$val[cron]";?></span></td>
						<?php
							if ($val['running'] == "processing"){
								$proc_perc = round(($val['nr_products_processed']/$val['nr_products'])*100);
								print "<td><span class=\"blink_me\">$val[running] ($proc_perc%)</span></td>";
							} else {
								print "<td><span class=\"blink_off blink_$val[project_hash]\">$val[running]</span></td>";
							}
						?>
						<td>
							<div class="actions">
								<span class="gear ui-icon ui-icon-gear" id="gear_<?php print "$val[project_hash]";?>" title="project settings"></span>
								<?php 
								if ($val['running'] != "processing"){
								?>
									<span class="trash ui-icon ui-icon-trash" id="trash_<?php print "$val[project_hash]";?>" title="delete project and productfeed"></span>
									<?php
									if ($val['active'] == "true"){
										print "<span class=\"ui-icon ui-icon-refresh\" id=\"refresh_$val[project_hash]\" title=\"manually refresh productfeed\"></span>";
										print "<a href=\"$val[external_file]\" target=\"_blank\"><span class=\"ui-icon ui-icon-arrowthickstop-1-s\" id=\"download\" title=\"download productfeed\"></span></a>";
									}?>
								<?php
								}
								?>	
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="8">
							<div>
								<table class="inline_manage">

									<?php
									if ($val['running'] == "ready"){
									?>
									<tr>
										<td>
											<strong>Change settings</strong><br/>
											<span class="ui-icon ui-icon-caret-1-e"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=0&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>">General settings</a><br/>
											<?php
											if ($val['fields'] == "standard"){
												print "<span class=\"ui-icon ui-icon-caret-1-e\"></span> <a href=\"admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=2&project_hash=$val[project_hash]&channel_hash=$val[channel_hash]\">Attribute selection</a></br/>";
											} else {
												print "<span class=\"ui-icon ui-icon-caret-1-e\"></span> <a href=\"admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=7&project_hash=$val[project_hash]&channel_hash=$val[channel_hash]\">Attribute mapping</a><br/>";
											}
											
											if ($val['taxonomy'] != "none"){
												print "<span class=\"ui-icon ui-icon-caret-1-e\"></span> <a href=\"admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=1&project_hash=$val[project_hash]&channel_hash=$val[channel_hash]\">Category mapping</a><br/>";
											}
											?>
											<span class="ui-icon ui-icon-caret-1-e"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=4&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>">Feed filter rules</a><br/>
											<span class="ui-icon ui-icon-caret-1-e"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=5&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>">Google Analytis settings</a><br/>
									</tr>
									<?php
									}
									?>
									<tr>
										<td>
											<strong>Feed statistics</strong><br/>
											<span class="ui-icon ui-icon-caret-1-e"></span> <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php&action=edit_project&step=8&project_hash=<?php print "$val[project_hash]";?>&channel_hash=<?php print "$val[channel_hash]";?>">Amount of products in feed</a><br/>

										</td>
									</tr>
									<tr>
										<td>
											<strong>Feed URL</strong><br/>
											<?php
											if ($val['active'] == "true"){
											 	print "<span class=\"ui-icon ui-icon-caret-1-e\"></span> $val[external_file]";
											} else {
												print "<span class=\"ui-icon ui-icon-alert\"></span> Whoops, there is no active product feed for this project as the project has been disabled.";
											}
											?>
										</td>
									</tr>
									
								</table>
							</div>
						</td>
					</tr>	
					</form>
					<?php
					$toggle_count++;
					}	
				}
			} else {
				?>
				<tr>
					<td colspan="8"><br/><span class="ui-icon ui-icon-alert"></span> You didn't configured a product feed yet, <a href="admin.php?page=woo-product-feed-pro%2Fwoocommerce-sea.php">please create one first</a>.<br/><br/></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	</div>
</div>
