
var Sahara = {
	map : null, 

	initSearchResult : function() {
		$("#result-table").dataTable({
			//as in SearchBase.php
			bJQueryUI: true,
	        bPaginate: false,  
	        bInfo: false,  
			bLengthChange: false,
	        bFilter: false,
	        bAutoWidth: false,
			asStripClasses:[],
			bSortClasses: false,
			sDom: 'T<"clear">lfrtip',
			//define columns
			aoColumns: [ 
				//detaljeside
				{"bSortable": false,
				 "sWidth" : "20px"
				},
				//DarwinScientificName 	
				{"bVisible": true,
				 "sWidth": "250px"
				},
				//DarwinScientNameAuthor
				{"bVisible": true,
				 "sWidth": "450px"
				},
				//DarwinInstitutionCode
				{"bVisible": true,
				 "sWidth": "300px"
				}
			]
		});
		$(".dataTables_filter").hide();
		$(".fg-toolbar").hide();
		$(".DTTT_container").hide();
	},

	createDetailsMap : function() {
		Sahara.map = new google.maps.Map(document.getElementById("map"), {
			center: new google.maps.LatLng(0.450, 14.50),
			zoom: 4,
			mapTypeId: google.maps.MapTypeId.TERRAIN,
			zoomControl: true,
			streetViewControl: false,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL
			}
		});
	
		Sahara.map.enableKeyDragZoom({
			visualEnabled: true,
			visualPosition: google.maps.ControlPosition.LEFT,
			visualPositionMargin: new google.maps.Size(35, 0),
			visualImages: {
			},
			visualTips: {
				off: "Zoom til",
				on: "Zoom fra"
			}
		});
	},

	createPolygons : function(species) {
		Search.wait(true);

		var url= 'ajax/sahara_latlong.php?species='+species;
		$.ajax({
			url: url,
			cache: true,
			async: true,
			dataType: 'json',
			timeout: 60000,
			error :function(jqXHR, textStatus, errorThrown) {
				alert(jqXHR.responseText+' '+textStatus+' '+errorThrown);
			},
			success: function(html) {
				$.each(html, function(id, ll) {
					var coords = [new google.maps.LatLng(parseFloat(ll.lat)-0.45, parseFloat(ll.long)-0.45),
								new google.maps.LatLng(parseFloat(ll.lat)+0.45, parseFloat(ll.long)-0.45),
								new google.maps.LatLng(parseFloat(ll.lat)+0.45, parseFloat(ll.long)+0.45),
								new google.maps.LatLng(parseFloat(ll.lat)-0.45, parseFloat(ll.long)+0.45)];

					var color = (ll.status==2) ? 'lime' : 'yellow';

					var poly = new google.maps.Polygon({
			    		paths: coords,
						strokeColor: '#FFF',
						strokeOpacity: 0.1,
						strokeWeight: 2,
						fillColor: color,
						fillOpacity: 0.75
					});

					poly.setMap(Sahara.map);
				});
			},
			complete : function() {
				Search.wait(false);
			}
		});
	}
};
