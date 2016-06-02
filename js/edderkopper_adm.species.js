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

		var allowedFields = ['den_danske_roedliste', 'NameDK', 'NameUK', 'SAuthor', 'SCharDK', 'SCharUK']

			var getCaption = function(field) {
				switch (field) {
					case 'Genus' : return 'Slægt'; break;
					case 'GenusID' : return 'Slægt'; break;
					case 'Species' : return 'Artsnavn'; break;
					case 'den_danske_roedliste': return 'Rødliste'; break;
					case 'NameDK' : return 'Dansk DK'; break;
					case 'NameUK' : return 'Navn UK'; break;
					case 'SAuthor' : return 'Author'; break;
					case 'SCharDK': return 'Beskrivelse DK'; break;
					case 'SCharUK': return 'Beskrivelse UK'; break;
					default : return '??'; break;
				}
			}

			function getHTMLElement(field, value) {
				switch (field) {
					case 'SCharDK' :
					case 'SCharUK' :
						return '<textarea class="editor" name="'+field+'">'+value+'</textarea>'
						break;

					default :
						return '<input size="40" name="'+field+'" value="'+value+'">'
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
					$td.append('<input size="40" name="Species" value="'+ r['Species'] +'"/>')
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

					//add save button
					var $tr = $('<tr>');
					var $td = $('<td>')
					$td.attr('colspan', 2).css('text-align', 'center')
					$td.append('<span id="art-message"></span>')
					$td.append('<input type="hidden" name="SpeciesID" id="SpeciesID" value="'+r['SpeciesID']+'"/>')
					$td.append('<input type="hidden" name="GenusID" id="GenusID"  value="'+r['GenusID']+'"/>')
					$('<button>')
						.text('Gem')
						.css('font-size', '150%')
						.on('click', function() {
							for(var i in CKEDITOR.instances) {
								CKEDITOR.instances[i].updateElement();
							}
							var url = 'ajax/edderkopper/actions.php?action=updateSpecies';
							var params = $('#species-form').serialize()
							//console.log(params)
							$.ajax({
								url: url,
								data: params,
								success: function(response) {
									$('#species-messages').text(response).show().fadeOut(10000)
								}
							})
							return false;
						})
						.appendTo($td)

					$td.appendTo($tr)
					$tr.appendTo($body);

					//init editors
					for (var i in CKEDITOR.instances) {
						CKEDITOR.instances[i].destroy(true);
					}

					$('.editor').each(function() {
						var name=$(this).attr('name');
						CKEDITOR.replace(name, { width:"750px", height:"90px", toolbar:'edderkopper' }); //, toolbar:'Basic'
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
			setSpecie(item)
		}
	})

})
