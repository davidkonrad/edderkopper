<?

class ToolDigitTyperCollection extends ClassBase {
	private $path = 'resources';
	private $fileList = array();

	public function drawBody() {
		parent::drawBody();
		HTML::h2('Rediger virtuel samling');
		HTML::br(2);
		$this->drawSessLang();
		$this->drawButtons();
		$this->drawCnt();
	}

	protected function collectionSelect() {
		$html='<select id="collection" name="collection" style="width:250px;" class="no-auto-select">';
		$html.='<option value="">[vælg samling]</option>';
		$SQL='select collection_name from digit_typer_collection order by collection_name';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			$html.='<option value="'.$row['collection_name'].'">'.$row['collection_name'].'</option>';
		}
		$html.='</select>';
		echo $html;
	}

	protected function drawCnt() {
?>
<div id="collection-cnt"></div>
<?
	}

	protected function drawButtons() {
?>
<input type="button" id="btn-create" value="Opret ny samling">
<? $this->collectionSelect();?>
<hr class="search">
<?
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
	$("#btn-create").click(function() {
		var collection=prompt('Ny samling','');
		if (collection!='' && collection!=null) {
			Collection.create(collection);
		}
	});
	$("#collection").change(function() {
		var collection=$("#collection").val();
		Collection.getList(collection);
	});	
</script>
<?
	}

	public function extraHead() {
?>
<script type="text/javascript" src="js/edit_digit_typer_collection.js"></script>
<style type="text/css">
th {
	background-color: #ebebeb;
}
</style>
<?
	}
}


?>
