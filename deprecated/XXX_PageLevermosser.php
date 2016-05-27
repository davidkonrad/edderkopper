<?

class PageLevermosser extends TemplateSimple {

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
<legend id="content-headline">Levermosser database</legend>
<form name="levermosser" id="levermosser" method="post" action="">
<table>
	<tr>
		<td><label for="gen"><? trans(LAB_GENUS, true); ?></label></td>
		<td><input type="text" name="gen" id="gen"/></td>
	</tr>
	<tr>
		<td><label for="spec"><? trans(LAB_SPECIES, true);?></label></td>
		<td><input type="text" name="spec" id="spec"/></td>
	</tr>
	<tr>
		<td><label for="leg"><? trans(LAB_COLLECTOR, true); ?></label></td>
		<td><input type="text" name="leg" id="leg"/></td>
	</tr>
	<tr>
		<td><label for="tbu">TBU</label></td>
		<td><input type="text" name="tbu" id="tbu" style="width:50px;"/></td>
	</tr>
	<tr>
		<td><label for="year"><? trans(LAB_DATE_YEAR, true);?></label></td>
		<td><input type="text" name="year" id="year" style="width:3em;" class="datoInterval"/>
		<span style="float:left;margin-left:6px;margin-right:6px;padding-top:3px;">-</span>
		<input type="text" name="to-year" id="to-year" style="width:3em;" class="datoInterval" /></td>
	</tr>
	<tr>
		<td><label for="lok"><? trans(LAB_LOCALITY, true);?></label></td>
		<td><input type="text" name="lok" id="lok"/></td>
	</tr>
	<tr><td colspan="2"><hr class="search"></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="button" value="<? trans(LAB_SEARCH, true);?>" onclick="Search.submit();"/>&nbsp;&nbsp;
		<input type="button" value="<? trans(LAB_RESET, true);?>" onclick="Search.reset();"/></td>
	</tr>

</table>
</form>
<div id="search-result" style="float:left;text-align:left;"></div>
</fieldset>
<?
	}

	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	Search.caption='<? trans(DB_LINK_LEVERMOSSER, true);?>';
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id="#levermosser";
	Search.submit_load="ajax/levermosser.php";
	Search.init();
});
</script>
<?
	}


}

?>
