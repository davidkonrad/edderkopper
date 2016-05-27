
var EditTrans = {

	index : function() {
		window.location='index.php';
	},

	createTextCode : function() {
		var code=prompt("Opret tekstkode","");
		if ((code!=null) && (code!="")) {
			var params='action=add&code='+code;
			var url='ajax/edittextcode.php?'+params;
			$.ajax({
				url: url,
				cache: false,
				async: true,
				timeout : 5000,
				success: function(html) {
					window.location.reload();
				}
			});
		}
	},

	deleteTextCode : function(code, text_id) {
		if (confirm('Slet tekstkode '+code+' ?')) {
			var params='action=delete&text_id='+text_id;
			var url='ajax/edittextcode.php?'+params;
			$.ajax({
				url: url,
				cache: false,
				async: true,
				timeout : 5000,
				success: function(html) {
					window.location.reload();
				}
			});
		}
	},

	updateTextCode : function(text_id) {
		var code=$("#code_"+text_id).val();
		var params='action=update&text_id='+text_id+'&code='+code;
		var url='ajax/edittextcode.php?'+params;
		$.ajax({
			url: url,
			cache: false,
			async: true,
			timeout : 5000,
			success: function(html) {
				window.location.reload();
			}
		});
	},

	gotoTranslations : function() {
		window.location='admin-translations';
	},

	gotoTextCodes : function() {
		window.location='admin-text-codes';
	},

	getTranslation : function() {
		var text_id=$("#text_id").val();
		var lang_id=$("#lang_id").val();
		if ((lang_id!='') && (text_id!='')) {
			var params='action=get&text_id='+text_id+'&lang_id='+lang_id;
			var url='ajax/edittranslations.php?'+params;
			$("#edit-translations").load(url);
		}
	},

	saveTranslation : function () {
		var text_id=$("#text_id").val();
		var lang_id=$("#lang_id").val();
		var translation=escape($("#translation").val());
		var params='action=update&text_id='+text_id+'&lang_id='+lang_id+'&translation='+translation;
		var url='ajax/edittranslations.php?'+params;
		$.ajax({
			url: url,
			cache: false,
			async: true,
			timeout : 5000,
			success: function(html) {
				$("#edit-translation-msg").html('<small>Tekstovers&aelig;ttelse gemt</small>');
			}
		});
	},

	createTranslationFile : function() {
		var lang_id=$("#lang_id").val();	
		var params='action=file&lang_id='+lang_id;
		var url='ajax/edittranslations.php?'+params;
		var file = window.open(url, '_blank');
	}
		

};
