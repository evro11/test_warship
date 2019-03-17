
$(document).ready(function() {
	
	$('.t_empty').click(function() {
		if ( $( this ).hasClass( "t_shot" ) ) {
			return;
		}
    
		const data = {};
		data.addr =  $(this).attr("id");
		const dataJson = JSON.stringify(data);
		sendAjax(dataJson, $(this).attr("id"));
	});
	
	
	function sendAjax(dataJson, id) {
		$.ajax({
			type: "POST",
			url: "index.php",
			dataType: "json",
			data: { data: dataJson },
			success: function(msg) {
				let color = 'blue';
				if ( 'miss' === msg.status ) {
					$('#'+id).addClass( "t_shot t_miss").removeClass( "t_empty");
				}
				else if ( 'ship_hit' === msg.status ) {
					const shipName = "ship_" + msg.shipName;
					$('#'+id).addClass( "t_shot "+shipName+" t_ship_hit").removeClass( "t_empty");
					color = 'yellow';
				}
				else if ( 'ship_sunk' === msg.status ) {
					const shipName = "ship_" + msg.shipName;
					$('#'+id).addClass( "t_shot "+shipName+" t_ship_hit").removeClass( "t_empty");
					$('.'+shipName).addClass(" t_ship_sunk").removeClass( "t_ship_hit");
					color = 'red';
					if (msg.gameOver) {
						alert(msg.message);
					}
				}
				$('#log').append('<div style="color: '+ color +'">' + msg.status + '</div>');
				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("Status: " + textStatus); alert("Error: " + errorThrown);
			}
		});
	}
	
});