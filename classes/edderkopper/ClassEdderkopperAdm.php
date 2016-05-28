<?

class ClassEdderkopperAdm extends ClassBase {
	public $template = 'TemplateEdderkopper';

	public function __construct() {
		parent::__construct();
	}

	public function extraHead() {
?>
<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
<link rel="stylesheet" href="css/bootstrap-typeahead.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper-adm.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/edderkopper.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.js"></script>
<script type="text/javascript" src="plugins/tabber/tabber.js"></script>
<link rel="stylesheet" href="plugins/tabber/example.css" type="text/css" media="screen" />
<?
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
var species;
var species_lookup=[];

$(document).ready(function() {
	$.ajax({
		url : 'ajax/edderkopper_json_data.php?target=species',
		success : function(json) {
			species=json;
			species_lookup = [];
			for (var i=0;i<json.length;i++) {
				//species_lookup.push(json[i].value+' ('+json[i].id+')');
				species_lookup.push(json[i].value);
			}
			$("#edit-art").typeahead({
				items : 20,
				source: species_lookup,
				updater : function(item) {
					return item;
				}
			});
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert('error : '+jqXHR.responseText+' '+textStatus+' '+errorThrown);
		}
	});

	$("#edit-fund").on('change propertychange keyup', function() {
		if ($(this).val()!='') {
			$("#edit-fund-btn").removeAttr('disabled');
		} else {
			$("#edit-fund-btn").attr('disabled');
		}
	});

	$("#messages").dialog({
		width: 500
	});
});
/*
var path='ajax/edderkopper_lookup.php';
$("#edit-art").typeahead({
	items : 20,
	source: function (query, process) {
		return $.get(path+'?target=species&lookup='+query, {}, function (data) {
			console.log(data[0].length);
			console.log(data);
			var json='';
			for (var i=0;i<data.length;i++) {
				if (json!='') json+=',';
				json+='"'+data[i].value+'"';
			}
			console.log(json);
			return process(json);
		});
	}
});
*/
/*
$("#edit-family").combobox({ source: "ajax/edderkopper_lookup.php" });
$("#edit-genus").combobox({ source: "ajax/edderkopper_lookup.php" });
$("#edit-art").combobox({ source: "ajax/edderkopper_lookup.php" });
*/
</script>
<?
	}

	public function drawBody() {
?>



<!-------------->

<div class="tabber">

	<div class="tabbertab">
		<h2>Upload mv.</h2>

<!--
<fieldset>
<legend>Download</legend>
-->
<br>
<button id="download">Download aktuel database som CSV</button>
<!--
</fieldset>
-->
<? HTML::divider(10);?>

<!--
<fieldset>
<legend>Upload CSV</legend>
-->
<h4>Upload CSV</h4>
<p style="margin-top:0px;">Upload en ny CSV-fil. Filen vil blive lagt på serveren i et særligt katalog.
Når filen er successfuldt uploaded vil den kunne blive importeret til databasen..
</p>
<form action="ajax/edderkopper/upload.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="return-to" value="../../edderkopper-administration">
<input type="file" name="file" id="upload-file" onchange="adm.setUploadBtn();"/> 
<input type="submit" id="upload-begin" value="Upload valgt CSV-fil"/>
</form>
<hr class="search">
<div id="csv-filelist" style="clear:left;float:left;"></div>

<!--
</fieldset>
-->

<!-- messages window -->
<div XXid="messages" title="Status på serveren">
<?
$f=$this->getParam('f');
$m=$this->getParam('m');
if ($f && $m) {
	$msg=$m=='y' 
		? $f.' er blevet uploadet'
		: 'Upload af '.$f.' mislykkedes';
	echo $msg;
}	
?>		
</div>

<!--
<fieldset style="clear:both;">
<legend>Diverse</legend>
-->
<h4>Tjekliste</h4>
<button id="generate-checklist">Generér tjekliste</button>
<a style="margin-left:20px;" href="Tjekliste-over-Danmarks-edderkopper">&#9658;&nbsp;Se tjekliste</a>
<hr class="search">
<button id="update-name">Opdatér &lt;name&gt; på fund</button>

<!--
</fieldset>
-->

	</div>

	<div class="tabbertab">
		<h2>Fund</h2>
	  <p>

LNR : <input type="text" id="fund-lnr" data-provide="typeahead" class="number-only" style="width:80px;">
<button id="edit-fund">Rediger Fund</button>
<button id="create-fund">Opret nyt fund</button>

		</p>
	</div>

	<div class="tabbertab">
		<h2>Art</h2>
	  <p>
<?
include('_art.inc.php');
?>
		</p>
	</div>

	<div class="tabbertab">
		<h2>Slægt</h2>
	  <p></p>
	</div>

	<div class="tabbertab">
		<h2>Familie</h2>
	  <p></p>
	</div>

</div>

<?
	}

}

?>
