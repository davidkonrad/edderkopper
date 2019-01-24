$(document).ready(function() {

	var currentSpeciesItem = null;

	$('#create-species').on('click', function() {
		var speciesName = prompt('Indtast artsnavn, f.eks quadratus.\nArtsnavnet kan ændres senere.\nHusk at tildele den nye art en slægt!', '');
		if (!speciesName || speciesName.trim() == '') return;
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=createspecie&specie='+speciesName,
			success : function(speciesID) {
				setSpecie(parseInt(speciesID))
			}
		});
	})

	$("#lookup-species").on('click', function() {
		$(this).val('')
	})

	$("#lookup-species").on('focusout', function() {
		if (currentSpeciesItem) $(this).val(currentSpeciesItem)
	})

  function setSpecie(item) {

		if (typeof item == 'number') {
			currentSpeciesItem = 'Ny art #'+item
			$("#lookup-species").val(currentSpeciesItem)
			var id = item
		} else {
			currentSpeciesItem = item;	
			var id =  item.match(/[^[\]]+(?=])/g)
			id = id[0] ? id[0] : false
		}

		if (!id) return
		$('#species-save').disable(false)

		var allowedFields = ['den_danske_roedliste', 'NameDK', 'NameUK', 'SAuthor', 'SCharDK', 'SCharUK',
			'SDistriEuDK', 'SDistriEuUK', 'SDistriDkUK', 'SDistriDkDK']

			var getCaption = function(field) {
				switch (field) {
					//case 'Genus' : return 'Slægt'; break;
					//case 'GenusID' : return 'Slægt'; break;
					//case 'Species' : return 'Artsnavn'; break;
					case 'den_danske_roedliste': 
						return 'Rødliste'; 
						break;
					//case 'NameDK' : return 'Dansk DK'; break;
					//case 'NameUK' : return 'Navn UK'; break;
					//case 'SAuthor' : return 'Author'; break;
					//case 'SCharDK': return 'Beskrivelse DK'; break;
					//case 'SCharUK': return 'Beskrivelse UK'; break;
					default : 
						return field; 
						break;
				}
			}

			function getHTMLElement(field, value) {
				switch (field) {
					case 'SCharDK' :
					case 'SCharUK' :
					case 'SDistriEuDK' :
					case 'SDistriEuUK' :
					case 'SDistriDkUK' :
					case 'SDistriDkDK' :
						return '<textarea class="editor" id="'+field+'" name="'+field+'" spellcheck="false">'+value+'</textarea>'
						break;

					default :
						return '<input size="40" name="'+field+'" value="'+value+'" spellcheck="false">'
						break;
				}
			}
							 
			$.ajax({
				url: 'ajax/edderkopper/actions.php?action=getSpecies',
				data : {
					SpeciesID: id
				},
				success : function(response) {
					if (!response) return
					var r = JSON.parse(response),
							$body = $('#species-table-body');

					$body.html('')

					$('<input type="hidden" name="GenusID" id="GenusID" value="' + r.GenusID +'">').appendTo($body)
					$('<input type="hidden" name="SpeciesID" value="' + r.SpeciesID +'">').appendTo($body)

					//slægt
					var $tr = $('<tr>'), $td = $('<td>');
					$td.append('<b>'+ getCaption('Genus') +'</b>')
					$td.appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" class="genus-typeahead" value="'+ r['Genus'] +'" spellcheck="false"/>')
					$td.append('<small id="hash-GenusID">#'+r['GenusID']+'</span>')
					$td.appendTo($tr)
					$tr.appendTo($body);

					//artsnavn
					var $tr = $('<tr>'), $td = $('<td>');
					$('<b>').text(getCaption('Species')).appendTo($td).appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" name="Species" value="'+ r['Species'] +'" spellcheck="false"/>')
					$td.append('<small>#'+r['SpeciesID']+'</span>')
					$td.appendTo($tr)
					$tr.appendTo($body);

					for (var field in r) {
						if (~allowedFields.indexOf(field)) {
							var $tr = $('<tr>');
							$('<td>').css('vertical-align','top').text(getCaption(field)).appendTo($tr)
							var $td = $('<td>')
							$td.append(getHTMLElement(field, r[field] ? r[field] : '' )).appendTo($tr)
							$tr.appendTo($body);
						}
					}

					for (var i in CKEDITOR.instances) {
						CKEDITOR.instances[i].destroy(true);
					}
					$('.editor').each(function() {
						var name=$(this).attr('name');
						CKEDITOR.replace(name, { width:"750px", height:"150px", toolbar:'edderkopper_small' }); 
					})

					//init slægt typeahead
					var path='ajax/edderkopper/actions.php?action=lookupGenus';
					$('.genus-typeahead').typeahead({
						showHintOnFocus: true,
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

	var path='ajax/edderkopper/actions.php?action=lookupSpecies';
	$("#lookup-species").typeahead({
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
			setSpecie(item.FullName)
		}
	})

	$('#species-save').on('click', function() {
		for(var i in CKEDITOR.instances) {
			CKEDITOR.instances[i].updateElement();
		}
		var url = 'ajax/edderkopper/actions.php?action=updateSpecies';
		var params = $('#species-form').serialize()
		$.ajax({
			url: url,
			data: params,
			success: function(response) {
				$('#species-messages').text(response).show().fadeOut(5000)
			}
		})
	})	
	

})
