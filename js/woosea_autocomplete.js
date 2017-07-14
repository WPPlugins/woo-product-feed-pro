jQuery(document).ready(function($) {

	jQuery( "#autocomplete" ).autocomplete({
		maxShowItems: 5,
		minLength: 2,
		position: { 	my: "right top", 
				at: "right bottom",
				of: "#autocomplete"
				},
		source: function(name, response) {
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajaxurl,
                	        data: { 'action': 'woosea_autocomplete_taxonomy', 'query': name, 'channel_hash': $('#channel_hash').val() },
				success: function(data) {
					response(data);
				}
			});
		}	
	});

	jQuery( "#mappingvalue" ).autocomplete({
		maxShowItems: 5,
		minLength: 2,
		position: { 	my: "right top", 
				at: "right bottom",
				of: "#mappingvalue"
				},
		source: function(name, response) {
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajaxurl,
				data: { 'action': 'woosea_autocomplete_mapping', 'query': name, 'searchin': $('#mapfrom').val(), 'condition': $('#mappingcondition').val() },
				success: function(data) {
					response(data);
				}
			});
		}	
	});

        // Add a mapping row to the table
        jQuery(".add-mapping").click(function(){
                var rowCount = $('#woosea-ajax-mapping-table >tbody >tr').length-1;

                jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_autocomplete_dropdown', 'rowCount': rowCount }
                })
                .done(function( data ) {
                        data = JSON.parse( data );

                        $( '#woosea-ajax-mapping-table' ).append('<tr><td><input type="hidden" name="mappings[' + rowCount + '][rowCount]" value="' + rowCount + '"><input type="checkbox" name="record" class="checkbox-field"></td><td><select name="mappings[' + rowCount + '][attribute]" id="mapfrom_' + rowCount + '">' + data.dropdown + '</select></td><td><select name="mappings[' + rowCount + '][condition]" id="mappingcondition"><option value="=">is equal to</option><option value="contains">contains</option></select></td><td><input type="text" name="mappings[' + rowCount + '][criteria]" class="mappingvalue_' + rowCount +' input-field-large"></td><td><input type="checkbox" name="mappings[' + rowCount + '][cs]" class="checkbox-field" alt="Case sensitive"></td><td><input type="text" class="autocomplete_' + rowCount +' input-field-large" name="mappings[' + rowCount + '][map_to_category]" class="input-field-large"></td></tr>');

                        jQuery( ".autocomplete_" + rowCount ).autocomplete({
                                maxShowItems: 5,
                                minLength: 2,
                                position: {     my: "right top",
                                                at: "right bottom",
                                                of: ".autocomplete_" + rowCount
                                        },
                                source: function(name, response) {
                                        jQuery.ajax({
                                                type: 'POST',
                                                dataType: 'json',
                                                url: ajaxurl,
                                                data: { 'action': 'woosea_autocomplete_taxonomy', 'query': name, 'channel_hash': $('#channel_hash').val() },
                                                success: function(data) {
                                                        response(data);
                                                }
                                        });
                                }
                        });

                        jQuery( ".mappingvalue_" + rowCount ).autocomplete({
                                maxShowItems: 5,
                                minLength: 2,
                                position: {     my: "right top",
                                                at: "right bottom",
                                                of: ".mappingvalue_" + rowCount
                                        },
                                source: function(name, response) {
                                        jQuery.ajax({
                                                type: 'POST',
                                                dataType: 'json',
                                                url: ajaxurl,
                                                data: { 'action': 'woosea_autocomplete_mapping', 'query': name, 'searchin': $('#mapfrom_' + rowCount).val(), 'condition': $('#mappingcondition').val() },
                                                success: function(data) {
                                                        response(data);
                                                }
                                        });
                                }
                        });
                })
                .fail(function( data ) {
                        console.log('Failed AJAX Call :( /// Return Data: ' + data);
                });

        });

        // Find and remove selected table rows
        jQuery(".delete-mapping").click(function(){
            $("table tbody").find('input[name="record"]').each(function(){

                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
});
