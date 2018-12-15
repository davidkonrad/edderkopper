<?

class ClassEdderkopperAdm extends ClassBase {
	public $template = 'TemplateEdderkopper';

	public function __construct() {
		parent::__construct();
		//redirect to frontpage if user not is logged in
		if (!Login::isLoggedIn()) {
			header('Location: '.$this->getIndexPage());
		}
	}

	public function extraHead() {
?>
<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
<link rel="stylesheet" href="css/bootstrap-typeahead.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper-adm.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/utm.js"></script>
<script type="text/javascript" src="js/edderkopper.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.species.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.genus.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.family.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.fund.js"></script>
<script type="text/javascript" src="plugins/tabber/tabber.js"></script>
<link rel="stylesheet" href="plugins/tabber/example.css" type="text/css" media="screen" />
<script type="text/javascript">
	window.CKEDITOR_BASEPATH = 'ckeditor/'
</script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<?
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">

//set autofocus on inputs when active tab changes
$('body').on('click', '.tabbernav li', function(e) {
	var tabName = e.currentTarget.innerText;
	switch (tabName) {
		case 'Art':
			$('#lookup-species').focus();
			break;
		case 'Slægt':
			$('#lookup-genus').focus();
			break;
		case 'Familie':
			$('#lookup-family').focus();
			break;
		case 'Fund':
			$('#fund-lnr').focus();
			break;
		default:
			break;
	}
})


var species;
var species_lookup=[];

$(document).ready(function() {
	$.ajax({
		url : 'ajax/edderkopper_json_data.php?target=species',
		success : function(json) {
			species=json;
			species_lookup = [];
			for (var i=0;i<json.length;i++) {
				species_lookup.push(json[i].value);
			}
			$("#edit-art").typeahead({
				items: 20,
				source: species_lookup,
				updater: function(item) {
					alert(item)
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
</script>

<?
	}

	public function drawBody() {
?>


<div class="tabber">

	<div class="tabbertab">
		<h2>Upload mv.</h2>
		<p>
			<br>
			<button id="download">Download aktuel fund-database som CSV</button>
<? HTML::divider(10);?>

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
			<div id="csv-filelist" style="clear:left;"></div>

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
</p>
</div>

	</div>

	<div class="tabbertab">
		<h2>Tjekliste</h2>
		<p>
<button id="generate-checklist">Generér tjekliste</button>
<a style="margin-left:20px;" href="Tjekliste-over-Danmarks-edderkopper">&#9658;&nbsp;Se tjekliste</a>
<hr class="search">
<button id="update-name">Opdatér &lt;name&gt; på fund</button>
		</p>
	</div>	

	<div class="tabbertab">
		<h2>Fund</h2>
	  <p>
			LNR #<input type="text" id="fund-lnr" class="number-only" style="width:80px;" autofocus/>
			<button id="edit-fund">Rediger Fund</button>
			<button id="create-fund">Opret nyt fund</button>
			<span id="fund-messages" class="messages"></span>
			<button class="save" id="fund-save" disabled>Gem</button>
			<hr>
			<form id="fund-form" spellcheck="false">
				<div id="current-art-cnt" style="float:left;clear:both;"></div>
				<table id="fund-table">
					<tbody id="fund-table-body"></tbody>
				</table>
			</form>
		</p>
	</div>

	<div class="tabbertab">
		<h2>Art</h2>
	  <p>
			Opslag : <input type="text" id="lookup-species" data-provide="typeahead" class="lookup" style="width:380px;" spellcheck="false" />
			<button id="create-species">Opret ny art</button>
			<span id="species-messages" class="messages"></span>
			<button class="save" id="species-save" disabled>Gem</button>
			<hr>
			<form id="species-form" spellcheck="false">
				<table id="species-table">
					<tbody id="species-table-body"></tbody>
				</table>
			</form>
		</p>
	</div>

	<div class="tabbertab">
		<h2>Slægt</h2>
	  <p>
			Opslag : <input type="text" id="lookup-genus" data-provide="typeahead" class="lookup" style="width:380px;" />
			<button id="create-genus">Opret ny slægt</button>
			<span id="genus-messages" class="messages"></span>
			<button class="save" id="genus-save" disabled>Gem</button>
			<hr>
			<form id="genus-form">
				<table id="genus-table">
					<tbody id="genus-table-body"></tbody>
				</table>
			</form>
		</p>
	</div>

	<div class="tabbertab">
		<h2>Familie</h2>
	  <p>
			Opslag : <input type="text" id="lookup-family" data-provide="typeahead" class="lookup" style="width:380px;" />
			<button id="create-family">Opret ny familie</button>
			<span id="family-messages" class="messages"></span>
			<button class="save" id="family-save" disabled>Gem</button>
			<hr>
			<form id="family-form">
				<table id="family-table">
					<tbody id="family-table-body"></tbody>
				</table>
			</form>
		</p>

	</div>

</div>

<?
	}

}

?>
