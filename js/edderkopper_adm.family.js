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

		var allowedFields = ['den_danske_roedliste', 'NameDK', 'NameUK', 'SAuthor', 'SCharDK', 'SCharUK']

			var getCaption = function(field) {
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
				url: 'ajax/edderkopper/actions.php?action=getFamily',
				data : {
					FamilyID: id
				},
				success : function(response) {
					if (!response) return
					var r = JSON.parse(response),
							$body = $('#family-table-body');

					$body.html('')

					//slægt
					var $tr = $('<tr>'), $td = $('<td>');
					$td.append('<b>'+ getCaption('Family') +'</b>')
					$td.appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" class="family-typeahead" value="'+ r['Family'] +'"/>')
					$td.append('<small id="hash-FamilyID">#'+r['FamilyID']+'</span>')
					$td.appendTo($tr)
					$tr.appendTo($body);

					//artsnavn
					var $tr = $('<tr>'), $td = $('<td>');
					$('<b>').text(getCaption('Family')).appendTo($td).appendTo($tr)
					var $td = $('<td>')
					$td.append('<input size="40" name="Family" value="'+ r['Family'] +'"/>')
					$td.append('<small>#'+r['FamilyID']+'</span>')
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
					$td.append('<input type="hidden" name="FamilyID" id="FamilyID" value="'+r['FamilyID']+'"/>')
					$td.append('<input type="hidden" name="FamilyID" id="FamilyID"  value="'+r['FamilyID']+'"/>')
					$('<button>')
						.text('Gem')
						.css('font-size', '150%')
						.on('click', function() {
							for(var i in CKEDITOR.instances) {
								CKEDITOR.instances[i].updateElement();
							}
							var url = 'ajax/edderkopper/actions.php?action=updateFamily';
							var params = $('#family-form').serialize()
							//console.log(params)
							$.ajax({
								url: url,
								data: params,
								success: function(response) {
									$('#family-messages').text(response).show().fadeOut(10000)
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
					var path='ajax/edderkopper/actions.php?action=lookupFamily';
					$('.family-typeahead').typeahead({
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
							this.$element.val(item.Family)
							$('#hash-FamilyID').text('#'+item.FamilyID)
							$('#FamilyID').val(item.FamilyID)
						}
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

})
