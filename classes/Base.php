<?
//baseclass for all classes, contains dummy functions expected for a class when it is processed

class Base extends Db {
	public $info;
	public $template = 'TemplateClass';

	public function __construct() {
		parent::__construct();
	}

	protected function getLanguage() {
		return $_SESSION[LANGUAGE];
	}

	//additional content for <head></head>
	public function extraHead() {
	}

	//main draw function
	public function drawBody() {
		Lang::flagMenu($this->getLangLinks($this->info['page_id']));
	}

	//additional content inserted just before the footer
	public function drawBeforeFooter() {
	}

	//return database info for the current class
	public function getInfo() {
		$class_name=get_class($this);
		$SQL='select c.page_id, c.lang_id, c.anchor_caption, c.anchor_title, c.semantic_name, c.title, c.meta_desc '.
			'from zn_page_content c, zn_page_class p '.
			'where p.class_name="'.$class_name.'" and c.lang_id='.$_SESSION[LANGUAGE].' and c.page_id=p.page_id ';
		return $this->getRow($SQL);
	}

	//return language specific URL's for page_id
	protected function getLangLinks($page_id, $args=true) {
		$params=($args) ? $this->getParams() : '';
		$links=array();
		$SQL='select lang_id, semantic_name from zn_page_content where page_id='.$page_id.' order by lang_id';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			$href=$row['semantic_name'].$params;
			$href=str_replace(' ', '%20', $href);
			$links[$row['lang_id']]=$href;
		}
		return $links;
	}	

}

?>
