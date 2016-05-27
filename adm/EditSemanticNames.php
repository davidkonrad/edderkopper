<?

class EditSemanticNames extends TemplateSimple {

	public function __construct() {
		parent::__construct();
	}

	protected function extraHead() {
?>
<style type="text/css">
.cat-content {
	margin-left: 30px;
}
.url {
	color: navy;
	font-family: "courier", "courier new";
}
.miss {
	color: maroon;
	font-size: 90%;
}

</style>
	<?
	}

	private function noURL() {
		HTML::span('&lt; mangler >', 'miss');
	}

	private function categoryLink($category_id) {
		echo '<a href="admin-categories?category='.$category_id.'" title="Rediger category">';
		echo '<img src="ico/page_edit.png" style="height:12px;" title="Rediger category">';
		echo '</a>&nbsp;';
	}

	private function pageLink($page_id) {
		echo '<a href="editpage?page_id='.$page_id.'" title="Rediger category">';
		echo '<img src="ico/page_edit.png" style="height:12px;" title="Rediger side">';
		echo '</a>&nbsp;';
	}
		
	protected function drawBody() {
		parent::drawBody();

		$SQL='select category_id, semantic_name, caption from zn_category_desc '.
			'where lang_id='.Lang::lang_id().' order by category_id';

		$result=$this->query($SQL);
		while($row = mysql_fetch_assoc($result)) {
			HTML::h3($row['caption']);

			echo '<div class="cat-content">';
			$this->categoryLink($row['category_id']);
			HTML::span('/'.$row['semantic_name'], 'url');
			if ($row['semantic_name']=='') $this->noURL();


			$SQL='select c.page_id, c.anchor_title, c.semantic_name from zn_page_content c, zn_page p '.
				'where lang_id='.Lang::lang_id().' '.
				'and p.page_id=c.page_id '.
				'and p.category_id='.$row['category_id'];

			$pages=$this->query($SQL);
			echo '<div class="cat-content">';
			while ($page = mysql_fetch_assoc($pages)) {
				$this->pageLink($page['page_id']);
				HTML::span('/'.$page['semantic_name'], 'url');
				if ($page['semantic_name']=='') $this->noURL();
				HTML::br(2);
			}
			echo '</div>';
			echo '</div>';
			HTML::divider(10);
		}
	}

}

?>
