<?

//debug
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', '1');

class ClassEdderkopper extends ClassBase {
	private $years;
	public $template = 'TemplateEdderkopper';

	public function __construct() {
		parent::__construct();

		$SQL='select min(Year_last) as minyear, max(Year_last) as maxyear from edderkopper where Year_last>1000';
		$this->years=$this->getRow($SQL);
	}

	private function regionSelect() {
		$d=array('SJ','EJ','WJ','NWJ','NEJ','F','LFM','SZ','NWZ','NEZ','B');
		echo '<select id="region" onchange="showZootopo();" data-placeholder="'.trans(LAB_REGION_SELECT, false).'" tabindex="1" name="region" class="mw">';
		echo '<option value="">'.trans(LAB_REGION_SELECT).'</option>';
		foreach($d as $t) {
			echo '<option value="'.$t.'">'.$t.'</option>';
		}
		echo '</select>';
	}

	public function drawBeforeFooter() {
		include('ajax/edderkopper_popup.php');
?>
<script type="text/javascript">
function showZootopo() {
	var region = $("#region option:selected").text();
	Geo.Regioner.showRegion(region, polygonMap);
}
$(document).ready(function() {
	$("#familie").combobox({ source: "ajax/edderkopper_lookup.php" });
	$("#genus").combobox({ source: "ajax/edderkopper_lookup.php" });
	$("#species").combobox({ source: "ajax/edderkopper_lookup.php" });

	var map = Edderkopper.initPolygonMap();
	Edderkopper.initKommuner();

	$.getJSON("json/leg.json", function(json) {
		$("#leg").typeahead({
			source : json.lookup,
			items : 12
		});
	});

	Geo.Habitater.populateSimple("#habitat");
	
	$("#habitat").change(function() {
		var name=$("#habitat option:selected").text();
		if (name!='') {
			Geo.Habitater.showHabitat(name, polygonMap);
			$("#hidden-habitat").val(name);
		}
	});	
});
$(document).ready(function() {
	var searchItem = new SearchItem('#edderkopper');

	searchItem.caption="<? echo $this->info['anchor_caption'];?>";
	searchItem.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	searchItem.form_id='#edderkopper';
	searchItem.result_id='#search-result';
	searchItem.submit_load="ajax/edderkopper.php";
	searchItem.mandatory_criterias=false;//true;
	searchItem.headline_id="#edderkopper-content-headline";
	searchItem.result_table="#result-table";
	searchItem.markers=markers; //in edderkopper.js

	searchItem.lookupFields = ['familie','genus','species'];
	searchItem.lookupValues = ['','',''];

	Search.addItem(searchItem);
	Search.init(searchItem);
});
</script>
<?
	}

	public function extraHead() {
		if ($_SESSION['LANG']==1) {
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true&language=da&v=3.33"></script>
<?
		} else {
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true&language=en&v=3.33"></script>
<?
		}
?>
<script type="text/javascript" src="https://rawgithub.com/nmccready/google-maps-utility-library-v3-keydragzoom/master/dist/keydragzoom.js"></script>
<link rel="stylesheet" href="css/ui.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper_popup.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
<link rel="stylesheet" href="css/bootstrap-typeahead.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/edderkopper.js?ver=1"></script>
<script type="text/javascript" src="js/edderkopper_details.js?ver=2"></script>
<script type="text/javascript" src="js/utm.js"></script>
<script type="text/javascript" src="js/geo.js?id=188823"></script>
<script type="text/javascript" src="js/zootopo.js"></script>
<script type="text/javascript">
</script>
<style>
.mw {
	max-width: 248px;
	width: 248px;
}
.ui-combobox {
	font-size: 13px;
}
.ui-combobox-input {
	border: 1px solid #dadada;
}
</style>
<?
	}

