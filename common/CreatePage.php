<?
include('Db.php');

class CreatePage extends Db {
	private $page_id;

	public function __construct() {
		parent::__construct();
		switch ($_GET['type']) {
			case 'class' : $this->createClassPage(); break;
			case 'static' : $this->createStaticPage(); break;
			case 'link' : $this->createLinkPage(); break;
			// internal function, for test purposes only
			case 'page' : $this->createPage(); break;
			default : break;
		}
		$this->redirect();
	}		

	private function redirect() {
		header('location: ../editpage?page_id='.$this->page_id);
	}

	private function createPage() {
		$SQL='insert into zn_page () values ()';
		$this->exec($SQL);
		$this->page_id=mysql_insert_id();
		$languages=$this->getLanguages();
		foreach($languages as $language) {
			$SQL='insert into zn_page_content '.
				'(page_id, lang_id, anchor_caption, semantic_name, title, meta_desc) values('.
				$this->q($this->page_id).
				$this->q($language['lang_id']).
				$this->q('anchor_'.$this->page_id.'_'.$language['name']).
				$this->q('semantic_'.$this->page_id.'_'.$language['name']).
				$this->q('title_'.$this->page_id.'_'.$language['name']).
				$this->q('meta_'.$this->page_id.'_'.$language['name'], false).
			')';
			$this->exec($SQL);
		}
	}		

	private function createStaticPage() {
		$this->createPage();
		$languages=$this->getLanguages();
		foreach($languages as $language) {
			$SQL='insert into zn_page_static (page_id, lang_id, page_html) values('.
				$this->q($this->page_id).
				$this->q($language['lang_id']).
				$this->q('html/content for page #'.$this->page_id.' language '.$language['name'], false).
			')';
			$this->exec($SQL);
		}
	}

	private function createClassPage() {
		$this->createPage();
		$SQL='insert into zn_page_class (page_id, class_name) values ("'.$this->page_id.'", "DynamicPage'.$this->page_id.'")';
		$this->exec($SQL);
	}

	private function createLinkPage() {
		$this->createPage();
		$SQL='insert into zn_page_link (page_id) values ('.$this->page_id.')';
		$this->exec($SQL);

		$languages=$this->getLanguages();
		foreach($languages as $language) {
			$SQL='insert into zn_page_link_desc (page_id, lang_id, link_desc) values('.
				$this->q($this->page_id).
				$this->q($language['lang_id']).
				$this->q('', false).
			')';
			$this->exec($SQL);
		}

	}

}

$page= new CreatePage();

?>
