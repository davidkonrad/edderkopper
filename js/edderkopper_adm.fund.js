
//regioner
var regions = ['WJ', 'NWJ', 'EJ', 'SJ', 'NEJ', 'F', 'LFM', 'NWZ', 'SZ', 'NEZ', 'B']

//build a UTM10 lookup list
var utm = []
$(document).ready(function() {
	//ref. js/utm.js
	for (var u in UTM_LatLng) {
		utm.push(u)
	}
})
//build collection lookup array
var collections = []
$(document).ready(function() {
	$.ajax({
		url: 'ajax/edderkopper/actions.php?action=getCollections',
		success: function(response) {
			response.forEach(function(collection) {
				if (collection.Collection && collection.Collection.trim().length > 0) collections.push(collection.Collection)
			})
		}
	})
})
//build leg, det lookup arrays
var dets = [], legs = []
$(document).ready(function() {
	$.ajax({
		url: 'ajax/edderkopper/actions.php?action=getDets',
		success: function(response) {
			response.forEach(function(item) {
				if (item.Det && item.Det.trim().length > 0) dets.push(item.Det)
			})
		}
	})
	$.ajax({
		url: 'ajax/edderkopper/actions.php?action=getLegs',
		success: function(response) {
			response.forEach(function(item) {
				if (item.Leg && item.Leg.trim().length > 0) legs.push(item.Leg)
			})
		}
	})
})
//build localities lookup array
var localities = []
$(document).ready(function() {
	$.ajax({
		url: 'ajax/edderkopper/actions.php?action=getLocalities',
		success: function(response) {
			response.forEach(function(item) {
				if (item.Locality && item.Locality.trim().length > 0) localities.push(item.Locality)
			})
		}
	})
})

