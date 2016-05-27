<?

class StaticPage extends TemplateSimple {
	private $page_id;
	private $row;

	public function __construct($page_id) {
		parent::__construct();
		$this->page_id=$page_id;
		$SQL='select c.title, c.anchor_caption, c.meta_desc, s.page_html '.
			'from zn_page_content c, zn_page_static s '.
			'where (c.page_id='.$page_id.' and c.lang_id='.$_SESSION[LANGUAGE].') '.
			'and (s.page_id=c.page_id) and s.lang_id='.$_SESSION[LANGUAGE];
		$this->row=$this->getRow($SQL);
	}

	protected function drawBody() {
		Lang::flagMenu($this->getLangLinks($this->page_id));

		echo '<fieldset id="static'.$this->page_id.'">';
		echo '<legend>'.$this->row['anchor_caption'].'</legend>';
		echo stripslashes($this->row['page_html']); //due to $this->q() when inserted
		echo '</fieldset>';
		$this->drawRelatedContent();
	}

	protected function getPageTitle() {
		echo $this->row['title'];
	}

	protected function getMetaDesc() {
		echo $this->row['meta_desc'];
	}

	//from Base.php
	//return language specific URL's for page_id
/*
	protected function getLangLinks($page_id) {
		$links=array();
		$SQL='select lang_id, semantic_name from zn_page_content where page_id='.$page_id.' order by lang_id';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			$href=$row['semantic_name'];
			$href=str_replace(' ', '%20', $href);
			$links[$row['lang_id']]=$href;
		}
		return $links;
	}	
*/

}

?>
