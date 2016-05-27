<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true"></script>
<script>
var map;

function formatPolygonData(polygon) {
	var pa = [];
	//
	var coordinates= polygon[0];
	for (var index = 0; index < coordinates.length; index++) {
		if (index % 20 == 0)  {
			pa.push(coordinates[index][1]+'/'+coordinates[index][0]);
		}
	}
	return pa;	
}

function showKommuneGraense(polygon) {
		var lat, lng;
		var paths;
		var coordinates= polygon[0];
		var latlng;
		var bounds = new google.maps.LatLngBounds();
		var vertices = new Array(coordinates.length);
		for (var index = 0; index < coordinates.length; index++) {
			if (index%20 == 0) {
				lat = parseFloat(coordinates[index][1]);
				lng = parseFloat(coordinates[index][0]);
				latlng = new google.maps.LatLng(lat, lng);
				vertices[index] = latlng;
				bounds.extend(latlng); 
			}
		}

        var gr = new google.maps.Polygon({
          paths: vertices,
          strokeColor: "#343434",
          strokeOpacity: 0.2,
          strokeWeight: 2,
          fillColor: '#'+(Math.random()*0xFFFFFF<<0).toString(16),
          fillOpacity: 0.25
        });

        //map.fitBounds(bounds);
        gr.setMap(map);

			//add each 20 latlong as invisible marker 
			/*
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
			*/
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

		/*
		var graense = new google.maps.Polygon({
			paths: paths,
			strokeOpacity: 0.8,
			strokeWeight: 2,
			strokeColor: '#FFF380',
			fillColor: 'blue' 
		});

		map.fitBounds(bounds); 
		graense.setMap(map);
		*/
}

function getGraense(nr) {
	var type="kommuner";
	var url= "../proxy.php?url=http://geo.oiorest.dk/"+type+"/"+nr+"/graense.json"; 			

	$.ajax({
		url: url,
		dataType: 'json',
		success:  function (data) {
				for (var index = 0; index<data.coordinates.length; index++) {
					showKommuneGraense(data.coordinates[index], type);
				}
			//showKommuneGraense(data);
			/*
			for (var index = 0; index<data.coordinates.length; index++) {
				//Edderkopper.showKommuneGraense(data.coordinates[index], type);
				//var s=formatPolygonData(data.coordinates[index]);
				//$("#kommune").append(s+'<br>');
				showKommuneGraense(data);
			}
			*/
		},
		error: function (xhr, ajaxOptions, thrownError){
				alert(xhr+' '+ajaxOptions+' '+thrownError);										
		}   
	});
}

function g2(nr) {
	//alert(nr);
	var type="kommuner";
	var url= "../proxy.php?url=http://geo.oiorest.dk/"+type+"/"+nr+"/graense.json"; 			
	//alert(url);

	$.ajax({
		url: url,
		dataType: 'json',
		success:  function (data) {
		var coordinates=data.coordinates[0];
		//alert(coordinates[0]);
        var options;
        var latlng;
        var bounds = new google.maps.LatLngBounds();
        var vertices = new Array(coordinates.length);
        for (var index = 0; index < coordinates.length; index++) {
          lat = parseFloat(coordinates[index].lat);
			alert(lat);
          lng = parseFloat(coordinates[index].lon);
          latlng = new google.maps.LatLng(lat, lng);
          vertices[index] = latlng;
          bounds.extend(latlng);
        }

        var gr = new google.maps.Polygon({
          paths: vertices,
          strokeColor: "#343434",
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: "#ebebeb",
          fillOpacity: 0.25
        });

        map.fitBounds(bounds);
        gr.setMap(map);
	}
     });
    }

function getKommuner() {
	var url = "../proxy.php?url=http://geo.oiorest.dk/kommuner.json";
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
					$("#kommune").append(ka[i].navn+'<br>');
					getGraense(ka[i].nr);
					//g2(ka[i].nr);
				}
			}
		}
	});
};

$(document).ready(function() {
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
		map = new google.maps.Map(document.getElementById("map"), mapOptions);	
		getKommuner();
});

</script>
</head>
<body>

<div id="map" style="width:685px;height:565px;float:left;clear:none;"></div>

<span id="kommune"></span>


</body>
</html>
