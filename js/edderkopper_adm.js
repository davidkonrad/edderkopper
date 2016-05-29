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
/*
var downloadURL = function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }
    iframe.src = url;
};
		alert(adm.getCSV(this));
*/
var $idown;  // Keep it outside of the function, so it's initialized once.
downloadURL = function(url) {
  if ($idown) {
    $idown.attr('src',url);
  } else {
    $idown = $('<iframe>', { id:'idown', src:url }).hide().appendTo('body');
  }
}
//... How to use it:
//downloadURL('http://whatever.com/file.pdf');
	downloadURL('edderkopper-upload/'+adm.getCSV(this));
	},

	speciesLookUp : function() {
		var speciesID = $("#species-lookup").val();
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=lookup&speciesID='+speciesID,
			success : function(name) {
				name = name!='' ? name : 'SpeciesID findes ikke';
				$("#current-species").text(name);
			}
		});
	},

	speciesEdit : function() {
		var species = $("#lookup-species").val(),
			speciesID = /\[(.*?)\]/.exec(species)[1];
		window.location.href='edderkopper-rediger-art?speciesID='+speciesID;
	},

	speciesCreate : function() {
		var speciesName = prompt('Indtast artsnavn, f.eks quadratus.\nArtsnavnet kan ændres senere.\nHusk at tildele den nye art en slægt!', '');
		if (speciesName== '') return;
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=createspecie&specie='+speciesName,
			success : function(speciesID) {
				//alert(speciesID);
				window.location.href='edderkopper-rediger-art?speciesID='+speciesID;
			}
		});
	},

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

/********************
	Art
********************/
	var path='ajax/edderkopper/actions.php?action=lookup';
	$("#lookup-species").typeahead({
		minLength : 1,
		items : 20,
		source: function(query, process) {
			return $.get(path+'&search='+query, {}, function(data) {
				var a=[];
				for (var i=0;i<data.length;i++) {
					a.push(data[i].value);
				}
				return process(a);
			});
		},
		updater: function(item) {
			$("#edit-specie").disable(false);
			return item;
    },
		afterSelect: function(item) {
			var allowedFields = ['den_danske_roedliste', 'NameDK', 'NameUK']

			var getCaption = function(field) {
				switch (field) {
					case 'Genus' : return 'Slægt'; break;
					case 'GenusID' : return 'Slægt'; break;
					case 'Species' : return 'Artsnavn'; break;
					case 'den_danske_roedliste': return 'Rødliste'; break;
					case 'NameDK' : return 'Dansk navn'; break;
					case 'NameUK' : return 'Navn UK'; break;
					default : return '??'; break;
				}
			}

			var id =  item.match(/[^[\]]+(?=])/g)
			id = id[0] ? id[0] : false
			if (!id) return
			$.ajax({
				url: 'ajax/edderkopper_Art.php',
				type: 'post',
				data : {
					action: 'get',
					id: id
				},
				success : function(response) {
					if (!response) return
					var r = JSON.parse(response),
							$body = $('#species-table-body');

					$body.html('')

					//slægt
					var $tr = $('<tr>'), $td = $('<td>');
					$td.append('<b>'+ getCaption('Genus') +'</b>')
					$td.appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" class="genus-typeahead" value="'+ r['Genus'] +'"/>')
					$td.append('<small id="hash-GenusID">#'+r['GenusID']+'</span>')
					$td.appendTo($tr)
					$tr.appendTo($body);

					//artsnavn
					var $tr = $('<tr>'), $td = $('<td>');
					$('<b>').text(getCaption('Species')).appendTo($td).appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" value="'+ r['Species'] +'"/>')
					$td.append('<small>#'+r['SpeciesID']+'</span>')
					$td.appendTo($tr)
					$tr.appendTo($body);

					for (var field in r) {
						if (~allowedFields.indexOf(field)) {
							var $tr = $('<tr>');
							$('<td>').text(getCaption(field)).appendTo($tr)
							var $td = $('<td>')
							$td.append('<input size="40" name="'+field+'" value="'+r[field]+'">').appendTo($tr)
							$tr.appendTo($body);
						}
					}

					//add save button
					var $tr = $('<tr>');
					var $td = $('<td>')
					$td.attr('colspan', 2).css('text-align', 'center')
					$td.append('<span id="art-message"></span>')
					$td.append('<input type="hidden" name="SpeciesID" id="SpeciesID" value="'+r['SpeciesID']+'"/>')
					$td.append('<input type="hidden" name="GenusID" id="GenusID"  value="'+r['GenusID']+'"/>')
					$('<button>')
						.text('Gem')
						.on('click', function() {
							var url = 'ajax/edderkopper/actions.php?action=updateSpecie';
							var params = $('#species-form').serialize()
							console.log(params)
							return false;
						})
						.appendTo($td)

					$td.appendTo($tr)
					$tr.appendTo($body);

					//init slægt typeahead
					var path='ajax/edderkopper/actions.php?action=lookupGenus';
					$('.genus-typeahead').typeahead({
						minLength : 1,
						items : 20,
						source: function(query, process) {
							return $.get(path+'&search='+query, {}, function(data) {
								var a=[];
								for (var i=0;i<data.length;i++) {
									a.push(data[i]);
								}
								return process(a);
							})
						},
						displayText: function(item) {
							return item.FullName
						},
						afterSelect: function(item) {
							this.$element.val(item.Genus)
							$('#hash-GenusID').text('#'+item.GenusID)
							$('#GenusID').val(item.GenusID)
						}
					})
				}
			})
		}
	});
	$("#lookup-species").on('click', function() {
		$(this).val('');
		$("#edit-specie").disable(true);
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


	$("#fund-lnr").on('keydown', function(e) {	
		if (e.which == 13 ) {
			$("#edit-fund").trigger('click');
		}		
	});
	$("#edit-fund").click(function() {
		var LNR = $("#fund-lnr").val();
		if (LNR!='') adm.fundEdit(LNR);
	});
	$("#create-fund").on('click', function() {
		adm.fundCreate();
	});
				
	$("#generate-checklist").on('click', function() {			
		adm.generateChecklist();
	});

	$("#update-name").on('click', function() {			
		adm.updateName();
	});

});

