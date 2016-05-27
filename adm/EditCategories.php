<?

class EditCategories extends TemplateSimple {
	private $category_id;
	private $lang_id;
	
	public function __construct($lang_id=null) {
		parent::__construct();
		$lang_id = ($lang_id==null) ? $_SESSION[LANGUAGE] : $lang_id;
		$this->lang_id = $lang_id;
		$this->category_id=1;
	}

	protected function extraHead() {
?>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/editcategory.js"></script>
<?
	}

	protected function drawBody() {
		$this->buttonBar();
		echo '<div id="edit-category"></div>';
	}

	protected function drawBeforeFooter() {
		if ($this->hasParam('category')) {
?>
<script type="text/javascript">
$(document).ready(function() {
var id=<? echo $this->getParam('category');?>;
EditCategory.selectCategory(id);
});
</script>
	<?
		}
	}

	private function categorySelect() {
		$SQL='select c.category_id, d.caption, d.category_desc '.
			'from zn_category c, zn_category_desc d '.
			'where c.category_id=d.category_id and d.lang_id='.$this->lang_id;

		$result = $this->query($SQL);
		$select = '<select name="category_id" id="category_id" onchange="EditCategory.selectCategory();">';
		while ($row = mysql_fetch_array($result)) {
			$select.='<option value="'.$row['category_id'].'">'.trans($row['caption']).'</option>';
		}
		$select .= '</select>';
		return $select;
	}

	private function langSelect() {
		echo '<span style="float:right;clear:right;">';
		echo '<select id="lang_id" onchange="EditCategory.changeLang();" style="width:100px;">';
		echo HTML::getOption(1, 'Dansk', $this->lang_id);
		echo HTML::getOption(2, 'English', $this->lang_id);
		echo '</select>';
		echo '</span>';
	}

	protected function buttonBar() {
		echo '<span style="float:left;text-align:left;width:600px;">';
		echo '<input type="hidden" name="lang" id="lang" value="'.$_SESSION[LANGUAGE].'"/>';
		echo '<input type="button" style="clear:none;float:left;" onclick="EditCategory.index();" value="'.trans(LAB_FRONTPAGE).'"/>';
		echo '<input type="button" style="clear:none;float:left;" onclick="EditCategory.createCategory();" value="'.trans(LAB_CATEGORY_CREATE).'"/>';
		echo $this->categorySelect();
		echo '</span>';
		$this->langSelect();
		echo '<hr style="clear:both;">';
	}

}

?>
