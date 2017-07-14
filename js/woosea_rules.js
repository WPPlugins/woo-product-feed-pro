jQuery(document).ready(function($) {

        jQuery(".add-row").click(function(){
		// Count amount of rows, used to create the form array field and values
		var rowCount = $('#woosea-ajax-table >tbody >tr').length-1;

		jQuery.ajax({
     		   	method: "POST",
        		url: ajaxurl,
        		data: { 'action': 'woosea_ajax', 'rowCount': rowCount }
      		})
     	 	.done(function( data ) {
			data = JSON.parse( data );
        		$( '#woosea-ajax-table' ).append('<tr><td><input type="hidden" name="rules[' + data.rowCount + '][rowCount]" value="' + data.rowCount + '"><input type="checkbox" name="record" class="checkbox-field"></td><td><select name="rules[' + data.rowCount + '][attribute]">' + data.dropdown + '</select></td><td><select name="rules[' + data.rowCount + '][condition]" class="select-field"><option value="contains">contains</option><option value="containsnot">does not contain</option><option value="=">is equal to</option><option value="!=">is not equal to</option><option value=">">is greater than</option><option value=">=">is greater or equal to</option><option value="<">is less than</option><option value="=<">is less or equal to</option></select></td><td><input type="text" name="rules[' + rowCount + '][criteria]" class="input-field-large"></td><td><input type="checkbox" name="rules[' + rowCount + '][cs]" class="checkbox-field" alt="Case sensitive"></td><td><select name="rules[' + rowCount + '][than]" class="select-field"><optgroup label="Action">Action:<option value="exclude"> Exclude</option><option value="include_only">Include only</option></optgroup></select></td></tr>');
      		})
      		.fail(function( data ) {
       		 	console.log('Failed AJAX Call :( /// Return Data: ' + data);
     	 	});	
	});

        // Find and remove selected table rows
        jQuery(".delete-row").click(function(){
            $("table tbody").find('input[name="record"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
});
