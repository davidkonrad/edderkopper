
var polygonMap;
var poly;
var markers = [];
var path = new google.maps.MVCArray;
//kommune
var bounds = new google.maps.LatLngBounds(); 

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
		var myLatLng = new google.maps.LatLng(56.30, 12); //11.65  
		var mapOptions = {
			zoom: 6,
			center: myLatLng,
			zoomControl: true,
			streetViewControl: false,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL
			},
			mapTypeId: google.maps.MapTypeId.TERRAIN
		}
		polygonMap = new google.maps.Map(document.getElementById("polygon-map"), mapOptions);	

		polygonMap.enableKeyDragZoom({
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

		poly = new google.maps.Polygon({
			strokeWeight: 1,
			strokeColor: 'transparent',
			fillColor: 'transparent'
		});
	
		poly.setMap(polygonMap);
		poly.setPaths(new google.maps.MVCArray([path]));

		google.maps.event.addListener(polygonMap, 'click', addPoint);
	},

	resetPolygon : function() {
		alert('ok');
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

/*
	populateKommuner : function(select) {
		var url = "proxy.php?url=http://geo.oiorest.dk/kommuner.json";
		var ka = [];
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(data){
				$.each(data, function(index, kommune) {
					ka[ka.length+1]={navn:kommune.navn,nr:kommune.nr}
				});
				
				ka.sort(function(a, b) {
					var A=a.navn.toLowerCase();
					var B=b.navn.toLowerCase();
					if (A<B) return -1;
					if (A>B) return 1;
					return 0;
				})

				for (var i=0;i<=ka.length;i++) {
					if (ka[i]!==undefined) {
						$('<option value="'+ka[i].nr+'">'+ka[i].navn+'</option>').appendTo(select);
					}
				}
			}
		});
	},
*/

	initKommuner : function() {
		//Edderkopper.
		Geo.populateKommuner('#kommune');

		$("#kommune").change(function() {
			var knr=$("#kommune option:selected").val()
			if (knr!='') {
				//Edderkopper.getKommuneGraense(knr);
				Geo.getKommuneGraense(knr, polygonMap);
				$("#hidden-kommune").val(knr);
			}
		});
	
		//some delay until kommuner is inserted ny populateKommuner()
		setTimeout(function() {
			$("#kommune").chosen();
		}, 500);
	},

/*
	getKommuner : function() {
		var url = "proxy.php?url=http://geo.oiorest.dk/kommuner.json";
		var ka = [];
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(data){
				$.each(data, function(index, kommune) {
					ka[ka.length+1]={navn:kommune.navn,nr:kommune.nr}
				});
				
				ka.sort(function(a, b) {
					var A=a.navn.toLowerCase();
					var B=b.navn.toLowerCase();
					if (A<B) return -1;
					if (A>B) return 1;
					return 0;
				})

				for (var i=0;i<=ka.length;i++) {
					if (ka[i]!==undefined) {
						$('<option value="'+ka[i].nr+'">'+ka[i].navn+'</option>').appendTo("#kommune");
					}
				}
				$("#kommune").chosen();

				$("#kommune").change(function() {
					var knr=$("#kommune option:selected").val()
					if (knr!='') {
						Edderkopper.getKommuneGraense(knr);
						$("#hidden-kommune").val(knr);
					}
				});
			}
		});
	},
*/

/*
	getKommuneGraense : function(nr) {
		var type="kommuner";
		var url= "proxy.php?url=http://geo.oiorest.dk/"+type+"/"+nr+"/graense.json"; 			
		//alert(url);
		$.ajax({
			url: url,
			dataType: 'json',
			success:  function (data) {
				for (var index = 0; index<data.coordinates.length; index++) {
					Edderkopper.showKommuneGraense(data.coordinates[index], type);
				}
			},
			error: function (xhr, ajaxOptions, thrownError){
				alert(xhr+' '+ajaxOptions+' '+thrownError);										
			}   
		});
	},

	showKommuneGraense : function(polygon) {
		var lat, lng;
		var paths;
		var coordinates= polygon[0];
		var latlng;
		var vertices = new Array(coordinates.length);
		for (var index = 0; index < coordinates.length; index++) {
			lat = parseFloat(coordinates[index][1]);
			lng = parseFloat(coordinates[index][0]);
			latlng = new google.maps.LatLng(lat, lng);
			vertices[index] = latlng;
			bounds.extend(latlng); 

			//add each 20 latlong as invisible marker 
			if (index % 20 == 0)  {
				var marker = new google.maps.Marker({
					icon : 'none',
					position: latlng,
					map: polygonMap,
					draggable: true,
		        		strokeColor: "transparent",
				        fillColor: "transparent"
				});
				markers.push(marker);
				path.insertAt(path.length, latlng);
			}
		}

		var hole;
		if (polygon.length > 1) {
			//alert('flere polygoner');
			coordinates= polygon[1];
			hole= new Array(coordinates.length);
			for (var index = 0; index < coordinates.length; index++) {
				lat = parseFloat(coordinates[index][1]);
				lng = parseFloat(coordinates[index][0]);
				latlng = new google.maps.LatLng(lat, lng);
				hole[index] = latlng;
				bounds.extend(latlng); 
			}
		}
	
		if (polygon.length > 1) {
			//alert('flere polygoner');
			paths= new Array(2);
			paths[0]= vertices;
			paths[1]= hole;
		} else paths= vertices;

		var graense = new google.maps.Polygon({
			paths: paths,
			strokeOpacity: 0.8,
			strokeWeight: 2,
			strokeColor: '#FFF380',
			fillColor: 'blue' 
		});

		polygonMap.fitBounds(bounds); 
		graense.setMap(polygonMap);
	},
*/
	showZootopo : function(polygon) {
		var lat, lng;
		//var paths;
		var coordinates = polygon;
		var latlng;
		var vertices = new Array(coordinates.length);
		for (var index = 0; index < coordinates.length; index++) {
			ll=coordinates[index].split(',');
			lat = parseFloat(ll[1]);
			lng = parseFloat(ll[0]);
			latlng = new google.maps.LatLng(lat, lng);
			vertices[index] = latlng;
			bounds.extend(latlng); 

			var marker = new google.maps.Marker({
				icon : 'none',
				position: latlng,
				map: polygonMap,
				draggable: true,
        		strokeColor: "transparent",
		        fillColor: "transparent"
			});
			/*
			dont insert in markers to avoid polygon in search
			markers.push(marker);
			path.insertAt(path.length, latlng);
			*/
		}

		var graense = new google.maps.Polygon({
			paths: vertices,
			strokeOpacity: 0.8,
			strokeWeight: 2,
			strokeColor: '#FFF380',
			fillColor: 'blue' 
		});

		polygonMap.fitBounds(bounds); 
		graense.setMap(polygonMap);
	},

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
				 "sWidth": "50px"
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
				{"bVisible": ($("#loggedin").val()!=undefined) ? true : false,
				 "sWidth": "20px"
				}

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

/*
	kommunePolygonDraw : function(polygon) {
		var lat, lng;
		var paths;
		var coordinates= polygon[0];
		var latlng;
		var bounds = new google.maps.LatLngBounds();
		var vertices = new Array(coordinates.length);
		for (var index = 0; index < coordinates.length; index++) {
			lat = parseFloat(coordinates[index][1]);
			lng = parseFloat(coordinates[index][0]);
			latlng = new google.maps.LatLng(lat, lng);
			vertices[index] = latlng;
			bounds.extend(latlng); 
		}

        var gr = new google.maps.Polygon({
          paths: vertices,
          strokeColor: "#343434",
          strokeOpacity: 0.2,
          strokeWeight: 2,
          fillColor: '#'+(Math.random()*0xFFFFFF<<0).toString(16),
          fillOpacity: 0.25
        });

        gr.setMap(map);

		var hole;
		if (polygon.length > 1) {
			//alert('flere polygoner');
			coordinates= polygon[1];
			hole= new Array(coordinates.length);
			for (var index = 0; index < coordinates.length; index++) {
				lat = parseFloat(coordinates[index][1]);
				lng = parseFloat(coordinates[index][0]);
				latlng = new google.maps.LatLng(lat, lng);
				hole[index] = latlng;
				bounds.extend(latlng); 
			}
		}
	
		if (polygon.length > 1) {
			//alert('flere polygoner');
			paths= new Array(2);
			paths[0]= vertices;
			paths[1]= hole;
		} else paths= vertices;
	},

/*
	kommunePolygon : function(nr) {
		$("#search-button").attr('disabled','disabled');
		var type="kommuner";
		var url= "proxy.php?url=http://geo.oiorest.dk/"+type+"/"+nr+"/graense.json"; 			
		$.ajax({
			url: url,
			dataType: 'json',
			success:  function (data) {
				for (var index = 0; index<data.coordinates.length; index++) {
					Edderkopper.showKommuneGraense(data.coordinates[index], type);
				}
				$("#search-button").removeAttr('disabled');
			},
			error: function (xhr, ajaxOptions, thrownError){
				alert(xhr+' '+ajaxOptions+' '+thrownError);
				$("#search-button").removeAttr('disabled');										
			}   
		});
	},
*/
	edit : function(table, field, value) {
		//alert(table+' '+field+' '+value);
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
		Geo.showUTM10Grid(polygonMap,"#polygon-map-utm");
		$("#btn-polygon-map-utm").attr('disabled','disabled');
	}
/*
	showUTM : function() {
		var poly, marker, utmcoords;

		for (var utm in UTM_LatLng) {
			utmcoords = eval(UTM_LatLng[utm]);
			var poly = new google.maps.Polygon({
				paths: utmcoords,
				strokeColor: "blue",
				strokeOpacity: 0.50,
				strokeWeight: 0.50,
				fillColor: "transparent",
				utm : utm,
				test : Math.floor(Math.random() * 10) + 2
			});

			google.maps.event.addListener(poly, 'mouseover', function() {
				$("#polygon-map-utm").text(this.utm);
			});

			google.maps.event.addListener(poly, 'mouseout', function() {
				$("#polygon-map-utm").text('');
			});

			poly.setMap(polygonMap);
			//Details.polygons.push(poly);
		}
		$("#btn-polygon-map-utm").attr('disabled','disabled');
	}
*/
	
};

/** artsside */

var Art = {
	map: null,

	initMap : function() {
		Art.map = new google.maps.Map(document.getElementById("details-map"), {
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
	},

	showFund : function(species) {
		var url='ajax/edderkopper_fund.php?species='+species;
		var icon='ico/Circle_Blue.png';
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
						icon : icon,
						position: new google.maps.LatLng(fund.lat,fund.long),
						map: Art.map
					});
				});
			}
		});

	}
}
	
