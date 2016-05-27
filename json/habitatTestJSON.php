<?
include('../common/Db.php');

class Habitat extends Db {
	public $id='';
	public $navn='';
	public function __construct() {
		parent::__construct();
		 $this->get();
	}
	private function get() {
		$SQL='select navn, id from habitat_kmlid_navne order by id';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			if ($this->id!='') $this->id.=',';
			if ($this->navn!='') $this->navn.=',';
			$this->id.=$row['id'];
			$this->navn.='"'.$row['navn'].'"';
		}
	}
	public function createJSON() {
		$polygoner = file_get_contents("habitatpolygoner.json");
		$polygoner = json_decode($polygoner, true);
		//$this->debug($polygoner);
		
		$JSON='';
		$SQL='select id, navn from habitat_kmlid_navne order by id';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			if ($JSON!='') $JSON.=',<br>';
			$polygon=$polygoner[$row['id']-1]['coords'];
			$JSON.=' { "id" : "'.$row['id'].'", "navn" : "'.$row['navn'].'", "coords" : "'.$polygon.'" } ';
		}
		echo '{ "habitater" : [ '.$JSON.' ] }';

	}
}
$habitat=new Habitat();
//$habitat->createJSON();
?>	
<!doctype html>
<html>
<head>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/keydragzoom/src/keydragzoom.js?"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerwithlabel/src/markerwithlabel.js"></script>
<!--
<script type="text/javascript" src="http://daim.snm.ku.dk/js/geo.js"></script>
-->
<script type="text/javascript" src="http://localhost/samlinger/js/geo.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
<style type="text/css">
#map {
	width: 900px;
	height: 700px;
	float: left;
	clear: none;
}
</style>
</head>

<body>

<div id="map"></div>
<select id="select"></select>

<script type="text/javascript">
var ider = [<? echo $habitat->id;?>];
var navne = [<? echo $habitat->navn;?>];

var map; //, json; //, habitater;

$(document).ready(function() {
	map = Geo.googleMap('map', Geo.DK_Lat, Geo.DK_Long, 7);
	console.log(map);
});
</script>

<script type="text/javascript">
/*
$.getJSON('habitater_10062013.json', function(data) {
	json=data;
});
for (var i=0;i<ider.length;i++) {
	$("#select").append('<option value="'+ider[i]+'">('+ider[i]+') '+navne[i]+'</option>');
}
*/

/*
var Habitater = {
	json : null,	

	init : function() {
		$.getJSON('habitater_27062013.json', function(json) {
			Habitater.json=json;
			console.log('load', Habitater.json);
		});
	},


	sortedList : function() {
		var names=[];
		for (var i=0;i<Habitater.json.habitater.length;i++) {
			var item=Habitater.json.habitater[i];
			if (names.indexOf(item.navn)<0) names.push(item.navn);
		}
		return names.sort();
	},
		
	populate : function(element) {
		var list=this.sortedList();
		for (var i=0;i<list.length;i++) {
			$(element).append('<option value="'+list[i]+'">'+list[i]+'</option>');
		}
	},

	nameToPolygons : function(name) {
		var result=[];
		for (var i=0;i<this.json.habitater.length;i++) {
			if (this.json.habitater[i].navn==name) {
				result.push(this.json.habitater[i].coords);
			}
		}
		return result;
	}

};

*/

Geo.Habitater.init();

setTimeout(function(){ 
	Geo.Habitater.populate("#select") 
	}, 700 
);


</script>

<script type="text/javascript">
function showPolygonSimple(polygon, map) {
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

	var polygon = new google.maps.Polygon({
		paths: vertices,
		strokeOpacity: 0.8,
		//obj: obj,
		strokeWeight: Geo.strokeWeight,
		strokeColor: Geo.strokeColor,
		fillColor: Geo.fillColor 
	});

	//add the polygonborder
	polygon.setMap(map);
}

Geo.fillColor = 'red';
Geo.strokeWeight = 1;
Geo.strokeColor = 'red';

$("#select").change(function() {
	var name=$("#select").val();
	console.log(name);

	var polygons=Geo.Habitater.nameToPolygons(name);
	console.log(polygons);

	for (var i=0;i<polygons.length;i++) {
		var polyarray = polygons[i].split('/');
		showPolygonSimple(polyarray, map);
		return false;
	}

	/*
	//console.log(json);
	for (var i=0;i<json[0].habitater.length;i++) {
		var item=json[0].habitater[i];
		if (id==item.id) {
			console.log(item);
			var polyarray = item.coords.split('/');
			showPolygonSimple(polyarray, map);
			return false;
		}
	}
	*/
});
</script>

</body>
</html>
