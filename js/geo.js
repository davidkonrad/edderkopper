/**********************************************************
Namespace with singleton model. 
"Geo" utilities library. Javascript for dealing with 

	- getlocation
	- google maps
	- oiorest.dk 
	- kommuner
	- UTM
	- habitater
	- handles table geocoding
	..

Using 
	js/utm.js
	json/habitater_27062013.json
	json/kommuner
	json/kommuner_geometryWkt_details.json

	proxy.php

***********************************************************/

var Geo = {

	/*
		default settings for a google map with Denmark in center
	*/
	DK_Lat : 56.30, 
	DK_Long : 11.65,
	DK_Zoom : 7,

	/* 
		default colors and weight for polygons, change these before calling showPolygon
	*/
	strokeColor : '#FFF380', 
	strokeWeight : 0.5,
	fillColor : 'blue',

	/*
		default strings for visual tips on zoom buttons, change according to language
	*/
	zoom_off : "Zoom til",
	zoom_on : "Zoom fra",

	/*
		reference to UTM grid generated by showUTM10Grid
		holding an array of polygons
	*/
	UTM10_Grid : null,

	/*
		map polygon data
	*/
	polygons: [],
	bounds: null,

	/*
		shows wait cursor, eg rotating wheel, or not
	*/
	wait : function(mode) {
		if (mode) {
			$('html, body').css("cursor", "wait");
		} else {
			$('html, body').css("cursor", "auto");
		}
	},

	/*
		default ajax error for test purposes
	*/
	ajaxError : function(jqXHR, textStatus, errorThrown) {
		console.log('error :'+jqXHR.responseText+' '+textStatus+' '+errorThrown);
		alert('error :'+jqXHR.responseText+' '+textStatus+' '+errorThrown);
	},

	/*
		generates a google map with dragzoom 
		target is elem_id, centered at lat,long with zoomlevel zoom
		returns map as var
		requires :
			<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true"></script>
			<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/keydragzoom/src/keydragzoom.js?"></script>
	*/
	googleMap : function(elem_id, lat, long, zoom) {
		lat = (typeof lat!='undefined') ? lat : Geo.DK_Lat;
		long = (typeof long!='undefined') ? long : Geo.DK_Long;
		zoom = (typeof zoom!='undefined') ? zoom : Geo.DK_Zoom;
		var latlong = new google.maps.LatLng(lat, long);

		var stylers = [{
			//remove "Danmark / Denmark"
			featureType: "administrative.country",
			elementType: 'labels',
			stylers: [{ visibility: 'off' }]
		},{
			//remove points of interest
			featureType: "poi",
			elementType: 'all',
			stylers: [{ visibility: 'off' }]
		}, {
			//remove road labels
	    featureType: "road",
	    elementType: "labels",
	    stylers: [{ "visibility": "off" }]
	  }];

		var mapOptions = {
			zoom: zoom,
			center: latlong,
			zoomControl: true,
			streetViewControl: false,
			fullscreenControl: false,
			zoomControlOptions: {
				fullscreenControl: false,
				style: google.maps.ZoomControlStyle.SMALL,
        position: google.maps.ControlPosition.RIGHT_TOP
			},
			styles : stylers,
			mapTypeId: google.maps.MapTypeId.TERRAIN
		}
		var map = new google.maps.Map(document.getElementById(elem_id), mapOptions);	

		if (map.keyDragZoomEnabled) map.enableKeyDragZoom({
			visualEnabled: true,
			visualImages: {},
			visualTips: {
				off: Geo.zoom_off,
				on: Geo.zoom_on
			}
		});
		return map;
	},

	/*
		generates a googlemap with a marker in center lat, lng
		no dragzoom, zoom to marker
		use this to get a close map of a specific location
		return map
	*/
	googleMapMarker : function(elem_id, lat, lng, zoom) {
		lat = (typeof lat!='undefined') ? lat : Geo.DK_Lat;
		lng = (typeof lng!='undefined') ? lng : Geo.DK_Long;
		zoom = (typeof zoom!='undefined') ? zoom : 12; //real close

		var latlng = new google.maps.LatLng(lat, lng);
		var mapOptions = {
			zoom: zoom,
			center: latlng,
			zoomControl: true,
			streetViewControl: false,
			fullscreenControl: false,
			zoomControlOptions: { style: google.maps.ZoomControlStyle.SMALL	},
			mapTypeId: google.maps.MapTypeId.TERRAIN
		}
		var map = new google.maps.Map(document.getElementById(elem_id), mapOptions);	

		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			draggable: false
		});

		map.enableKeyDragZoom({
			visualEnabled: false
		});

		return map;
	},

	/*
		remove polygons and markers
	*/
	resetMap : function(map) {
		Geo.bounds = new google.maps.LatLngBounds(); 
		Geo.polygons.forEach(function(p) {
			p.setMap(null);
		})
		Geo.polygons = [];
		/*
		for (var i = 0; i < Geo.markers.length; i++ ) {
			Geo.markers[i].setMap(null);
  	}
		*/
	},

	/* 
		loads list of kommuner, sort it and insert them as options in a select
		12.11.2013 : now uses local copy of kommuner.json for better performance
		29.01.2019 : due to iorest no longer exists, now using local version of kommune borders in wgs84 format
	*/
	populateKommuner : function(select) {
		var url = "json/kommuner.json";
		var ka = [];
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(data) {
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
		//get kommune borders
		$.getJSON('json/kommuner_wgs84.json', function(json) {
			Geo.kommuner_wgs84 = json;
			//console.log(Geo.kommuner_wgs84);
		})
	},

	showKommune : function(knr, map) {
		Geo.resetMap();
		for (var k in Geo.kommuner_wgs84.kommuner_WGS84) {
			if (Geo.kommuner_wgs84.kommuner_WGS84[k].knr == knr) {
				var borders = Geo.kommuner_wgs84.kommuner_WGS84[k].border;
				for (var i=0;i<borders.length;i++) {
					var polyarray = borders[i].coords.split('/');
					Geo.showPolygon(polyarray, map, true);
				}
			}
		}
	},

	/* 
		generates UTM10_Grid array
	*/
	createUTM10Grid : function(map) {
		Geo.UTM10_Grid = [];
		for (var utm in UTM_LatLng) {
			utmcoords = eval(UTM_LatLng[utm]);
			var poly = new google.maps.Polygon({
				paths: utmcoords,
				strokeColor: "blue",
				strokeOpacity: 0.50,
				strokeWeight: 0.50,
				fillColor: "transparent",
				utm : utm
			});
			//pass click events to the underyling map
			google.maps.event.addListener(poly, 'click', function(args) {
				google.maps.event.trigger(map, 'click', args);
			});

			Geo.UTM10_Grid.push(poly);
		}
		return Geo.UTM10_Grid;
	},

	/*
		shows UTM10 grid on a map. 
		If <elem_id> is defined, an event to show utm on mouseover at <elem_id> is added
		NOTE :
			Uses utm.js
	*/
	showUTM10Grid : function(map, elem_id) {
		if (Geo.UTM10_Grid==null) Geo.UTM10_Grid=Geo.createUTM10Grid(map);
		for (var i=0;i<Geo.UTM10_Grid.length;i++) {
			var poly=Geo.UTM10_Grid[i];
			poly.setMap(map);
			if (typeof elem_id !='undefined') {
				google.maps.event.addListener(poly, 'mouseover', function() {
					$(elem_id).text(this.utm);
				});
				google.maps.event.addListener(poly, 'mouseout', function() {
					$(elem_id).text('');
				});
			}
		}
	},

	/* 
		hide UTM10 grid, if it is generated and hold by UTM_grid
	*/
	hideUTM10Grid : function() {
		if (Geo.UTM10_Grid==null) return;
		for (var i=0;i<Geo.UTM10_Grid.length;i++) {
			Geo.UTM10_Grid[i].setMap(null);
		}
	},

	/*
		draws a polygon on a map. 
		Polygon is an array of strings on the form lat,long
	*/
	showPolygon : function(polygon, map, zoom) {
		var lat, lng;
		var coordinates = polygon;
		var latlng;
		var vertices = new Array(coordinates.length);
		for (var index = 0; index < coordinates.length; index++) {
			ll=coordinates[index].split(',');
			lat = parseFloat(ll[1]);
			lng = parseFloat(ll[0]);
			latlng = new google.maps.LatLng(lat, lng);
			vertices[index] = latlng;
			Geo.bounds.extend(latlng); 

/*
			var marker = new google.maps.Marker({
				icon : 'none',
				position: latlng,
				map: map, //polygonMap,
				draggable: true,
      	strokeColor: "transparent",
		    fillColor: "transparent"
			});
*/
		}

		var graense = new google.maps.Polygon({
			paths: vertices,
			strokeOpacity: 0.8,
			strokeWeight: Geo.strokeWeight,
			strokeColor: Geo.strokeColor,
			fillColor: Geo.fillColor 
		});

		//zoom
		if (zoom) map.fitBounds(Geo.bounds); 
		//add the polygonborder
		graense.setMap(map);
		Geo.polygons.push(graense);
	},


	/*
		show <polygon> on <map>
		Polygon is an array of strings on the form lat,long
	*/
	showPolygonSimple : function (polygon, map) {
	var lat, lng;
	var coordinates = polygon;
	var latlng;
	var vertices = new Array(coordinates.length);
	for (var index = 0; index < coordinates.length; index++) {
		ll=coordinates[index].split(',');
		lat = parseFloat(ll[1]);
		lng = parseFloat(ll[0]);
		latlng = new google.maps.LatLng(lat, lng);
		vertices[index] = latlng;
	}

	var poly = new google.maps.Polygon({
		paths: vertices,
		strokeOpacity: 0.8,
		strokeWeight: Geo.strokeWeight,
		strokeColor: Geo.strokeColor,
		fillColor: Geo.fillColor 
	});

	//add the polygonborder
	poly.setMap(map);
},

	/****************************************************************************
		regioner / zootopo.js
	*/
	Regioner : {
		/*
			show region (multiple polygons from zootopo.js) on map
		*/
		showRegion : function(region, map) {
			Geo.resetMap();
			var polygons = eval('Zootopo_'+region);
			for (var i=0;i<polygons.length;i++) {
				Geo.showPolygon(polygons[i], map, true);
			}
		}
	},

	/****************************************************************************
		habitater
	*/
	Habitater : {
		json : null,
		ready : false,

		/* 
			the JSON habitat file contains atm 328 habitat polygons, and has a size of 2.1 mb
			a slight delay of a few hundred ms must be expected
			therefore, init() must be called in the beginning of a session or, 
			populate() and nameToPolygons must be called with some kind of delay
			check Geo.Habitater.ready to see if the JSON has been loaded
		*/
		init : function(pathToJSON) {
			pathToJSON = (typeof pathToJSON=='undefined') ? 'json/habitater_27062013.json' : pathToJSON;
			$.getJSON(pathToJSON, function(json) {
				Geo.Habitater.json=json;
				Geo.Habitater.ready=true;
			});
		},

		/* 
			return a sorted array of unique habitat names 
		*/
		sortedList : function() {
			if (!Geo.Habitater.ready) return [];
			var names=[];
			for (var i=0;i<Geo.Habitater.json.habitater.length;i++) {
				var item=Geo.Habitater.json.habitater[i];
				if (names.indexOf(item.navn)<0) names.push(item.navn);
			}
			return names.sort();
		},
		
		/* 
			populates a <select> #element with a sorted list of habitat names 
		*/
		populate : function(element) {
			var list=this.sortedList();
			for (var i=0;i<list.length;i++) {
				$(element).append('<option value="'+list[i]+'">'+list[i]+'</option>');
			}
		},

		/* 
			populates a <select> #element with a sorted list of habitat names 
			the list of habitats is loaded from habitater_navne.json 
		*/
		populateSimple : function(element) {
			var habitater = [];
			$.getJSON('json/habitater_navne.json', function(json) {
				for (var i=0;i<json.length;i++) {
					habitater.push(json[i].navn);
				}
				habitater.sort();
				for (var i=0;i<habitater.length;i++) {
					$(element).append('<option value="'+habitater[i]+'">'+habitater[i]+'</option>');
				}
			});
		},

		/* 
			return all polygons associated with a habitat name 
		*/
		nameToPolygons : function(name) {
			if (!Geo.Habitater.ready) return [];
			var result=[];
			for (var i=0;i<Geo.Habitater.json.habitater.length;i++) {
				if (Geo.Habitater.json.habitater[i].navn==name) {
					result.push(Geo.Habitater.json.habitater[i].coords);
				}
			}
			return result;
		},

		/* 
			show a habitat polygon for <name> on <map>
		*/
		showHabitat : function(name, map) {
			Geo.resetMap();
			if ((!Geo.Habitater.ready) || (typeof map=='undefined')) return false;
			var polygons = Geo.Habitater.nameToPolygons(name);
			for (var i=0;i<polygons.length;i++) {
				var polyarray = polygons[i].split('/');
				Geo.showPolygon(polyarray, map, true);
			}
		}
	},

};


Geo.Habitater.init();

