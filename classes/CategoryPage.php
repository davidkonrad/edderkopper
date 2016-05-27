<?

class CategoryPage extends TemplateSimple {
	private $category_id;
	private $category;
	private $category_pages;

	public function __construct($category_id) {
		parent::__construct();
		Lang::load();
		$this->category_id=$category_id;
		
		$SQL='select caption, category_desc '.
			'from zn_category_desc '.
			'where category_id='.$category_id.' and lang_id='.$_SESSION[LANGUAGE];
		$this->category=$this->getRow($SQL);
	}

	public function drawBody() {
		//parent::drawBody();
		//$this->debug($this->getLangLinks());
		Lang::flagMenu($this->getLangLinks());//$this->page_id));

		$this->drawCategory();
		$this->drawPages();
	}

	//return language specific URL's for page_id
	protected function getLangLinks() {
		$links=array();
		$SQL='select lang_id, category_id, semantic_name from zn_category_desc where category_id='.$this->category_id.' order by lang_id';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			//$href=($row['semantic_name']!='') ? $row['semantic_name'] : 'index.php?category='.$row['category_id'].'&lang='.$row['lang_id'];
			$href=($row['semantic_name']!='') ? $row['semantic_name'] : 'index.php?category='.$row['category_id'];//.'&lang='.$row['lang_id'];
			$href=trim($href);
			$href=str_replace(' ', '%20', $href);
			$links[$row['lang_id']]=$href;
		}
		return $links;
	}	

	protected function drawCategory() {
		echo '<h1>'.$this->category['caption'].'</h1>';
		echo $this->category['category_desc'];
	}

	protected function drawPages() {
		echo '<br>';
		$SQL='select p.page_id, c.semantic_name, c.anchor_caption from zn_page p, zn_page_content c '.
		' where p.category_id='.$this->category_id.' and (p.page_id=c.page_id) and c.lang_id='.$_SESSION[LANGUAGE].' ';

		if (!$this->loggedIn()) {
			$SQL.='and p.visible=1 ';
		} 
		$SQL.='order by p.weight';

		$result=$this->query($SQL);
		while ($row = mysql_fetch_array($result)) {
			if ($this->loggedIn()) {
				echo '<a href="'.EDIT_PAGE.'?page_id='.$row['page_id'].'"><img src="ico/page_edit.png"></a>&nbsp;';
			} else {
				$pageType = $this->getPageType($row['semantic_name'], $row['page_id']);				
				switch ($pageType) {
					case PAGE_CLASS : 
						echo '<img src="ico/page_white_lightning.png" alt="" style="position:relative;top:3px;">';
						break;
					default :
						echo '<img src="ico/page_white_text.png" alt="" style="position:relative;top:3px;">';
						break;
				}
			}			

			$link = ($row['semantic_name']!='') ? ''.$row['semantic_name'] : '?id='.$row['page_id'];
			echo '&nbsp;<a href="'.$link.'">'.trans($row['anchor_caption']).'</a><br/>';
		}
	}

	protected function getPageTitle() {
		echo trans($this->category['caption']);
	}

	protected function getMetaDesc() {
		echo trans($this->category['category_desc']);
	}

}

?>