	public function drawBody() {
		parent::drawBody();
?>
<fieldset style="background-color:white;" id="edderkopper-main">
<legend id="edderkopper-content-headline"></legend>
<form name="edderkopper" id="edderkopper" method="post" action="">
<input type="hidden" name="hidden-kommune" id="hidden-kommune"/>
<input type="hidden" name="hidden-det" id="hidden-det"/>
<? 
$this->drawSessLang();
$this->drawLoggedIn();
?>
<table style="width:420px;float:left;clear:none;" class="unstyled">
	<tr>
		<td><label for="taxon" class="unstyled">Taxon</label></td>
		<td><input type="text" name="taxon" id="taxon" class="unstyled mw"/></td>
	</tr>
	<tr>
		<td><label for="familie"><? trans(LAB_FAMILY, true);?></label></td>
		<td><input type="text" name="familie" id="familie"/></td>
	</tr>
	<tr>
		<td><label for="genus"><? trans(LAB_GENUS, true);?></label></td>
		<td><input type="text" name="genus" id="genus"/></td>
	</tr>
	<tr>
		<td><label for="species"><? trans(LAB_SPECIES, true);?></label></td>
		<td><input type="text" name="species" id="species"/></td>
	</tr>
	<tr>
		<td colspan="2"><hr class="pale"></td>
	</tr>
	<tr>
		<td style="width:120px;"><label for="region">Zool. region</label></td>
		<td><? $this->regionSelect();?></td>
	</tr>
	<tr>
		<td><label for="utm">UTM10</label></td>
		<td><input type="text" name="utm" id="utm" style="width:80px;"/></td>
	</tr>
	<tr>
		<td><label for="locality"><? trans(LAB_LOCALITY, true);?></label></td>
		<td><input type="text" name="locality" id="locality" class="mw"></td>
	</tr>
	<tr>
		<td><label for="kommune"><? trans(LAB_COUNTY, true);?></label></td>
		<td><select name="kommune" id="kommune" data-placeholder="[<?trans(LAB_COUNTY_SELECT, true);?>]" class="mw" >
			<option value="">[<?trans(LAB_COUNTY_SELECT, true);?>]</option>
			</select>
		</td>
	</tr>

<!-- -->
	<tr>
		<td><label for="habitat"><? trans(LAB_EU_HABITAT_AREA, true);?></label></td>
		<td>
			<input type="hidden" name="hidden-habitat" id="hidden-habitat">
			<select id="habitat" class="mw"><option value="">[<? trans(LAB_EU_HABITAT_AREA_SELECT, true);?>]</option></select>
		</td>
	</tr>
<!-- -->

	<tr>
		<td colspan="2"><hr class="pale"></td>
	</tr>
	<tr>
		<td><label for="redlisted"><? trans(LAB_REDLIST_ONLY, true);?></label></td>
		<td>
			<input type="checkbox" name="redlisted" id="redlisted">
			<label for="redlisted" style="float:none;font-size:10px;top:-3px;position:relative;">(<? trans(LAB_REDLIST_CATEGORIES, true);?>)</label>
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr class="pale"></td>
	</tr>
	<tr>
		<td><label for="leg"><? trans(LAB_COLLECTOR, true);?></label></td>
		<td><input type="text" name="leg" id="leg" class="mw"/></td>
	</tr>
	<tr>
		<td colspan="2"><hr class="pale"></td>
	</tr>

	<tr>
		<td><label for="day"><? trans(LAB_DATE_DAY, true);?></label></td>
		<td>
			<input type="text" name="day" id="day" class="datoInterval"  value="" style="width:50px;"/>
			<span class="date-space">
			-
			</span>
			<input type="text" name="to-day" id="to-day" class="datoInterval" value="" style="width:50px;"/>
		</td>
	</tr>

	<tr>
		<td><label for="month"><? trans(LAB_DATE_MONTH, true);?></label></td>
		<td>
			<input type="text" name="month" id="month" class="datoInterval"  value="" style="width:50px;"/>
			<span class="date-space">
			-
			</span>
			<input type="text" name="to-month" id="to-month" class="datoInterval" value="" style="width:50px;"/>
		</td>
	</tr>

	<tr>
		<td><label for="year"><? trans(LAB_DATE_YEAR, true);?></label></td>
		<td>
			<input type="text" name="year" id="year" class="datoInterval"  value="" style="width:50px;"/>
			<span class="date-space">
			-
			</span>
			<input type="text" name="to-year" id="to-year" class="datoInterval" value="" style="width:50px;"/>
		</td>
	</tr>
	<tr><td colspan="2"><hr class="pale"></td></tr>
<? $this->formButtons('#edderkopper', false); ?>
</table>
<div id="polygon-map" style="width:460px;height:510px;float:left;clear:none;margin-left:10px;border:1px solid #dadada;"></div>
<div style="float:right;margin-right:3px;">
<span id="polygon-map-utm" style="margin-right:10px;font-weight:bold;"></span>
<input type="button" id="btn-polygon-map-utm" value="<? trans(LAB_UTM_SHOW_SQUARES, true);?>" style="font-size:11px;margin:0px;padding:3px;" onclick="Edderkopper.showUTM();">
</div>
</form>
<div id="search-result" style="float:left;text-align:left;"></div>
<div id="edit-record" style="float:left;text-align:left;display:none;"></div>
</fieldset>
<?
	}

}


?>
