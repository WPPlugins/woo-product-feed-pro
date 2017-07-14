<?php
/**
 * Class holding the notification messages and type of notices
 * Returns the message and type of message (info, error, success)
 */
class WooSEA_Get_Admin_Notifications {

	public function __construct() {
		$this->notification_details = array();
	}
	
	public function get_admin_notifications ( $step, $error ) {
	
		switch($step){
			case 0:
				$message = "Please select the country and channel for which you would like to create a new product feed. The channel drop-down will populate with relevant country channels once you selected a country. Filling in a project name is mandatory.";		
				$message_type = "notice notice-info is-dismissible";
				break;
			case 1:
				$message = "Map your products or categories to the categories of your selected channel. For some channels adding their categorisation in the product feed is mandatory. Even when category mappings are not mandatory it is likely your products will get better visibility and higher conversions when mappings have been added.";		
				$message_type = "notice notice-info is-dismissible";
				break;
			case 2:
				$message = "Please drag and drop the attributes you want to be in the product feed from left to right.";		
				$message_type = "notice notice-info is-dismissible";
				break;
			case 3:
				$message = "Mapping your product categories to the channel categories will increase changes of getting all your products listed correctly, thus increase your conversion rates.";		
				$message_type = "notice notice-info is-dismissible";
				break;
			case 4:
				$message = "Create rules so exactly the right products end up in your new feed. These rules are only eligable for the current feed you are configuring and will not be used for other feeds. In-case no rules are created all active products will be added to the feed.";		
				$message_type = "notice notice-info is-dismissible";
				break;
			case 5:
				$message = "Adding Google Analytics tracking-code is not mandatory, it will however enable you to get detailed insights into how your products are performing and allowe you to tweak and tune your campaign making it more profitable. We strongly advise you to add the Google Analytics tracking. If enabled this option will append the Google Analytics UTM parameters to your landingpage URL's.";
				$message_type = "notice notice-info is-dismissible";
				break;
			case 6:
				$message = "Your file is now being created, please be patient. Your feed details will be displayed when generation of the file has been finished.";
				$message_type = "notice notice-info is-dismissible";
				break;
			case 7:
				$message = "For the selected channel the attributes shown below are mandatory, please map them to your product attributes. We've already pre-filled a lot of mappings so all you have to do is check those and map the ones that are left blank or add new ones by hitting the 'Add field mapping' button.";
				$message_type = "notice notice-info is-dismissible";
				break;
			case 8:
				$message = "Manage your projects, such as the mappings and filter rules, below. Hit the refresh icon for the project to run with its new settings or just to refresh the product feed. When a project is being processed it is not possible to make changes to its configuration.";
				$message_type = "notice notice-info is-dismissible";
				break;
			case 9:
				$message = "You cannot create product feeds yet, please install the WooCommerce plugin first.";
				$message_type = "notice notice-error";
				break;
			case 10:
				$message = "The graph shows the amount of products in this product feed, measured after every scheduled and/or manually triggered refresh.";
				$message_type = "notice notice-info is-dismissible";
				break;
		}
		
		$this->notification_details['message'] = $message;
		$this->notification_details['message_type'] = $message_type;
		return $this->notification_details;
	}
}
?>
