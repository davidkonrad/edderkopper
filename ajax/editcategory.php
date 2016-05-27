<?
ini_set('error_reporting', E_ALL);

include('AjaxBase.php');
include('../common/HTML.php');

class EditCategory extends AjaxBase {

	public function __construct() {
		parent::__construct();
		switch ($_GET['action']) {
			case 'create' : $this->createCategory(); break;
			case 'update' : $this->updateCategory(); break;
			case 'select' : $this->showCategory(); break;
			default : break;
		}
		$this->drawScript();
	}

	private function createCategory() {
		$SQL='insert into zn_category () values()'; //all fields has default values
		$this->exec($SQL);
		$id=mysql_insert_id();
		
		$languages = $this->getLanguages();
		foreach($languages as $language) {
			$SQL='insert into zn_category_desc (category_id, lang_id, caption, category_desc, semantic_name) values('.
				$this->q($id).
				$this->q($language['lang_id']).
				$this->q('category_caption_'.$id.'_'.$language['name']).
				$this->q('category_desc_'.$id.'_'.$language['name']).
				$this->q('semantic_name_'.$id.'_'.$language['name'], false).
			')';
			$this->exec($SQL);
		}
		$this->showCategory($id);
	}

	private function updateCategory() {
		$SQL='update zn_category set '.		
			'weight='.$this->q($_GET['weight']).
			'template_class='.$this->q($_GET['template_class']).
			'visible='.$this->q($_GET['visible'], false).' '.
			'where category_id='.$_GET['category_id'];
		$this->exec($SQL);

		$SQL='update zn_category_desc set '.
			'caption='.$this->q($_GET['caption']).
			'category_desc='.$this->q($_GET['category_desc']).
			'semantic_name='.$this->q($_GET['semantic_name'], false).' '.
			'where category_id='.$_GET['category_id'].' and lang_id='.$_GET['lang_id'];

		$this->exec($SQL);
		$this->showCategory();
	}

	private function showCategory($category_id=null) {
		if ($category_id==null) $category_id=$_GET['category_id'];

		$SQL='select c.category_id, c.template_class, c.visible, c.weight, '.
			'd.caption, d.category_desc, d.semantic_name '.
			'from zn_category c, zn_category_desc d '.
			'where c.category_id='.$category_id.' '.
			'and (c.category_id=d.category_id) and d.lang_id='.$_GET['lang_id'];
		$row=$this->getRow($SQL);
?>
<table id="category-table">
<tr>
<td>Navn</td><td><input type="text" name="caption" id="caption" value="<? echo $row['caption']; ?>" style="width:400px;"/></td>
</tr>
<tr>
<td>Semantisk navn</td><td><input type="text" name="semantic_name" id="semantic_name" value="<? echo $row['semantic_name'];?>" style="width:400px;"/></td>
</tr>
<tr>
<td>Vægt</td><td><? HTML::selectWeight($row['weight']); ?></td>
</tr>
<tr>
<td>Synlighed</td><td><? HTML::selectYesNo('visible', $row['visible']); ?></td>
</tr>
<tr>
<td>Template</td><td><input type="text" size="40" id="template_class" name="template_class" value="<? echo $row['template_class']; ?>"></td>
</tr>
<tr>
<td style="vertical-align:top;">Beskrivelse</td><td><textarea name="category_desc" id="category_desc" rows="7" cols="60"><? echo $row['category_desc']; ?></textarea></td>
</tr>

<?
		echo '<tr><td colspan=2><hr></td></tr>';
		echo '<tr><td></td><td><input type="button" onclick="EditCategory.saveCategory();" value="Gem"></td></tr>';
?>
</table>
<?
	}

	private function drawScript() {
?>
<script type="text/javascript">
$(document).ready(function() {
	if (CKEDITOR.instances['category_desc']) { 
		delete CKEDITOR.instances['category_desc'] 
	};
	CKEDITOR.replace('category_desc', {width:"700px",height:"300px"});
});
</script>
<?
	}

}

$category = new EditCategory();

?>
