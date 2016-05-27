<?

class ClassDigitalisering extends ClassDigitaliseringBase {

	public function __construct() {
		parent::__construct();
	}

	public function extraHead() {
?>
<style type="text/css">
#left {
	float: left;
	clear: left;
	width: 400px;
}
#right {
	margin-left: 20px;
	float: left;
	clear: none;
	width: 400px;
	margin-left: 20px;
}
fieldset {
	height: 300px;
	width: 95%;
}
#control {
	float: left;
	clear: both;
	text-align: center;
	margin-top: 10px;
	width: 800px;
}
#control button {
	font-size: 20px;
	padding: 10px;
	width: 100px;
}
table input, table select {
	padding: 5px;
	font-size: 20px;
}
input.disabled {
	color: gray;
}
img.webcam-image {
	width: 930px;
}
</style>
<?
	}

	public function drawBody() {
?>
<script type="text/javascript" src="digitalisering/webcam.js"></script>
<script type="text/javascript">
webcam.set_api_url('digitalisering/upload.php');
webcam.set_quality(100); // JPEG quality (1 - 100)
webcam.set_shutter_sound(true); // play shutter click sound
</script>
<div id="left">
<fieldset>
<legend>Tekst / etiketter</legend>
<table>
<tr>
	<td>Project</td>
	<td><? echo $this->getProjects();?></td>
</tr>
<tr>
	<td>Indtaster</td>
	<td><input type="text" id="creator" name="creator" class="disabled" readonly="readonly" value="<? echo Login::getUsername();?>"></td>
</tr>
<tr>
	<td>Label</td>
	<td><input type="text" id="label" name="label"></td>
</tr>
<tr>
	<td>Taxon</td>
	<td><input type="text" id="taxon" name="taxon"></td>
</tr>
<tr>
	<td>Billede</td>
	<td><input type="text" id="image" name="image" class="disabled" readonly="readonly"></td>
</tr>
</table>
</fieldset>
</div>
<div id="right">
<fieldset style="clear:right;">
<legend>Webcam</legend>
<script language="JavaScript">
document.write(webcam.get_html(320, 240, 1280, 1024));
</script>
<br><hr class="search">
		<input type=button value="Tag billede" onclick="snap();";>
		&nbsp;&nbsp;
		<input type=button value="Nulstil" onclick="webcam.reset()">
		&nbsp;&nbsp;
		<input type=button value="Konfigurer" onclick="webcam.configure()">
</fieldset>
</div>
<div id="control">
	<button id="new_btn">Ny</button>&nbsp;
	<button id="save_btn" disabled="disabled" onclick="serverRen();">Gem</button>
	<br><br>
	<div id="upload_results" style="background-color:#eee;"></div>
</div>
<? HTML::divider(10);?>
<script type="text/javascript">
webcam.set_hook('onComplete', 'completionHandler');
var sample = '';
var project_id=1;
var saved;
function serverRen() {
	if (saved) return;
	Search.wait(true);
	$.ajax({
		url: 'digitalisering/upload.php?file='+sample+'&project_id='+project_id,
		success: function(html) {
			//alert(html);
			var img=html.replace('sample','');
			$("#image").val(img);
			ajaxSave();
			Search.wait(false);
			alert('Recorden er gemt!');
		}
	});
}
function snap() {
	webcam.snap('digitalisering/upload.php?sample=yes&project_id='+project_id, 'completionHandler');
	enableSave();
}
function ajaxSave() {
	var url='digitalisering/ajax_save.php?';
	url+='project='+$("#project").val();
	url+='&creator='+$("#creator").val();
	url+='&image='+$("#image").val();
	url+='&label='+$("#label").val();
	url+='&taxon='+$("#taxon").val();
	$.ajax({
		url: url,
		success: function(html) {
			//alert(html);
			saved=true;
		}
	});
}
$("#project").change(function() {
	project_id=$("#project").val();
});
$("#new_btn").click(function() {
	saved=false;
	$("#image").val('');
	$("#label").val('');
	document.getElementById('upload_results').innerHTML = '';
	enableSave();
});
function do_upload() {
	document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
	webcam.upload();
}
function completionHandler(msg) {
	//console.log(msg);
	document.getElementById('upload_results').innerHTML = '<img class="webcam-image" src="' + msg + '">';
	//http://stackoverflow.com/questions/857618/javascript-how-to-extract-filename-from-a-file-input-control
	var filename = msg.split(/(\\|\/)/g).pop();
	filename=filename.replace('sample','');
	$("#image").val(filename);
	enableSave();

	if (msg.match(/(http\:\/\/\S+)/)) {
		var image_url = RegExp.$1;
		//console.log('url : '+image_url);
		if (image_url.indexOf('sample.jpg')>0) {
			sample = image_url;
			//console.log('sample');
			document.getElementById('upload_results').innerHTML = '<img class="webcam-image" src="' + image_url + '">';
		} else {
			//console.log('upload');
			document.getElementById('upload_results').innerHTML = 
				'<h1>Billede oploadet til server</h1>' + 
				'<h3>jpeg url : ' + image_url + '</h3>' + 
				'<img src="' + image_url + '">';
		}
		webcam.reset();
	}
	webcam.set_api_url('digitalisering/upload.php');
}
function enableSave() {
	var ok=($("#label").val()!='') && ($("#taxon").val()!='') && ($(".webcam-image").length>0);
	if (ok) {
		$("#save_btn").removeAttr('disabled');
	} else {
		$("#save_btn").attr('disabled','disabled');
	}
}
$("#label,#taxon").on('propertychange keyup input paste change', function(e) {
	enableSave();
});
</script>
<?
	}
}

?>
