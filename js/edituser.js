
var EditUser = {

	selectUser : function() {
		if ($('#user_id').find(':selected').val()=='') return;
		var url='ajax/edituser.php?action=select&user_id='+$('#user_id').find(':selected').val()+'&lang='+$("#lang").val();
		$('#edit-user').load(url);
	},

	index : function() {
		window.location='index.php';
	},

	createUser : function() {
		var url='ajax/edituser.php?action=create&lang='+$("#lang").val();
		$('#edit-user').load(url);
	},

	saveUser : function() {
		var url='ajax/edituser.php?action=update&user_id='+$('#user_id').find(':selected').val()+'&lang='+$("#lang").val();
		url=url+'&username='+$("#username").val()+'&password='+$("#password").val();
		$("input[type=checkbox]").each(function() {
			if ($(this).attr('checked')) {
				url=url+'&'+$(this).attr('id')+'=';
			}
		});
		$('#edit-user').load(url);
	}
};

