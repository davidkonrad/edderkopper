<?

class PageFloraDanica extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" language="javascript" src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/search.js"></script>
<?
	}

	private function groupSelect() {
		$html='<select name="group" id="group">';
		$html.='<option value="">[v&aelig;lg gruppe]</option>';
		$html.='<option value=" ">(ej specificeret)</option>';
		$html.='<option value="Bryophytes">Bryophytes</option>';
		$html.='<option value="Fungi incl. lichens">Fungi incl. lichens</option>';
		$html.='<option value="Algae">Algae</option>';
		$html.='</select>';
		return $html;
	}

	protected function drawBody() {
?>
<fieldset>
<legend id="content-headline">Flora Danica</legend>
<form name="searchform" id="searchform" method="post" action="">
<? $this->drawSessLang();?>
<table style="float:left;display:block;width:420px;">
	<tr>
		<td><label for="plate"><? trans(LAB_PLATE, true); ?></label></td>
		<td><input type="text" name="plate" id="plate" style="width:100px;"/></td>
	</tr>
	<tr>
		<td><label for="plateroman"><? trans(LAB_PLATE_ROMAN, true); ?></label></td>
		<td><input type="text" name="plateroman" id="plateroman" style="width:100px;"/></td>
	</tr>
	<tr>
		<td><label for="latin"><? trans(LAB_SCIENTIFIC_NAME, true);?></label></td>
		<td><input type="text" name="latin" id="latin"/></td>
	</tr>
	<tr>
		<td><label for="original"><? trans(LAB_NAME_ORIGINAL, true);?></label></td>
		<td><input type="text" name="original" id="original"/></td>
	</tr>
	<tr>
		<td><label for="volume"><? trans(LAB_VOLUME, true); ?></label></td>
		<td><input type="text" name="volume" id="volume" style="width:100px;"/></td>
	</tr>
	<tr>
		<td><label for="year"><? trans(LAB_YEAR_PUBLISHED, true); ?></label></td>
		<td><input type="text" name="year" id="year" style="width:100px;"/></td>
	</tr>
	<tr>
		<td><label for="lok"><? trans(LAB_GROUP, true);?></label></td>
		<td><? echo $this->groupSelect();?></td>
	</tr>
	<tr><td colspan="2"><hr class="search"></td></tr>
	<tr>
		<td style=""><input type="checkbox" name="exact" id="exact"/><? echo trans (LAB_SEARCH_EXACT);?></td>
		<td><input id="search-btn" type="button" value="<? trans(LAB_SEARCH, true);?>" onclick="Search.submit();"/>&nbsp;&nbsp;
		<input type="button" value="<? trans(LAB_RESET, true);?>" onclick="Search.reset();"/></td>
		<td>&nbsp;</td>
	</tr>

</table>
<div style="border-left:1px solid #33613D;width:460px;float:left;display:inline;height:250px;white-space:nowrap;""><center>
<?
$SQL='select auto_id, URLsmalPic, OriginalName, Plate_Roman from floradanica order by rand() limit 1';
$example=$this->getRow($SQL);
echo '<img src="'.$example['URLsmalPic'].'" style="height:230px;"/>';
echo '<small><a href="detail.php?page=PageFloraDanicaDetail&auto_id='.$example['auto_id'].'" target=_blank title="'.trans(LAB_SHOW_DETAILS).'" style="color:#33613d;">';
echo '<br/>'.$example['Plate_Roman'].'<br>'.$example['OriginalName'];
echo '</a></small>';
?>
</center></div>

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
	Search.caption='<? trans(DB_LINK_FLORADANICA, true);?>';
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id="#searchform";
	Search.submit_load="ajax/floradanica.php";
	Search.init();
});
</script>
<?
	}


}

?>