$(document).ready(function() {

	function setFund(fund) {
		if (!fund.LNR) fund = JSON.parse(fund)

		$('#fund-save').disable(false)
		$body = $('#fund-table-body');
		$body.html('')

		function getCaption(field) {
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

		function addRow(caption, name, value) {
			var $tr = $('<tr>');
			$('<td>').css('vertical-align','top').text(caption).appendTo($tr);
			$td = $('<td>').appendTo($tr)
			$('<input name="' + name + '" value="' + value + '">').appendTo($td)
			$tr.appendTo($body)
		}

		function addRowLong(caption, name, value) {
			var $tr = $('<tr>');
			$('<td>').css('vertical-align','top').text(caption).appendTo($tr);
			$td = $('<td>').appendTo($tr)
			$('<input name="' + name + '" value="' + value + '" size="55">').appendTo($td)
			$tr.appendTo($body)
		}

		function addFloat(caption, name, value) {
			var $tr = $('<tr>');
			$('<td>').css('vertical-align','top').text(caption).appendTo($tr);
			$td = $('<td>').appendTo($tr)
			$('<input class="float-only" name="' + name + '" value="' + value + '">').appendTo($td)
			$tr.appendTo($body)
		}

		//current LNR, species, genus, family, author
		$cnt = $('#current-art-cnt');
		$cnt.html('');
		$('<input type="hidden" name="LNR" value="'+ fund.LNR + '">').appendTo($cnt);
		['Family', 'Genus', 'Species', 'AuthorYear'].forEach(function(k) {
			$('<input style="color:gray;border:1px solid #ebebeb;" name="'+k+'" value="'+fund[k]+'" readonly>').appendTo($cnt)
		})

		//art
		var $tr = $('<tr>');
		$('<td>').css('vertical-align','top').text('Art').appendTo($tr);
		$('<input id="fund-current-art" size="55" placeholder="Ingen art matcher fundet">').appendTo($('<td>')).appendTo($tr);
		$tr.appendTo($body);

		//set current art
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=lookupSpeciesByTaxon',
			data : {
				species: fund.Species,
				genus: fund.Genus,
				family: fund.Family
			},
			success: function(response) {
				$('#fund-current-art').val(response.FullName)
			}
		})
		
		//set art typeahead
		var path='ajax/edderkopper/actions.php?action=lookupSpecies';
		$("#fund-current-art").typeahead({
			minLength : 1,
			items : 20,
			source: function(query, process) {
				return $.get(path+'&search='+query, {}, function(data) {
					return process(data)
				});
			},
			displayText: function(item) {
				return item.FullName
			},
			afterSelect: function(item) {
				$('#fund-form input[name=Genus]').val(item.Genus)
				$('#fund-form input[name=Species]').val(item.Species)
				$('#fund-form input[name=Family]').val(item.Family)
				$('#fund-form input[name=AuthorYear]').val(item.SAuthor)
			}
		})

		//dato
		var $tr = $('<tr>');
		$('<td>').css('vertical-align','top').text('Dato').appendTo($tr);
		$td = $('<td>').appendTo($tr)
		$('<input class="number-only" name="Date_last" size="2" value="'+fund.Date_last+'">').appendTo($td)
		$('<span>').text('/').appendTo($td)
		$('<input class="number-only" name="Month_last" size="2" value="'+fund.Month_last+'">').appendTo($td)
		$('<span>').text('/').appendTo($td)
		$('<input class="number-only" name="Year_last" size="2" value="'+fund.Year_last+'">').appendTo($td)
		$tr.appendTo($body);

		//lokalitet
		addRowLong('Lokalitet', 'Locality', fund.Locality)
		$('#fund-form input[name=Locality]').typeahead({
			minLength : 1,
			showHintOnFocus: true,
			items : 20,
			source: localities
		})

		//utm, region with typeaheads
		var $tr = $('<tr>');
		$('<td>').css('vertical-align','top').text('').appendTo($tr);
		$td = $('<td>').appendTo($tr)
		$('<span>').text('UTM').appendTo($td)
		$('<input size="3" name="UTM10" value="' + fund.UTM10 + '">').appendTo($td)
		$('<span>').text('Region').appendTo($td)
		$('<input size="3" name="Region" value="' + fund.Region + '">').appendTo($td)
		$tr.appendTo($body);
		$('#fund-form input[name=UTM10]').typeahead({
			minLength : 1,
			showHintOnFocus: true,
			items : 20,
			source: utm
		})
		$('#fund-form input[name=Region]').typeahead({
			minLength : 1,
			showHintOnFocus: true,
			items : 20,
			source: regions
		})

		//lat lng leg det
		addFloat('Lat.', 'LatPrec', fund.LatPrec)
		addFloat('Lng.', 'LongPrec', fund.LongPrec)

		//det leg with typeahead
		addRowLong('Det.', 'Det', fund.Det)
		$('#fund-form input[name=Det]').typeahead({
			minLength : 1,
			showHintOnFocus: true,
			items : 10,
			source: dets
		})
		addRowLong('Leg.', 'Leg', fund.Leg)
		$('#fund-form input[name=Leg]').typeahead({
			minLength : 1,
			showHintOnFocus: true,
			items : 10,
			source: legs
		})

		//collection with typeahead
		addRow('Samling', 'Collection', fund.Collection)
		$('#fund-form input[name=Collection]').typeahead({
			minLength : 1,
			showHintOnFocus: true,
			items : 10,
			source: collections
		})

		//KatalogNrPers
		addRow('KatNrPers', 'KatalogNrPers', fund.KatalogNrPers)

		//antal
		var $tr = $('<tr>');
		$('<td>').css('vertical-align','top').text('Antal').appendTo($tr);
		$td = $('<td>').appendTo($tr)
		$('<span>').text(' han').appendTo($td)
		$('<input class="number-only" name="MaleCount" size="2" value="'+fund.MaleCount+'">').appendTo($td)
		$('<span>').text(' hun').appendTo($td)
		$('<input class="number-only" name="FemaleCount" size="2" value="'+fund.FemaleCount+'">').appendTo($td)
		$('<span>').text(' ungdyr').appendTo($td)
		$('<input class="number-only" name="JuvenileCount" size="2" value="'+fund.JuvenileCount+'">').appendTo($td)
		$tr.appendTo($body);

		//create a delete button
		var $tr = $('<tr>'),
				$td = $('<td colspan="2" style="padding-top:20px;">').appendTo($tr)
		$('<button class="delete" type="button" id="fund-delete">').text('Slet fund').appendTo($td);
		$tr.appendTo($body);
		
	}


	$("#fund-lnr").on('keydown', function(e) {	
		if (e.which == 13 ) {
			$("#edit-fund").trigger('click');
		}		
	})

	$("#edit-fund").click(function() {
		var LNR = $("#fund-lnr").val();
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=fundGet',
			data : {
				LNR: LNR
			},
			success: function(response) {
				setFund(response)
			}
		})
	})

	$("#create-fund").on('click', function() {
		$.ajax({
			dataType: 'json', 
			url : 'ajax/edderkopper/actions.php?action=fundCreate',
			success : function(response) {
				$("#fund-lnr").val(response.LNR)
				$("#edit-fund").trigger('click')
				$('#fund-messages').text('Fund oprettet ...').show().fadeOut(10000)
			}
		});
	})

	$("#fund-save").on('click', function() {
		var params=$('#fund-form').serialize();
		params+='&action=fundSave';
		$.ajax({
			url : 'ajax/edderkopper/actions.php?'+params,
			success : function(response) {
				$('#fund-messages').text(response).show().fadeOut(10000)
			}
		});
	})

	$('body').on('click', '#fund-delete', function() {
		if (confirm('Slet det aktuelle Fund. Er du sikker?')) {
			$.ajax({
				url : 'ajax/edderkopper/actions.php?action=fundDelete',
				data: {
					LNR: $("#fund-lnr").val()
				},
				success : function(response) {
					$('#fund-table-body').html('')
					$('#current-art-cnt').html('')
					$('#fund-messages').text('Fund slettet ...').show().fadeOut(10000)
					$("html, body").animate({ scrollTop: 0 }, "fast");
					$("#fund-lnr").val('').focus()
				}
			})
		}
	})
	

})
