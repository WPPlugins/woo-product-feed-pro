jQuery(document).ready(function($) {
	var project_hash = null;
	var project_status = null;

    	$("td[colspan=8]").find("div").parents("tr").hide();

	$('.checkbox-field').change(function(index, obj){
    		project_hash = $(this).val();
		project_status = $(this).prop("checked")

                jQuery.ajax({
                	method: "POST",
                        url: ajaxurl,
                        data: { 'action': 'woosea_project_status', 'project_hash': project_hash, 'active': project_status }
                })

         	$("table tbody").find('input[name="manage_record"]').each(function(){
			var hash = this.value;
			if(hash == project_hash){
				if (project_status == false){
					$(this).parents("tr").addClass('strikethrough');
				} else {
					$(this).parents("tr").removeClass('strikethrough');
				}
                	}
            	});
	});

	$("#dialog").dialog({
      		autoOpen: false,
      		modal: true
    	});

	$("#refresh-dialog").dialog({
      		autoOpen: false,
      		modal: true
    	});

	$(".actions").delegate("span", "click", function() {
   		var id=$(this).attr('id');
		var idsplit = id.split('_');
		var project_hash = idsplit[1];
		var action = idsplit[0];		

		if (action == "gear"){
    			$("tr").not(':first').click(
				function(event) {
        				var $target = $(event.target);
        				$target.closest("tr").next().find("div").parents("tr").slideDown( "slow" );                
    				}
			);
		}

		if (action == "trash"){

			$("#dialog").dialog({
				resizable: false,
        			modal: true,
        			width: 300,
        			height: "auto",
        			autoOpen: false
    			});

		        $("#dialog").dialog('option', 'buttons', {
            			"Confirm" : function() {
            				jQuery.ajax({
                				method: "POST",
                       	 			url: ajaxurl,
                        			data: { 'action': 'woosea_project_delete', 'project_hash': project_hash }
                			})
                			$(this).dialog("close");

            				$("table tbody").find('input[name="manage_record"]').each(function(){
						var hash = this.value;
						if(hash == project_hash){
                    					$(this).parents("tr").remove();
                				}
            				});

				},
            			"Cancel" : function() {
                			$(this).dialog("close");
            			}
        		});

    			$("#dialog").dialog("open");
		}

		if (action == "refresh"){
			
			$("#refresh-dialog").dialog({
				resizable: false,
        			modal: true,
        			width: 300,
        			height: "auto",
        			autoOpen: false
    			});

		        $("#refresh-dialog").dialog('option', 'buttons', {
            			"Confirm" : function() {
        				jQuery.ajax({
                				method: "POST",
                       	 			url: ajaxurl,
                        			data: { 'action': 'woosea_project_refresh', 'project_hash': project_hash }
                			})
					$(this).dialog("close");

					// Replace status of project to processing
			         	$("table tbody").find('input[name="manage_record"]').each(function(){
						var hash = this.value;
						if(hash == project_hash){
							$(".blink_"+hash).text(function () {
                                        			$(this).addClass('blink_me');
    								return $(this).text().replace("ready", "processing"); 
							});	
						}
            				});
				},
            			"Cancel" : function() {
                			$(this).dialog("close");
            			}
        		});

    			$("#refresh-dialog").dialog("open");
		}
	});
});
