<?

class PageFiskMYC extends TemplateSimple {

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
<form name="fisk" id="fisk" method="post" action="">
<? $this->drawSessLang();?>
<table>
	<tr>
		<td><label for="collection"><? trans(LAB_COLLECTION, true);?></label></td>
		<td><? $this->collectionSelect();?></td>
	</tr>
	<tr>
		<td><label for="family"><? trans(LAB_FAMILY, true);?></label></td>
		<td><input type="text" name="family" id="family"/></td>
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
		<td><label for="locality"><? trans(LAB_LOCALITY, true);?></label></td>
		<td><input type="text" name="locality" id="locality"/></td>
	</tr>
	<tr>
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
	}

	protected function collectionSelect() {
?>
<select name="collection" id="collection" size="1">
	<option value="" selected><? trans(LAB_COLLECTIONS_ALL, true);?></option>
	<option value="*">*</option>
	<option value="P">P</option>
	<option value="1">1</option>
	<option value=" ">(empty)</option>
</select>
<?
	}	
   
	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	Search.caption='<? trans(DB_LINK_FISK_MYC, true);?>';
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id='#fisk';
	Search.submit_load="ajax/fiskmyc.php";
	Search.init();
});
</script>
<?
	}


}

?>
