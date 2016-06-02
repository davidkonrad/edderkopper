var adm = {
	readDir : function() {
		$.ajax({
			url : 'ajax/edderkopper/readdir.php',
			success : function(html) {
				$("#csv-filelist").html(html);
				$("#csv-filelist .csv-check").on('click', adm.checkCSV);
				$("#csv-filelist .csv-replace").on('click', adm.replaceCSV);
				$("#csv-filelist .csv-add").on('click', adm.insertCSV);
				$("#csv-filelist .csv-delete").on('click', adm.deleteCSV);
				$("#csv-filelist .csv-download").on('click', adm.downloadCSV);

				$("#csv-filelist .csv-replace").attr('title', 'Nulstiller (sletter) fund-tabellen og indsætter CSV-filens fund');
				$("#csv-filelist .csv-add").attr('title', 'Tilføjer CSV-filens fund til allerede eksisterende fund');
				$("#csv-filelist .csv-download").attr('title', 'Download denne CSV');
				$("#csv-filelist .csv-delete").attr('title', 'Slet CSV fil på serveren (sletter IKKE fund)');
			} 
		});
	},

	setUploadBtn : function() {
		if ($("#upload-file").val()!='') {
			$("#upload-begin").removeAttr('disabled');
		} else {
			$("#upload-begin").attr('disabled','disabled');
		}
	},

	downloadDatabase : function() {
		window.location.href = 'ajax/edderkopper/download.php';
	},

	getCSV : function(element) {
		var tr = $(element).parent().parent(),
			csv = tr.find('.csv-name').text();
		return csv;
	},

	replaceCSV : function() {
		var csv = adm.getCSV(this);
		if (confirm('Er du sikker på du vil nustille fund-tabllen og indsætte '+csv+'?')) {
			adm.msgWait();			
			$.ajax({
				url : 'ajax/edderkopper/actions.php?action=truncate',
				success : function(msg) {
					adm.msg(msg);
					adm.msgWait();
					$.ajax({
						url : 'ajax/edderkopper/actions.php?action=insert&csv='+csv,
						success : function(msg) {
							adm.msg(msg);
							adm.updateName();
						}
					});
				}
			});
		}
	},

	insertCSV : function() {
		var csv = adm.getCSV(this);
		if (confirm('Er du sikker på du vil tilføje '+csv+'?')) {
			adm.msgWait();			
			$.ajax({
				url : 'ajax/edderkopper/actions.php?action=insert&csv='+csv,
				success : function(msg) {
					adm.msg(msg);
					adm.updateName();
				}
			})
		}
	},

	deleteCSV : function() {
		var csv = adm.getCSV(this);
		if (confirm('Er du sikker på du vil slette '+csv+'?')) {
			$.ajax({
				url : 'ajax/edderkopper/actions.php?action=delete&csv='+csv,
				success : function(msg) {
					adm.msg(msg);
					adm.readDir();
				}
			})
		}
	},

	checkCSV : function() {
		var csv = adm.getCSV(this);
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=check&csv='+csv,
			success : function(msg) {
				if (msg == '') {
					adm.msg('<b>'+csv+' ser ud til at være en gyldig edderkoppe-CSV'+'</b>');
				} else {
					adm.msg('<b>Fejl i '+csv+'</b>');
					adm.msg(msg);
				}
			}
		});
	},

	updateName : function() {
		adm.msgWait();
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=fundupdatenameall',
			success : function() {
				adm.msg('Navne på fund opdateret ...');
			}
		});
	},
		
	downloadCSV : function(element) {
		var $idown;  // Keep it outside of the function, so it's initialized once.
		downloadURL = function(url) {
		  if ($idown) {
		    $idown.attr('src',url);
		  } else {
		    $idown = $('<iframe>', { id:'idown', src:url }).hide().appendTo('body');
		  }
		}
		//downloadURL('http://whatever.com/file.pdf');
		downloadURL('edderkopper-upload/'+adm.getCSV(this));
	},

/*
	fundCreate : function() {
		window.location='edderkopper-rediger-fund';
	},

	fundEdit : function(lnr) {
		window.location='edderkopper-rediger-fund?lnr='+lnr;
	},

	fundDelete : function(lnr) {
		if (confirm('Slet fund - er du sikker?')) {
			var params = 'lnr='+lnr+'&action=funddelete';
			$.ajax({
				url : 'ajax/edderkopper/actions.php?'+params,
				success : function(msg) {
					window.location='edderkopper-administration';
				}
			})
		}
	},

	fundSave : function(formElement, msgElement) {
		var params=$(formElement).serialize();
		params+='&action=fundsave';
		$.ajax({
			url : 'ajax/edderkopper/actions.php?'+params,
			success : function(msg) {
				$(msgElement).show();
				$(msgElement).html(msg);
				setTimeout(function() {
					//$(msgElement).fadeOut( "slow" );
				}, 3000);
			}
		});
	},
*/
		
	generateChecklist : function() {
		adm.msgWait();
		$.ajax({
			url : 'ajax/edderkopper_checklist.php?action=create',
			success : function() {
				adm.msg('<b>Tjekliste er opdateret!</b>');
			}
		});
	},

	msg : function(msg) {
		//remove any images
		$("#messages").find('img').remove();

		//remove any last-msg
		$("#messages .msg-last").removeClass('msg-last');

		//insert linebreak if #messages already have content
		var pre = $("#messages").text().trim() != '' ? '<br>' : '';

		//insert message
		msg='<span class="msg-last">'+msg+'</span>';
		$("#messages").append(pre+msg);

		//scroll to bottom
		$('#messages').scrollTop( $('#messages').height());
	},

	msgWait : function() {
		adm.msg('<img src="img/ajax-loader.gif">');
	}
}

$(document).ready(function() {
	$("#download").on('click', function() {
		adm.downloadDatabase();
	});
	$("#species-lookup").on('keyup', function() {
		adm.speciesLookUp();
	});

	adm.readDir();

	$("#edit-specie").on('click', function() {
		adm.speciesEdit();
	}).disable(true);

	$("#create-specie").on('click', function() {
		adm.speciesCreate();
	});


	$("input.number-only").bind({
		keydown: function(e) {
	        if (e.shiftKey === true ) {
	            if (e.which == 9) {
	                return true;
	            }
	            return false;
	        }
	        if (e.which > 57) {
	            return false;
	        }
	        if (e.which==32) {
	            return false;
	        }
	        return true;
	   }
	});

	$("#generate-checklist").on('click', function() {			
		adm.generateChecklist();
	});

	$("#update-name").on('click', function() {			
		adm.updateName();
	});

});


