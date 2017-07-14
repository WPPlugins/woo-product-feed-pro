jQuery(document).ready(function($) {

        jQuery("#variations").click(function(){
		var variations = ( $( "#variations" ).is(':checked')) ? 1 : 0;

		if(variations == "1"){
               	 	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_special_attributes' }
                	})
                	.done(function( data ) {
                        	data = JSON.parse( data );
                		$('#product_variations').after('<tr id="attribute_variation"><td><i>Add variation to product title:</i><br/>Normally just the product title is uploaded to your product feed. However, when product variations are included we would end up with duplicate product titles. To avoid that and give the channel more information about the product we will append the variation attribute you select to the title.</td><td valign="top"><select name="productname_append" class="select-field">' + data.dropdown + '</select></td></tr>');
	                })
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		} else {
			$('#attribute_variation').remove();
		} 
	});

        jQuery("#shipping_zone").click(function(){
		var variations = ( $( "#shipping_zone" ).is(':checked')) ? 1 : 0;

		if(variations == "1"){
               	 	jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_shipping_zones' }
                	})
                	.done(function( data ) {
                        	data = JSON.parse( data );
                		$('#shipping_zones').after('<tr id="select_shipping_zone"><td><i>Select shipping zone:</i><br/>You have multiple shipping zones configured for your shop. Please select the shipping zone name for the product feed you are creating now so the right shipping cost are added to your product feed.</td><td valign="top"><select name="zone" class="select-field">' + data.dropdown + '</select></td></tr>');
	                })
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});	
		} else {
			$('#select_shipping_zone').remove();
		} 
	});





        jQuery("#channel_hash").on("change", function(){
		var channel_hash = $("#channel_hash").find('option:selected').text();
		if(channel_hash == 'Google Remarketing - DRM'){ // Ugly hack, should be configurable per channel
			$("#fileformat option[value='xml']").remove();
			$("#fileformat option[value='txt']").remove();
		} else {
			$("#fileformat")
				.empty()
				.append('<option value="csv">CSV</option><option value="txt">TXT</option><option value="xml">XML</option>')
			;
		}
	});

        jQuery("#countries").on("change", function(){
		var country = this.value;

                jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_channel', 'country': country }
                })

		.done(function( data ) {
                	data = JSON.parse( data );
					
			var select = $('#channel_hash');
			select.empty();
	
			$.each(data, function(index, val) {
				if(val.type == 'Custom Feed'){
					if($('optgroup[label="Custom Feed"]').length == 0){
						var optgroup_customfeed = $('<optgroup id="CustomFeed">');
						optgroup_customfeed.attr('label', val.type);
						$("#channel_hash").append(optgroup_customfeed);
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#CustomFeed");
					} else {
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#CustomFeed");
					}
				}

				if(val.type == 'Advertising'){
					if($('optgroup[label="Advertising"]').length == 0){
						var optgroup_advertising = $('<optgroup id="Advertising">');
						optgroup_advertising.attr('label', val.type);
						$("#channel_hash").append(optgroup_advertising);
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#Advertising");
					} else {
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#Advertising");
					}
				}

				if(val.type == 'Comparison shopping engine'){
					if($('optgroup[label="Comparison shopping engine"]').length == 0){
						var optgroup_shopping = $('<optgroup id="Shopping">');
						optgroup_shopping.attr('label', val.type);
						$("#channel_hash").append(optgroup_shopping);
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#Shopping");
					} else {
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#Shopping");
					}
				}

				if(val.type == 'Marketplace'){
					if($('optgroup[label="Marketplace"]').length == 0){
						var optgroup_marketplace = $('<optgroup id="Marketplace">');
						optgroup_marketplace.attr('label', val.type);
						$("#channel_hash").append(optgroup_marketplace);
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#Marketplace");
					} else {
						$("<option>").val(val.channel_hash).text(val.name).appendTo("#Marketplace");
					}
				}
			});
		})
        
	        .fail(function( data ) {
                        console.log('Failed AJAX Call :( /// Return Data: ' + data);
                });
	});

        jQuery("#fileformat").on("change", function(){
		var fileformat = this.value;
		
		if (fileformat == "xml"){
			$('#delimiter').remove(); 
		} else {
			// Put delimiter dropdown back
			if($("#delimiter").length == 0){
				$('#file').after('<tr id="delimiter"><td><span>Delimiter:</span></td><td><select name="delimiter" class="select-field"><option value=",">, comma</option><option value="|">| pipe</option></select></td></tr>');
			}
		}	
	});

	var manage_fileformat = jQuery("#fileformat").val();
	var project_update = jQuery("#project_update").val();

	if (manage_fileformat == "xml"){
		$('#delimiter').remove(); 
	}

	if (project_update == "yes"){
		$('#goforit').attr('disabled',false);
	}

});
