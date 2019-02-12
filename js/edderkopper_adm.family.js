$(document).ready(function() {

	var currentFamilyItem = null;

	$('#create-family').on('click', function() {
		var familyName = prompt('Indtast navn på familie, f.eks Araneidae', '');
		if (!familyName || familyName.trim() == '') return;
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=createFamily&family='+familyName,
			success : function(familyID) {
				setFamily(parseInt(familyID))
			}
		});
	})

	$("#lookup-family").on('click', function() {
		$(this).val('')
	})

	$("#lookup-family").on('focusout', function() {
		if (currentFamilyItem) $(this).val(currentFamilyItem)
	})

  function setFamily(item) {
		if (typeof item == 'number') {
			currentFamilyItem = 'Ny art #'+item
			$("#lookup-family").val(currentFamilyItem)
			var id = item
		} else {
			currentFamilyItem = item;	
			var id =  item.match(/[^[\]]+(?=])/g)
			id = id[0] ? id[0] : false
		}

		if (!id) return

		var allowedFields = ['Family', 'Author', 'FamilyDK', 'FamilyUK', 
			'FBiologyEuDK', 'FBiologyEuUK', 'FBiologyDkDK', 'FBiologyDkUK', 'FCharactersDK', 'FCharactersUK', 'FTaxNoteDK', 'FTaxNoteUK']

			var getCaption = function(field) {
				/*
				switch (field) {
					case 'Family' : return 'Slægt'; break;
					case 'FamilyID' : return 'Slægt'; break;
					case 'Family' : return 'Artsnavn'; break;
					case 'den_danske_roedliste': return 'Rødliste'; break;
					case 'NameDK' : return 'Dansk DK'; break;
					case 'NameUK' : return 'Navn UK'; break;
					case 'SAuthor' : return 'Author'; break;
					case 'SCharDK': return 'Beskrivelse DK'; break;
					case 'SCharUK': return 'Beskrivelse UK'; break;
					default : return '??'; break;
				}
				*/ return field
			}

			function getHTMLElement(field, value) {
				switch (field) {
					case 'FBiologyEuDK' :
					case 'FBiologyEuUK' : 
					case 'FBiologyDkDK' :
					case 'FBiologyDkUK' :
					case 'FCharactersDK' :
					case 'FCharactersUK' :
					case 'FTaxNoteDK' : 
					case 'FTaxNoteUK' :
						return '<textarea class="editor" name="'+field+'" spellcheck="false">'+value+'</textarea>'
						break;
					default :
						return '<input size="40" name="'+field+'" value="'+value+'" spellcheck="false">'
						break;
				}
			}

			$.ajax({
				url: 'ajax/edderkopper/actions.php?action=getFamily',
				data : {
					FamilyID: id
				},
				success : function(response) {
					if (!response) return
					var r = JSON.parse(response);
					var $body = $('#family-table-body');

					$body.html('')

					$('#family-save').disable(false)

					$('<input type="hidden" name="FamilyID" id="FamilyID" value="' + r.FamilyID +'">').appendTo($body)							 

					for (var field in r) {
						if (~allowedFields.indexOf(field)) {
							var $tr = $('<tr>');
							$('<td>').css('vertical-align','top').text(getCaption(field)).appendTo($tr)
							var $td = $('<td>')
							$td.append(getHTMLElement(field, r[field] ? r[field] : '' )).appendTo($tr)
							$tr.appendTo($body);
						}
					}

					//add save button
					var $tr = $('<tr>');
					var $td = $('<td>')
					$td.attr('colspan', 2).css('text-align', 'center')
					$td.append('<span id="art-message"></span>')

					//init editors
					for (var i in CKEDITOR.instances) {
						CKEDITOR.instances[i].destroy(true);
					}

					$('.editor').each(function() {
						var name=$(this).attr('name');
						CKEDITOR.replace(name, { width:"750px", height:"90px", toolbar:'edderkopper_small' }); 
					})

				}
			})
		}

	var path='ajax/edderkopper/actions.php?action=lookupFamily';
	$("#lookup-family").typeahead({
		minLength : 1,
		items : 20,
		source: function(query, process) {
			return $.get(path+'&search='+query, {}, function(data) {
				return process(data);
			});
		},
		displayText: function(item) {
			return item.FullName
		},
		afterSelect: function(item) {
			setFamily(item.FullName)
		}
	})

	$('#family-save').on('click', function() {
		for(var i in CKEDITOR.instances) {
			CKEDITOR.instances[i].updateElement();
		}
		var url = 'ajax/edderkopper/actions.php?action=updateFamily';
		var params = $('#family-form').serialize()
		$.ajax({
			url: url,
			data: params,
			success: function(response) {
				$('#family-messages').text(response).show().fadeOut(5000)
			}
		})
	})	

})
