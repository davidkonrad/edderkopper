var map, 
	poly,
	markers = [], 
	path = new google.maps.MVCArray;

var options = { 
	strokeColor: '#571B7E', 
	strokeWeight: 2,
	zIndex: 99
}

function addPoint(event) {
	path.insertAt(path.length, event.latLng);

	var marker = new google.maps.Marker({
		icon : 'ico/csquare.png',
		position: event.latLng,
		map: map,
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

	if (markers.length>=3) checkPolygon();
}

function checkPolygon() {
	for (var i=0;i<Geo.UTM10_Grid.length;i++) {
		var utm=Geo.UTM10_Grid[i].getPath();
		var inPolygon=false;
		for (var u=0;u<utm.length;u++) {
			if ((google.maps.geometry.poly.containsLocation(utm.getAt(u), poly)) ||
				(google.maps.geometry.poly.isLocationOnEdge(utm.getAt(u), poly, 0.001))) {
				inPolygon=true;
			}
		}
		if (inPolygon) {
			//override default options set by Geo
			Geo.UTM10_Grid[i].setOptions(options);
			Geo.UTM10_Grid[i].setMap(map);
		} else {
			Geo.UTM10_Grid[i].setMap(null);
		}
	}
}

function resetPolygon() {
	for (var i = 0; i < markers.length; i++ ) {
		markers[i].setMap(null);
	}
	for (i=0;i<=path.getLength();i++){
		path.removeAt(i);
		path.clear();
		path.pop();
	}
	markers=[];
}

function zoomToPolygon(poly) { 
	var bounds = new google.maps.LatLngBounds; 
	poly.getPath().forEach(function(latLng) { 
	    bounds.extend(latLng); 
	}); 
	map.fitBounds(bounds) 
} 

function checkGruppe(gruppe) {
	for (var i=0;i<Geo.UTM10_Grid.length;i++) {
		var utm=Geo.UTM10_Grid[i].utm;
		if (utm.indexOf(gruppe)>-1) {
			Geo.UTM10_Grid[i].setOptions(options);
			Geo.UTM10_Grid[i].setMap(map);
		} else {
			Geo.UTM10_Grid[i].setMap(null);
		}
	}
}

function checkRegion(region) {
	var url='ajax/UTM10x10.php?Region='+region;
	$.ajax({
		url: url,
		dataType: 'json',
		success : function(json) {
			checkJSON(json);
		}
	});
}

function checkEgn(egn) {
	var url='ajax/UTM10x10.php?Egn='+egn;
	$.ajax({
		url: url,
		dataType: 'json',
		success : function(json) {
			checkJSON(json);
		}
	});
}

function checkJSON(json) {
	for (var i=0;i<Geo.UTM10_Grid.length;i++) {
		var utm=Geo.UTM10_Grid[i].utm;
		if (json.utm.indexOf(utm)>-1) {
			Geo.UTM10_Grid[i].setOptions(options);
			Geo.UTM10_Grid[i].setMap(map);
		} else {
			Geo.UTM10_Grid[i].setMap(null);
		}
	}
}
			
function UTMFeltnavn() {
	if ($("#Name").val().length==4) {
		var utm=$("#Name").val().toUpperCase();
		if (Geo.UTM10_Grid[utm]=='undefined') return;
		for (var i=0;i<Geo.UTM10_Grid.length;i++) {
			if (Geo.UTM10_Grid[i].utm==utm) {
				Geo.UTM10_Grid[i].setOptions(options);
				Geo.UTM10_Grid[i].setMap(map);
				zoomToPolygon(Geo.UTM10_Grid[i]);			
			} else {
				Geo.UTM10_Grid[i].setMap(null);
			}
		}
	}
}

$(document).ready(function() {
	map = Geo.googleMap('map');
	poly = new google.maps.Polygon({
		strokeWeight: 1,
		strokeColor: 'red', 
		fillColor: 'red' 
	});
	poly.setMap(map);
	poly.setPaths(new google.maps.MVCArray([path]));
	google.maps.event.addListener(map, 'click', addPoint);
	Geo.createUTM10Grid(map);
});

function convertPoint(latLng) { 
	var topRight=map.getProjection().fromLatLngToPoint(map.getBounds().getNorthEast()); 
	var bottomLeft=map.getProjection().fromLatLngToPoint(map.getBounds().getSouthWest()); 
	var scale=Math.pow(2,map.getZoom()); 
	var worldPoint=map.getProjection().fromLatLngToPoint(latLng); 
	return new google.maps.Point((worldPoint.x-bottomLeft.x)*scale,(worldPoint.y-topRight.y)*scale); 
} 

$(document).ready(function() {
	for (var i=0;i<Geo.UTM10_Grid.length;i++) {
		google.maps.event.addListener(Geo.UTM10_Grid[i], 'mouseover', function(args) {
			var pos=convertPoint(args.latLng);
			var top=$("#map").position().top+pos.y;
			$("#utm").css('left',pos.x+'px');
			$("#utm").css('top',top+'px');
			$("#utm").text(this.utm);
			$("#utm").show();
		});
		google.maps.event.addListener(Geo.UTM10_Grid[i], 'mouseout', function() {
			$("#utm").hide();
		});
	}
});

