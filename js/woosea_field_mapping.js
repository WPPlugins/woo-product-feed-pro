jQuery(document).ready(function($) {

	// Add a mapping row to the table
	jQuery(".add-field-mapping").click(function(){
		var rowCount = $('#woosea-fieldmapping-table >tbody >tr').length-1;
		var channel_hash = $('#channel_hash').val();

                jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_fieldmapping_dropdown', 'rowCount': rowCount, 'channel_hash': channel_hash }
                })
                .done(function( data ) {
                        data = JSON.parse( data );
			
			$( '#woosea-fieldmapping-table' ).append('<tr><td><input type="hidden" name="attributes[' + rowCount + '][rowCount]" value="' + rowCount + '"><input type="checkbox" name="record" class="checkbox-field"></td><td><select name="attributes[' + rowCount + '][attribute]" class="select-field">' + data.field_options + '</select></td><td><input type="text" name="attributes[' + rowCount + '][prefix]" class="input-field-medium"></td><td><select name="attributes[' + rowCount + '][mapfrom]" class="select-field">' + data.attribute_options + '</select></td><td><input type="text" name="attributes[' + rowCount + '][suffix]" class="input-field-medium"></td></tr>');
                })
                .fail(function( data ) {
                        console.log('Failed AJAX Call :( /// Return Data: ' + data);
                });
        });
        
	// Find and remove selected table rows
        jQuery(".delete-field-mapping").click(function(){
            $("table tbody").find('input[name="record"]').each(function(){
		
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
});
