<?php
/**
 * Settings for Bing Shopping product feeds
 */
class WooSEA_bing_shopping {
	public $bing_attributes;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$bing_attributes = array(
			"Required fields" => array(
				"id" => array(
					"name" => "id",
					"feed_name" => "g:id",
					"format" => "required",
					"woo_suggest" => "id",
				),
            			"title" => array(
					"name" => "title",
					"feed_name" => "g:title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"link" => array(
					"name" => "link",
					"feed_name" => "g:link",
					"format" => "required",
					"woo_suggest" => "link",
            			),
            			"price" => array(
					"name" => "price",
					"feed_name" => "g:price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"description" => array(
					"name" => "description",
					"feed_name" => "g:description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"image_link" => array(
					"name" => "image_link",
					"feed_name" => "g:image_link", 
					"format" => "required",
					"woo_suggest" => "image",
				),
				"shipping" => array(
					"name" => "shipping",
					"feed_name" => "g:shipping", 
					"format" => "required",
				),
			),
			"Item identification" => array(
            			"mpn" => array(
					"name" => "mpn",
					"feed_name" => "g:mpn", 
					"format" => "optional",
            			),
				"gtin" => array(
					"name" => "gtin",
					"feed_name" => "g:gtin",
					"format" => "optional",
				),
				"brand" => array(
					"name" => "brand",
					"feed_name" => "g:brand",
					"format" => "optional",
				),
			),
			"Apparal products" => array(
				"gender" => array(
					"name" => "gender",
					"feed_name" => "g:gender",
					"format" => "optional",
				),
				"age_group" => array(
					"name" => "age_group",
					"feed_name" => "g:age_group",
					"format" => "optional",
				),
				"color" => array(
					"name" => "color",
					"feed_name" => "g:color",
					"format" => "optional",
				),
				"size" => array(
					"name" => "size",
					"feed_name" => "g:size",
					"format" => "optional",
				),
			),
			"Product variants" => array(
				"item_group_id" => array(
					"name" => "item_group_id",
					"feed_name" => "g:item_group_id",
					"format" => "optional",
					"woo_suggest" => "item_group_id",
				),
				"material" => array(
					"name" => "material",
					"feed_name" => "g:material",
					"format" => "optional",
				),
				"pattern" => array(
					"name" => "pattern",
					"feed_name" => "g:pattern",
					"format" => "optional",
				),
			),
			"Other" => array(
				"adult" => array(
					"name" => "adult",
					"feed_name" => "g:adult",
					"format" => "optional",
				),
				"availability" => array(
					"name" => "availability",
					"feed_name" => "g:availability",
					"format" => "optional",
				),
				"product_category" => array(
					"name" => "product_category",
					"feed_name" => "g:product_category",
					"format" => "optional",
				),
				"condition" => array(
					"name" => "condition",
					"feed_name" => "g:condition",
					"format" => "optional",
				),
				"expiration_date" => array(
					"name" => "expiration_date",
					"feed_name" => "g:expiration_date",
					"format" => "optional",
				),
				"multipack" => array(
					"name" => "multipack",
					"feed_name" => "g:multipack",
					"format" => "optional",
				),
				"product_type" => array(
					"name" => "product_type",
					"feed_name" => "g:product_type",
					"format" => "optional",
				),
				"mobile_link" => array(
					"name" => "mobile_link",
					"feed_name" => "g:mobile_link",
					"format" => "optional",
				),
			),
			"Bing attributes" => array(
				"seller_name" => array(
					"name" => "seller_name",
					"feed_name" => "g:seller_name",
					"format" => "optional",
				),
				"bingads_grouping" => array(
					"name" => "bingads_grouping",
					"feed_name" => "g:bingads_grouping",
					"format" => "optional",
				),
				"bingads_label" => array(
					"name" => "bingads_label",
					"feed_name" => "g:bingads_label",
					"format" => "optional",
				),
				"bingads_redirect" => array(
					"name" => "bingads_redirect",
					"feed_name" => "g:bingads_redirect",
					"format" => "optional",
				),
				"custom_label_0" => array(
					"name" => "custom_label_0",
					"feed_name" => "g:custom_label_0",
					"format" => "optional",
				),
				"custom_label_1" => array(
					"name" => "custom_label_1",
					"feed_name" => "g:custom_label_1",
					"format" => "optional",
				),
				"custom_label_2" => array(
					"name" => "custom_label_2",
					"feed_name" => "g:custom_label_2",
					"format" => "optional",
				),
				"custom_label_3" => array(
					"name" => "custom_label_3",
					"feed_name" => "g:custom_label_3",
					"format" => "optional",
				),
				"custom_label_4" => array(
					"name" => "custom_label_4",
					"feed_name" => "g:custom_label_4",
					"format" => "optional",
				),
			),
			"Sales and promotions" => array(
				"sale_price" => array(
					"name" => "sale_price",
					"feed_name" => "g:sale_price",
					"format" => "optional",
				),
				"sale_price_effective_date" => array(
					"name" => "sale_price_effective_date",
					"feed_name" => "g:sale_price_effective_date",
					"format" => "optional",
				),
				"promotion_ID" => array(
					"name" => "promotion_ID",
					"feed_name" => "g:promotion_ID",
					"format" => "optional",
				),
			),
		);
		return $bing_attributes;
	}
}
?>
