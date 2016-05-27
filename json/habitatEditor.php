<?
include('../common/Db.php');

class Editor extends Db {
	public $accepted = array();

	public function __construct() {
		parent::__construct();
		$SQL='select * from habitat_kmlid_navne';
		$result=$this->query($SQL);
		$a=array();
		while ($row = mysql_fetch_assoc($result)) {
			$a[$row['id']]=$row['navn'];
		}
		//$this->debug($a);
		$this->accepted=$a;
	}
}

$editor=new Editor();
?>
		

<!doctype html>
<html>
<head>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/keydragzoom/src/keydragzoom.js?"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerwithlabel/src/markerwithlabel.js"></script>
<script type="text/javascript" src="http://daim.snm.ku.dk/js/geo.js"></script>
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
#right {
	width: 550px;
	height: 700px;
	float: left;
	clear: none;
	background-color: #ebebeb;
	padding-left: 20px;
}
#right button {
	padding: 5px;
	font-size: 18px;
}
</style>
</head>
<body>

<div id="map"></div>
<div id="right">
	<br><br>
	<table>
	<tr><td>ID</td><td>:</td><td id="item_id"></td></tr>
	<tr><td>EF-habitat-id</td><td>:</td><td><input type="text" id="ef_habitat_id" name="ef_habitat_id"></td></tr>
	<tr><td>Nuv. habitat&nbsp;&nbsp;</td><td>:</td><td id="item_habitat"></td></tr>
	<tr><td>Godkendt</td><td>:</td><td id="item_godkendt"></td></tr>
	</table>
	<br>
	S&aelig;t ny habitat<br>
	<select id="habitat"></select>
	<br>
	<button id="godkend">Godkend</button>
	<br><br><br><br><br><br><br>
	Steder der endnu ikke er knyttet id/polygon til :<br>
	<span id="noid"></span>
</div>

<script type="text/javascript">

var accepted = [
<?
/*
echo '<pre>';
print_r($editor->accepted);
echo '<pre>';
*/
$js='';
foreach ($editor->accepted as $field=>$value) {
	if ($js!='') $js.=',';
	$js.='{ id : '.$field.', ';
	$js.=  'habitat : "'.$value.'"}';
}
echo $js;
?>
];

$(document).ready(function() {
	var a=[];
	$.getJSON('habitatnavne.json', function(json) {
		$.each(json, function(index, navn) {
			a.push(navn.navn);
		});
		a.sort();
		for (var i=0;i<a.length;i++) {
			$('#habitat').append('<option value="'+a[i]+'">'+a[i]+'</option>');		
		}
	});
});

var map;
$(document).ready(function() {
	map = Geo.googleMap('map', Geo.DK_Lat, Geo.DK_Long, 7);
});

function showPolygonSimple(polygon, map, obj) {
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
		obj: obj,
		strokeWeight: Geo.strokeWeight,
		strokeColor: Geo.strokeColor,
		fillColor: Geo.fillColor 
	});

	google.maps.event.addListener(polygon, 'click', function() {
		$("#item_id").text(polygon.obj.count);
		$("#ef_habitat_id").val(polygon.obj.ef_habitat_id);
		$("#item_habitat").text(polygon.obj.habitat);
		$("#item_godkendt").text(polygon.obj.godkendt);
	});

	//add the polygonborder
	polygon.setMap(map);
}

function isGodkendt(id) {
	for (var i=0;i<accepted.length;i++) {
		if (accepted[i].id==id) {
			return accepted[i];
		}
	}
	return false;
}

$(document).ready(function() {
	Geo.fillColor = 'red';
	Geo.strokeWeight = 1;
	Geo.strokeColor = 'red';

	var count=1;

	$.getJSON("habitatpolygoner.json", function(json) {
		$.each(json, function(index, habitat) {
			//console.log(count, habitat);
			var polystr = habitat.coords;
			var polyarray = polystr.split('/');
			var obj = new Object();
			obj.count=count;
			obj.habitat=habitat.name;
			obj.godkendt='Nej';

			var accepted=isGodkendt(count);
			if (accepted) {
				Geo.fillColor = 'navy';
				Geo.strokeColor = 'navy';
				Geo.strokeWeight = 1;
				obj.habitat=accepted.habitat;
				obj.godkendt='Ja';
			} else {
				Geo.strokeColor = 'red';
				Geo.fillColor = 'red';
				Geo.strokeWeight = 1;
			}
				
			showPolygonSimple(polyarray, map, obj);
			count=count+1;
		});
	});
});

$(document).ready(function() {
	$("#godkend").click(function() {
		var id=$("#item_id").text()!='' ? $("#item_id").text() : false;
		var habitat=$("#habitat").val()!='' ? $("#habitat").val() : false;
		var ef_habitat_id=$("#ef_habitat_id").val()!='' ? $("#ef_habitat_id").val() : false;
		window.location.href="habitatEditorUpdate.php?id="+id+'&habitat='+habitat+'&ef_habitat_id='+ef_habitat_id;
	});
});		

$(document).ready(function() {
	$.ajax({
		url : 'habitatEditorLists.php?get=noid',
		success : function(html) {
			$('#noid').html(html);
		}
	});
});		

</script>

</body>
</html>
