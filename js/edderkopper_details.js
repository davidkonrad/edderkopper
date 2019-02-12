var COL_DETAILS = 2;
var COL_ART = 3;
var COL_DATO = 4;
var COL_LOKALITET = 5;
var COL_UTM = 6;
var COL_LEG = 7;
var COL_DET = 8;
var COL_SAMLING = 9;
var COL_LAT = 10;
var COL_LONG = 11;
var COL_LNR = 12;

var Details = {
	mapLoaded: false,
	map: null,
	stack: [],
	polygons : [],

	getHTML : function(lnr, art, locality, leg, utm, lat, long) {
		var ret='<b><em><a href="#" title="Klik for at se detaljer" '+
		'onclick="Details.loadDetails(&quot;'+utm+'&quot;,&quot;'+lat+'&quot;,&quot;'+long+'&quot;,&quot;'+lnr+'&quot;);">'+
		art+'</a></em></b><br>'+locality+',&nbsp;<em>'+leg+'</em><br><br>';
		return ret;
	},

	getIcon : function(date) {
		var d=date.split('/');
		y=(typeof d[2] != 'undefined') ? parseInt(d[2]) : false;
		if (!y) return 'ico/Circle_Blue.png';
		if (y<1900) return 'ico/Circle_Blue.png';
		if (y<1980) return 'ico/Circle_Orange.png';
		if (y<2006) return 'ico/Circle_Yellow.png';
		return 'ico/Circle_Red.png';
	},

	getZIndex : function(date) {
		var d=date.split('/');
		y=(typeof d[2] != 'undefined') ? parseInt(d[2]) : false;
		if (!y) return 10;
		if (y<1900) return 20;
		if (y<1980) return 30;
		if (y<2006) return 40;
		return 50;
	},

	showUTMgrid : function() {
		Geo.showUTM10Grid(Details.map, "#current-utm");
		$("#btn-utm-grid").val('Skjul UTM-felter');
		document.getElementById('btn-utm-grid').onclick=Details.hideUTMgrid;
	},	

	hideUTMgrid : function() {
		Geo.hideUTM10Grid(Details.map, "#current-utm");
		$("#btn-utm-grid").val('Vis UTM-felter');
		document.getElementById('btn-utm-grid').onclick=Details.showUTMgrid;
	},	

	reset: function() {
		Details.mapLoaded = false
	},

	loadMap : function() {
		Search.wait(true);
		var lat, long, zoom;

		//"standard" center is lat = 56.15; long = 11.65;
		lat = ($("#lat-cnt").val()!=undefined) ? $("#lat-cnt").val() : 56.15;
		long = ($("#long-cnt").val()!=undefined) ? $("#long-cnt").val() : 11.65;
		zoom = (long!=11.65) ? 10 : 7;
	
		var myLatLng = new google.maps.LatLng(lat, long);

		Details.map = Geo.googleMap('map');
		Details.map.setCenter(myLatLng);
		Details.map.setZoom(zoom);

		var rows= $("#result-table tr:gt(0)"); // skip the header row
		var markers = new Array();
		var bounds = new google.maps.LatLngBounds(); 

		rows.each(function(index) {
			var details = $("td:nth-child("+COL_DETAILS+")", this);
			var lat = $(details).find('img').attr('lat');
			var long = $(details).find('img').attr('long');
			var lnr = $(details).find('img').attr('lnr');
			var sindex = lat+long;

			var art = $("td:nth-child("+COL_ART+")", this).text();
			var date = $("td:nth-child("+COL_DATO+")", this).text();
			var locality = $("td:nth-child("+COL_LOKALITET+")", this).text();
			var utm = $("td:nth-child("+COL_UTM+")", this).text();
			var leg = $("td:nth-child("+COL_LEG+")", this).text();
		
			if (Details.stack[sindex]===undefined) {
				Details.stack[sindex]=Details.getHTML(lnr, art, locality, leg, utm, lat, long);
			} else {
				Details.stack[sindex]=Details.stack[sindex]+Details.getHTML(lnr, art, locality, leg, utm, lat, long);
			}
			
			var latlng = new google.maps.LatLng(lat,long);
			bounds.extend(latlng); 

			var Marker = new google.maps.Marker({
				icon : Details.getIcon(date),
				zIndex: Details.getZIndex(date),
				position: latlng,
				map: Details.map
			});

			google.maps.event.addListener(Marker, 'click', function() {
				var html=Details.stack[sindex];
				$("#map-markers").html(html);
			});

			google.maps.event.addListener(Details.map, 'click', function(e) {
				if ($("#fund-popup").is(':visible')) {
					$("#fund-popup").hide();
				}
			});
		});

		//fit bounds, but avoid absolute zoom
		if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
			bounds.extend( new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() + 0.01) );
			bounds.extend( new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() - 0.01) );
		}

		setTimeout(function() {
			Details.map.fitBounds(bounds); 
		}, 100);

		Details.mapLoaded = true;
		Search.wait(false);
	},

	changeView : function(v) {
		$("#fund-popup").hide(); //hide if visible
		if (v==1) { 
			$("#tabel-cnt").removeClass('visuallyhidden');
			$("#kort-cnt").addClass('visuallyhidden');
			$("#tabel-cnt").show();
		} else {
			$("#tabel-cnt").addClass('visuallyhidden');
			$("#kort-cnt").removeClass('visuallyhidden');
			$("#kort-cnt").show(); 
			if (!Details.mapLoaded) Details.loadMap();
		}
		System.adjustPageHeight();
	},

	bindCloseBtn : function() {
		$("#fund-popup-close").click(function() {
			$("#fund-popup").hide();
		});
	},

	setFundPopupMap : function() {
		if ($("#fund-popup").is(':visible')==false) {
			var l=16;
			var t = $("#map").offset().top;
			t=t-130;
			$("#fund-popup").css('left',l+'px');
			$("#fund-popup").css('top',t+'px');
			$("#fund-popup").css('z-index','100');
			$("#fund-popup").css('position','absolute');
			$("#fund-popup").css('width','665px');
			$("#fund-popup").css('height','340px');
			$("#fund-popup").show();
		}
		Details.bindCloseBtn();
	},

	setFundPopupTable : function(y) {
		$("#fund-popup").hide();
		l=70;
		var t=y-200; //last click event mouse Y

		//ie needs the extra scroll offset
		//var scroll=self['pageYOffset'] || document.documentElement.scrollTop;
		//if ($.browser.msie) t=t+scroll;

		$("#fund-popup").css('left',l+'px');
		$("#fund-popup").css('top',t+'px');
		$("#fund-popup").css('z-index','100');
		$("#fund-popup").css('position','absolute');
		$("#fund-popup").css('width','660px');
		$("#fund-popup").css('height','340px');
		$("#fund-popup").show();
		Details.bindCloseBtn();	
	},

	loadDetailsFromTable : function(utm, lat, long, lnr, y) {
		Details.setFundPopupTable(y);
		$("#fund-details").load('ajax/edderkopper_details.php?LNR='+lnr);
		Details.loadDetailsMap(lat, long, utm);
	},

	loadDetails : function(utm, lat, long, lnr) {
		Details.setFundPopupMap();
		$("#fund-details").load('ajax/edderkopper_details.php?LNR='+lnr);
		Details.loadDetailsMap(lat, long, utm);
	},

	loadDetailsMap : function(lat, long, utm) {
		var myLatLng = new google.maps.LatLng(lat, long);
		var mapOptions = {
			zoom: 10,
			center: myLatLng,
			zoomControl: true,
			streetViewControl: false,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL
			},
			mapTypeId: google.maps.MapTypeId.TERRAIN	
		}
		var map = new google.maps.Map(document.getElementById("utm-map"), mapOptions);	

		var utmcoords = eval(UTM_LatLng[utm.toUpperCase()]);
		var poly = new google.maps.Polygon({
			paths: utmcoords,
			strokeColor: "blue",
			strokeOpacity: 0.50,
			strokeWeight: 5,
			fillColor: "transparent"
		});
		poly.setMap(map);

		var Marker = new google.maps.Marker({
			position: new google.maps.LatLng(lat,long),
			map: map
		});

		google.maps.event.addListener(map, 'click', function(event) {
			var doc = document.documentElement, body = document.body;
			var top = (doc && doc.scrollTop  || body && body.scrollTop  || 0);
			top = parseInt(top)-20;
			$("#map-popup").css('z-index','110');
			$("#map-popup").css('left', '100px');
			$("#map-popup").css('top', top+'px');
			$("#map-popup").css('position','absolute');
			$("#map-popup").show();
			popupmap = Geo.googleMap('map-popup-map', lat, long, 11);
			var utmcoords = eval(UTM_LatLng[utm.toUpperCase()]);
			var poly = new google.maps.Polygon({
				paths: utmcoords,
				strokeColor: "blue",
				strokeOpacity: 0.50,
				strokeWeight: 5,
				fillColor: "transparent"
			});
			poly.setMap(popupmap);
			var Marker = new google.maps.Marker({
				position: new google.maps.LatLng(lat,long),
				map: popupmap
			});

			google.maps.event.addListener(popupmap, 'dblclick', function(event) {
				$("#map-popup").hide();
			});
		});
	}

};

