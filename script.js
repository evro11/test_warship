
$(document).ready(function() {
	
	$('.t_empty').click(function() {
		//alert( "Handler for .click() called." + $(this).attr("id") );
		
		if ( $( this ).hasClass( "t_shot" ) ) {
			return;
		}
    
		var data = {};
		data.addr =  $(this).attr("id");
		//data.name = $(this).attr("name");
			
		var dataJson = JSON.stringify(data);
			
		//console.log(data);
		sendAjax(dataJson, $(this).attr("id"));
	});
	
	
	function sendAjax(dataJson, id) {
		$.ajax({
			type: "POST",
			url: "index.php",
			dataType: "json",
			data: { data: dataJson },
			success: function(msg) {

				console.log(msg);
				if ( 'miss' == msg.status ) {
					$('#'+id).addClass( "t_shot t_miss").removeClass( "t_empty");
					
				}
				else if ( 'ship_hit' == msg.status ) {
					var shipName = "ship_" + msg.shipName
					$('#'+id).addClass( "t_shot "+shipName+" t_ship_hit").removeClass( "t_empty");
				}
				else if ( 'ship_sunk' == msg.status ) {
					var shipName = "ship_" + msg.shipName
					$('#'+id).addClass( "t_shot "+shipName+" t_ship_hit").removeClass( "t_empty");
					$('.'+shipName).addClass(" t_ship_sunk").removeClass( "t_ship_hit");
					if (msg.gameOver) {
						alert(msg.message);
					}
				}
				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("Status: " + textStatus); alert("Error: " + errorThrown);
			}
		});
	}
	
});