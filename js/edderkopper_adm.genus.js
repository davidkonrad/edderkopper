$(document).ready(function() {

	var currentGenusItem = null;

	$('#create-genus').on('click', function() {
		var genusName = prompt('Indtast slægtsnavn, f.eks araneus.\nSlægtsnavnet kan ændres senere.\nHusk at tildele den nye slægt en familie!', '');
		if (!genusName || genusName.trim() == '') return;
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=createGenus&genus='+genusName,
			success : function(genusID) {
				setGenus(parseInt(genusID))
			}
		});
	})

	$("#lookup-genus").on('click', function() {
		$(this).val('')
	})

	$("#lookup-genus").on('focusout', function() {
		if (currentGenusItem) $(this).val(currentGenusItem)
	})

  function setGenus(item) {
		if (typeof item == 'number') {
			currentGenusItem = 'Ny art #'+item
			$("#lookup-genus").val(currentGenusItem)
			var id = item
		} else {
			currentGenusItem = item;	
			var id =  item.match(/[^[\]]+(?=])/g)
			id = id[0] ? id[0] : false
		}

		if (!id) return

		var allowedFields = ['den_danske_roedliste', 'NameDK', 'NameUK', 'SAuthor', 'SCharDK', 'SCharUK', 
			'SDistriEuDK', 'SDistriEuUK', 'SDistriDkUK', 'SDistriDkDK'];


			var getCaption = function(field) {
				/*
				switch (field) {
					case 'Family' : return 'Familie'; break;
					case 'FamilyID' : return 'Familie'; break;
					case 'Genus' : return 'Slægstnavn'; break;
					case 'den_danske_roedliste': return 'Rødliste'; break;
					case 'GNameDK' : return 'Dansk DK'; break;
					case 'GNameUK' : return 'Navn UK'; break;
					case 'GAuthor' : return 'Author'; break;
					case 'GCharactersDK': return 'Beskrivelse DK'; break;
					case 'GCharactersUK': return 'Beskrivelse UK'; break;
					default : return '??'; break;
				}
				*/
				return field
			}

			function getHTMLElement(field, value) {
				switch (field) {
					case 'GCharactersDK' :
					case 'GCharactersUK' :
					case 'SDistriEuDK':
					case 'SDistriEuUK': 
					case 'SDistriDkUK': 
					case 'SDistriDkDK':
						return '<textarea class="editor" name="'+field+'">'+value+'</textarea>'
						break;

					default :
						return '<input size="40" name="'+field+'" value="'+value+'">'
						break;
				}
			}
							 
			$.ajax({
				url: 'ajax/edderkopper/actions.php?action=getGenus',
				data : {
					GenusID: id
				},
				success : function(response) {
					console.log(response);
					if (!response) return
					var r = JSON.parse(response),
							$body = $('#genus-table-body');

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
					$('<b>').text(getCaption('Genus')).appendTo($td).appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" name="Genus" value="'+ r['Genus'] +'"/>')
					$td.append('<small>#'+r['GenusID']+'</span>')
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
					$td.append('<input type="hidden" name="GenusID" id="GenusID" value="'+r['GenusID']+'"/>')
					$td.append('<input type="hidden" name="GenusID" id="GenusID"  value="'+r['GenusID']+'"/>')
					$('<button>')
						.text('Gem')
						.css('font-size', '150%')
						.on('click', function() {
							for(var i in CKEDITOR.instances) {
								CKEDITOR.instances[i].updateElement();
							}
							var url = 'ajax/edderkopper/actions.php?action=updateGenus';
							var params = $('#genus-form').serialize()
							//console.log(params)
							$.ajax({
								url: url,
								data: params,
								success: function(response) {
									$('#genus-messages').text(response).show().fadeOut(10000)
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

	var path='ajax/edderkopper/actions.php?action=lookupGenus';
	$("#lookup-genus").typeahead({
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
			setGenus(item.FullName)
		}
	})

})
