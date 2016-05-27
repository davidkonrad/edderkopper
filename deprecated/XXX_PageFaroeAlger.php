<?

class PageFaroeAlger extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" language="javascript" src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/search.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&sensor=true"></script>
<?
	}

	protected function drawBody() {
?>
<fieldset>
<legend id="content-headline"></legend>
<form name="faroealger" id="faroealger" method="post" action="">
<? $this->drawSessLang();?>
<table>
	<tr>
		<td><label for="scientific"><? trans(LAB_SCIENTIFIC_NAME, true);?></label></td>
		<td><input type="text" name="scientific" id="scientific"/></td>
	</tr>
	<tr>
		<td><label for="taxon"><? trans(LAB_TAXON_GROUP, true);?></label></td>
		<td><? $this->taxonSelect();?></td>
	</tr>
	<tr>
		<td><label for="lok"><? trans(LAB_LOCALITY, true);?></label></td>
		<td><input type="text" name="lok" id="lok"/></td>
	</tr>
		<td><label for="dato-year"><? trans(LAB_DATE, true);?></label></td>
		<td>
			<small class="datoInterval" style="margin-left:0px;"><? trans(LAB_DATE_YEAR, true);?></small></label>
				<input type="text" class="datoInterval" max="4" name="year" style="width:40px;" id="year"/>
			<small class="datoInterval"><? trans(LAB_DATE_MONTH, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="month" style="width:40px;" id="month"/>
			<small class="datoInterval"><? trans(LAB_DATE_DAY, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="day" style="width:40px;" id="day"/>
		</td>
		<td>&nbsp;</td>
	</tr>

	<tr>		
		<td><label for="dato-to-year"><? trans(LAB_DATE_TO, true);?></label></td>
		<td>
			<small class="datoInterval" style="margin-left:0px;"><? trans(LAB_DATE_YEAR, true);?></small></label>
				<input type="text" class="datoInterval" max="4" name="to-year" style="width:40px;" id="to-year"/>
			<small class="datoInterval"><? trans(LAB_DATE_MONTH, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="to-month" style="width:40px;" id="to-month"/>
			<small class="datoInterval"><? trans(LAB_DATE_DAY, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="to-day" style="width:40px;" id="to-day"/>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><label for="leg"><? trans(LAB_COLLECTOR, true);?></label></td>
		<td><input type="text" name="leg" id="leg"/></td>
	</tr>
	<tr>
		<td><label for="det"><? trans(LAB_DETERMINATOR, true);?></label></td>
		<td><input type="text" name="det" id="det"/></td>
	</tr>
	<tr><td colspan="2"><hr class="search"></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="button" value="<? trans(LAB_SEARCH, true);?>" onclick="Search.submit();"/>&nbsp;&nbsp;
		<input type="button" value="<? trans(LAB_RESET, true);?>" onclick="Search.reset();"/></td>
		<td>&nbsp;</td>
	</tr>

</table>
</form>
<div id="search-result" style="float:left;text-align:left;"></div>
</fieldset>
<?
	$this->drawRelatedContent();
	}

	protected function taxonSelect() {
?>
<select name="taxon" id="taxon" size="1">
	<option value="" selected><? trans(LAB_ALL_GROUPS, true);?></option>
	<option value="a">Cyanophyta</option>
	<option value="b">Rhodophyta</option>
	<option value="c">Fucophyceae</option>
	<option value="d">Diatomophyceae</option>
	<option value="e">Tribophyceae</option>
	<option value="f">Chlorophyta</option>
	<option value="g">Charophyceae</option>
	<option value="h">Prasinophyceae</option>
</select>
<?
	}	
   
	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	Search.caption='<? trans(DB_LINK_FAROEALGER, true);?>';
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id='#faroealger';
	Search.submit_load="ajax/faroealger.php";
	Search.init();
});
</script>
<?
	}


}

?>
