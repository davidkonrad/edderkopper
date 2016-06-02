
$(document).ready(function() {

	$("#fund-lnr").on('keydown', function(e) {	
		if (e.which == 13 ) {
			$("#edit-fund").trigger('click');
		}		
	})

	$("#edit-fund").click(function() {
		var LNR = $("#fund-lnr").val();
		console.log(LNR)
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=fundGet',
			data : {
				LNR: LNR
			},
			success: function(response) {
				console.log(response)
			}
		})
	})

	$("#create-fund").on('click', function() {
		adm.fundCreate();
	})


})
