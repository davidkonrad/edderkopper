<!doctype html>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://localhost/samlinger/plugins/jquery.xml2json.js"></script>
</head>
<body>

<script type="text/javascript">
$(document).ready(function() {
/*
	$.get("habitat.kml", function(data) {
		var xml = $(data);
		var kml = $.parseXML(xml);
		alert(kml);
	});
*/

	$.ajax({
		url: "habitat.kml",
		dataType: "xml",
		type: "GET",
		success: function(data){
			console.log(data);
			var json = $.xml2json(data);
			console.log(json);
			//alert(json.Folder);

			alert(json.Folder.Placemark.length);
			for (var i=0;i<json.Folder.Placemark.length;i++) {
				alert(json.Folder.Placemark[i]);
			}
			alert(xml);
			var kml=$.parseXML(xml);
			alert(kml);
			var placemarks = $(kml).find('placemark');
			placemarks.each(function(){ 
				$this=$(this);
				alert($this);
				alert($this.find('name'));
			});
		}
	});
});
</script>

</body>
</html>



