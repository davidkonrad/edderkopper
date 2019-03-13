$(document).ready(function() {

	$('#photo').DataTable({
		lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
		ajax: {
			url: 'ajax/edderkopper/actions_photo.php?action=list',
			contentType: 'json',
			dataType: 'json',
			dataSrc: function(d) {
				return JSON.parse(d)
			}
		},
		columns: [
			{ data: 'PhotoId', title: '#' },
			{ 
				data: 'specie_name', 
				title: 'Specie' ,
				render: function(data, type, row) {
					if (type == 'display') return '<em>'+data+'</em>'+' '+'<small>#'+row.SpeciesId+'</small>'
					return data
				}
			},
			{ 
				data: 'Filename', 
				title: 'Filename',
				render: function(data, type, row) {
					//if (type == 'display') return '<img src="lissner'+data+'" width="100">'
					return data
				}
			},
			{ data: 'SubjectDK', 
				title: 'SubjectDK', 
				render: function(data, type, row) {
					return type == 'display'
						? data.length>40 ? '<span title="'+data+'">'+data.substr(0, 40)+'&hellip;</span>' : data
						: data
				}
			},
			{ data: 'SubjectUK', 
				title: 'SubjectUK',
				render: function(data, type, row) {
					return type == 'display'
						? data.length>40 ? '<span title="'+data+'">'+data.substr(0, 40)+'&hellip;</span>' : data
						: data
				}
			},
		],
		language: {
	    "sEmptyTable":     "Ingen tilgængelige data (prøv en anden søgning)",
	    "sInfo":           "Viser _START_ til _END_ af _TOTAL_ rækker",
	    "sInfoEmpty":      "Viser 0 til 0 af 0 rækker",
 		  "sInfoFiltered":   "(filtreret ud af _MAX_ rækker i alt)",
 		  "sInfoPostFix":    "",
 		  "sInfoThousands":  ",",
	    "sLengthMenu":     "Vis _MENU_ rækker",
	    "sLoadingRecords": "Henter data...",
	    "sProcessing":     "Processing...",
	    "sSearch":         "Filter",
	    "sZeroRecords":    "Ingen rækker matchede filteret",
	    "oPaginate": {
        "sFirst":    "Første",
        "sLast":     "Sidste",
        "sNext":     "Næste",
        "sPrevious": "Forrige"
	    },
	    "oAria": {
        "sSortAscending":  ": activate to sort column ascending",
        "sSortDescending": ": activate to sort column descending"
	    }
		}
	})

});

