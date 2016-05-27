<?

class ClassFishes extends ClassBase {

	private function collectionSelect() {
?>
<select name="fishes-collection" id="fishes-collection" data-placeholder="[<? trans(LAB_COLLECTIONS_ALL, true);?>]" class="chzn-select">
<!--
	<option value="" selected><? trans(LAB_COLLECTIONS_ALL, true);?></option>
-->
	<option value=""></option>
	<option value="*">*</option>
	<option value="P">P</option>
	<option value="1">1</option>
	<option value=" ">(empty)</option>
</select>
<?
	}	
   
	public function drawBeforeFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	Search.caption='<? trans(DB_LINK_FISK_MYC, true);?>';
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id='#fisk';
	Search.result_id='#search-result';
	Search.submit_load="ajax/fiskmyc.php";
	Search.headline_id="#content-headline";
	Search.init();
});
</script>
<?
	}

	public function drawBody() {
?>
<fieldset>
<legend id="content-headline"></legend>
<form name="fisk" id="fisk" method="post" action="">
<? $this->drawSessLang();?>
<table style="width:100%;">
	<tr>
		<td style="width:170px;"><label for="fishes-collection"><? trans(LAB_COLLECTION, true);?></label></td>
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
<? $this->formButtons(); ?>
</table>
</form>
<div id="search-result" style="float:left;text-align:left;"></div>
</fieldset>
<?
	}

}


?>
