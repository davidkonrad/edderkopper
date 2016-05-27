<?

class PageFloraAgarcinaDanica extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" language="javascript" src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/search.js"></script>
<?
	}

	protected function drawBody() {
?>
<fieldset>
<legend id="content-headline">S&oslash;g i Svampe-Databasen</legend>
<form name="searchform" id="searchform" method="post" action="">
<table style="float:left;display:block;width:450px;">
	<tr>
		<td><label for="latin"><? trans(LAB_SCIENTIFIC_NAME, true);?></label></td>
		<td><input type="text" name="latin" id="latin"/></td>
	</tr>
	<tr>
		<td><label for="dansk"><? trans(LAB_DANISH_NAME, true);?></label></td>
		<td><input type="text" name="dansk" id="dansk"/></td>
	</tr>
	<tr>
		<td><label for="FAD"><? trans(LAB_FAD_PLATE_NO, true);?></label></td>
		<td><input type="text" name="FAD" id="FAD"/></td>
	</tr>
	<tr><td colspan="2"><hr class="search"></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="button" value="<? trans(LAB_SEARCH, true);?>" onclick="Search.submit();"/>
		&nbsp;&nbsp;
		<input type="button" value="<? trans(LAB_RESET, true);?>" onclick="Search.reset();"/></td>
		<td>&nbsp;</td>
	</tr>
</table>
<div style="border-left:1px solid #33613D;float:left;display:inline;width:420px;white-space:nowrap;"">
<center>
<?
$SQL='select URLpic, FADtavlenumber, Currentnavn, DanskNavn from floraagarcinadanica order by rand() limit 1';
mysql_set_charset('utf8');
$example=$this->getRow($SQL);
$pic='Agaricina/'.strtoupper($example['URLpic']);
echo '<img src="'.$pic.'" style="height:180px;"/>';
echo '<a href="detail.php?PageFloraAgarcinaDanicaDetail&FAD='.$example['FADtavlenumber'].'" target=_blank title="'.trans(LAB_SHOW_DETAILS).'" style="color:#33613d;">';
echo '<br>'.$example['Currentnavn'].'<br>'.$example['DanskNavn'];
echo '</a>';
?>
</center>
</div>
</form>
<div id="search-result" style="float:left;text-align:left;"></div>
</fieldset>
<?
	$this->drawRelatedContent();
	}

	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	Search.caption="<? trans(DB_LINK_FLORAAGARICINA, true);?>";
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id="#searchform";
	Search.submit_load="ajax/floraagarcinadanica.php";
	Search.init();
});
</script>
<?
	}

}


?>

