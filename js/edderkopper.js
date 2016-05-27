
var polygonMap;
var poly;
var markers = [];
var path = (typeof google !== 'undefined') ? new google.maps.MVCArray : null;
//kommune
var bounds = (typeof google !== 'undefined') ? new google.maps.LatLngBounds() : null;

function addPoint(event) {
	poly.setOptions({ strokeColor: '#FFF380', fillColor: '#FFF380'});
	path.insertAt(path.length, event.latLng);

	var marker = new google.maps.Marker({
		icon : 'ico/csquare.png',
		position: event.latLng,
		map: polygonMap,
		draggable: true
	});
	markers.push(marker);
	marker.setTitle("#" + path.length);

	google.maps.event.addListener(marker, 'click', function() {
		marker.setMap(null);
		for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
		markers.splice(i, 1);
		path.removeAt(i);
	});
	
	google.maps.event.addListener(marker, 'dragend', function() {
		for (var i = 0, I = markers.length; i < I && markers[i] != marker; ++i);
		path.setAt(i, marker.getPosition());
	});
}


var Edderkopper = {

	initPolygonMap : function() {
		polygonMap = Geo.googleMap("polygon-map", Geo.DK_Lat, Geo.DK_Long-1.21,  7); //6
		poly = new google.maps.Polygon({
			strokeWeight: 1,
			strokeColor: 'transparent',
			fillColor: 'transparent'
		});
	
		poly.setMap(polygonMap);
		poly.setPaths(new google.maps.MVCArray([path]));

		google.maps.event.addListener(polygonMap, 'click', addPoint);
	},

	back : function() {
		Search.back();
		google.maps.event.trigger(polygonMap, 'resize');
	},

	resetPolygon : function() {
		//e.stopPropagation();
		//e.preventDefault();
		/*
		for (var i = 0; i < markers.length; i++ ) {
			markers[i].setMap(null);
		}
		for (i=0;i<=path.getLength();i++){
			path.removeAt(i);
			path.clear();
			path.pop();
		}
		markers=[];
		*/
		return true;
	},

	initKommuner : function() {
		Geo.populateKommuner('#kommune');

		$("#kommune").change(function() {
			var knr=$("#kommune option:selected").val()
			if (knr!='') {
				Geo.getKommuneGraense(knr, polygonMap);
				$("#hidden-kommune").val(knr);
			}
		});
	},

	initSearchResult : function() {
		//var sess_lang=$('[name="sess_lang"]').val();
		var lang = System.getLang()==2 ? 'lang/dataTables.en' : 'lang/dataTables.da';
		
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
			//sDom: 'T<"clear">lfrtip',
			sDom: '<"clear">lfrtip',
			oLanguage : {
				sUrl : lang
			},
			//define columns
			aoColumns: [ 
				//artslink
				{"bSortable": false,
				 "sWidth" : "20px"
				},
				//details popup		
				{"bSortable": false,
				 "sWidth": "20px"
				},
				//art / species
				{"bVisible": true,
				 "sWidth": "150px"
				},
				//date
				{"bVisible": true,
				 "sWidth": "70px",
				 "sType": "edderkoppe-dato"
				},
				//locality
				{"bVisible": true,
				 "sWidth": "300px"
				},
				//UTM
				{"bVisible": true,
				 "sWidth": "60px"
				},
				//leg
				{"bVisible": true,
				 "sWidth": "100px"
				},
				//det
				{"bVisible": true,
				 "sWidth": "100px"
				},
				//collection
				{"bVisible": true,
				 "sWidth": "100px"
				},

				//den_danske_rowdliste
				{"bVisible": true,
				 "sWidth": "50px"
				},

				//--> hidden columns
				/*
				//lat
				{"bVisible": false,
				 "sWidth": "50px"
				},
				//long
				{"bVisible": false,
				 "sWidth": "50px"
				},
				//LNR
				{"bVisible": false,
				 "sWidth": "50px"
				}
				*/
				
				//edit
				/*
				{"bVisible": ($("#loggedin").val()!=undefined) ? true : false,
				 "sWidth": "20px"
				}
				*/

			]
		});
		$(".dataTables_filter").hide();
		$(".fg-toolbar").hide();
		$(".DTTT_container").hide();

		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"edderkoppe-dato-pre": function (a) {
				var dato = a.split('/');
				if (dato[0].length==1) dato[0]='0'+dato[0];
				if (dato[1].length==1) dato[1]='0'+dato[1];
				return (dato[2] + dato[1] + dato[0]) * 1;
			},
			"edderkoppe-dato-asc": function ( a, b ) {
				return ((a < b) ? -1 : ((a > b) ? 1 : 0));
			},
			"edderkoppe-dato-desc": function ( a, b ) {
				return ((a < b) ? 1 : ((a > b) ? -1 : 0));
			}
		});
	},

	edit : function(table, field, value) {
		$("#search-result").hide();
		$("#edit-record").show();
		$.ajax({
			url : 'ajax/edit_record',
			data: {
				action: 'show',
				table : table,
				field: field,
				value : value
			},
			success : function(html) {
				$("#edit-record").html(html);
			}
		});
	},

	showUTM : function() {
		Geo.showUTM10Grid(polygonMap, "#polygon-map-utm");
		$("#btn-polygon-map-utm").val('Skjul UTM-felter');
		document.getElementById('btn-polygon-map-utm').onclick=Edderkopper.hideUTM;
	},

	hideUTM : function() {
		Geo.hideUTM10Grid(polygonMap, "#polygon-map-utm");
		$("#btn-polygon-map-utm").val('Vis UTM-felter');
		document.getElementById('btn-polygon-map-utm').onclick=Edderkopper.showUTM;
	}

};

/** artsside */

var Art = {
	map: null,

	initMap : function() {
		/*
		Art.map = new google.maps.Map(document.getElementById("map"), {
			center: new google.maps.LatLng(56.30, 12),
			zoom: 6,
			mapTypeId: google.maps.MapTypeId.TERRAIN,
			zoomControl: true,
			streetViewControl: false,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL
			}
		});
	
		Art.map.enableKeyDragZoom({
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
		*/
		Art.map = Geo.googleMap('map');
		Art.map.setZoom(6);
	},

	getIcon : function(year) {
		if (year<=1900) return 'ico/Circle_Blue.png';
		if (year<=1979) return 'ico/Circle_Orange.png';
		if (year<=2005) return 'ico/Circle_Yellow.png';
		return 'ico/Circle_Red.png';
	},

	getZIndex : function(year) {
		if (parseInt(year)<1901) return 1;
		if (parseInt(year)<1980) return 2;
		if (parseInt(year)<2006) return 3;
		return 4;
	},

	showFund : function(species) {
		var url='ajax/edderkopper_fund.php?species='+species;
		var icon, zindex;
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
				$.each(html, function(id, fund) {
					var Marker = new google.maps.Marker({
						icon : Art.getIcon(fund.year),
						zIndex: Art.getZIndex(fund.year),
						position: new google.maps.LatLng(fund.lat,fund.long),
						map: Art.map
					});

					google.maps.event.addListener(Marker, 'click', function() {
						Details.loadDetails(fund.utm, fund.lat, fund.long, fund.lnr);
					});
				});
			}
		});

	}
}
	
