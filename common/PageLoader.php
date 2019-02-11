<?

error_reporting(1);
ini_set('display_errors', '1'); 


class PageLoader extends Db {
	//matching page_id found by getPageType, regardless page type or semantic_name load of page
	private $page_id; 
	private $user;

	public function __construct($ignore=false) {
		parent::__construct();

		//DEFAULT_USER = 1
		$this->user = Login::isLoggedIn();

		if ($ignore) {
			Lang::load();
			$page = new Sitemap();
			$page->draw();
			return;
		}

		$semantic = $this->currentSemanticName();

		if ($semantic=='index.php') $semantic='';
		$id = (isset($_GET['id'])) ? $_GET['id'] : '';

		//set introduktion-til-danmarks-edderkopper as frontpage, if no page specied
		if ($semantic=='') {
			$semantic = 'introduktion-til-danmarks-edderkopper';
		}

		if (($semantic!='') || ($id>0)) {
			$page = $this->loadPage($semantic, $id);
		} else {
			Lang::load(); //??
			$page = new Sitemap();
		}

		$_SESSION[CURRENT_PAGE_ID]=$this->page_id;

		if ($this->page_id>0) {
			$SQL='select standalone from zn_page where page_id='.$this->page_id;
			$row=$this->getRow($SQL);
			$_SESSION[STANDALONE]=($row['standalone']==1);
		} else {
			$_SESSION[STANDALONE]=false;
		}

		$page->draw();
	}

	/*
		check if it is a category main page
		get semantic_name, return category_id or false
	*/
	private function semanticToCategory($semantic) {
		$SQL = 'select category_id, lang_id from zn_category_desc where semantic_name="'.$semantic.'"';
		$result = $this->query($SQL);
		if ($result->rowCount() > 0) {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$_SESSION[LANGUAGE]=$row['lang_id'];
			return $row['category_id'];
		} else {
			return false;
		}
	}

	/*
		returns the template_class for the corresponding zn_category, or the default template
	*/
	protected function getCategoryTemplate($class=null) {
		$semantic=$this->currentSemanticName();
		$SQL='select c.template_class '.
				'from zn_category c, zn_page_content pc, zn_page p '.
				'where pc.semantic_name="'.$semantic.'" and p.page_id=pc.page_id '.
				'and p.category_id=c.category_id';

		$result=$this->getRow($SQL);
		$template=$result['template_class'];
		if ($template=='') {
			if (is_object($class)) {
				$template=$class->template;
			}
			if ($template=='') {
				$template='TemplateClass';
			}
		}
		return $template;
	}

	/*
		get page type according to id or semantic name
	*/
	public function getPageType($semantic, $id) {
		$where = ($id!='') ? 'z.page_id='.$id : 'z.semantic_name="'.$semantic.'"';
		$where = ' where '.$where;
		$where.= ' and (c.page_id=z.page_id) limit 1';

		$SQL='select c.page_id, z.lang_id from zn_page_class c, zn_page_content z '.$where;
		if ($this->hasData($SQL)) {
			$r=$this->getRow($SQL);
			$this->page_id=$r['page_id'];
			$_SESSION[LANGUAGE]=$r['lang_id'];
			return PAGE_CLASS;
		}

		$SQL='select c.page_id, z.lang_id from zn_page_static c, zn_page_content z '.$where;
		if ($this->hasData($SQL)) {
			$r=$this->getRow($SQL);
			$this->page_id=$r['page_id'];
			$_SESSION[LANGUAGE]=$r['lang_id'];
			return PAGE_STATIC;
		}

		//return PAGE_UNDEFINED;
		return -1;
	}

	protected function loadPage($semantic, $id) {
		//process $semantic, 
		$url=parse_url($semantic);
		//cleanup semantic, params now in $url['query'], if any (!!)
		if (strpos($semantic, '?')>0) {
			$semantic=substr($semantic, 0, strpos($semantic, '?'));
		}

		//process params
		$_SESSION[GET]=array();
		if (isset($url['query'])) {
			$params=urldecode($url['query']);
			$params=str_replace('&','=',$params);
			$params=explode('=',$params);
			//remove amp; & in &amp; is used by str_replace
			$params=str_replace('amp;','',$params);
			//$this->debug($params);
			$_SESSION[GET]=$params;
			//[0] => param1
			//[1] => value1
			//[2] => param2
			//[3] => value2
			//...
		} 

		//check for "specials", eg editpage etc
		//$query=''; //??
		switch ($url['path']) {
			case EDIT_PAGE :
				//parse the query part, extract the params
				parse_str($url['query'], $query);
				$page_id = isset($query['page_id']) ? $query['page_id'] : -1; //should really never become -1
				$lang_id = isset($query['amp;lang_id']) ? $query['amp;lang_id'] : 1; //default dansk
				Lang::load();
				return new EditPage($page_id, $lang_id);
				break;

			case EDIT_CATEGORY :
				Lang::load();
				return new EditCategories();//$lang_id);
				break;
			
			default : break;
		}

		//check for category-id, or 
		if (isset($url['query'])) {
			$pid=explode('=',$url['query']);
			if ($pid[0]=='category') {
				return new CategoryPage($pid[1]);
			}
		}

		//check for semantic corresponding to a category, return id
		$cat_id=$this->semanticToCategory($semantic);
		if ($cat_id) {
			return new CategoryPage($cat_id);
		}

		$page_type = $this->getPageType($semantic, $id);
		Lang::load(); //load language file

		switch ($page_type) {
			case PAGE_CLASS : 
				$SQL='select class_name from zn_page_class where page_id='.$this->page_id;
				$row=$this->getRow($SQL);
				$class_name = ($row['class_name']!='') ? $row['class_name'] : '<unknown>';
				if ($class_name!='') {
					if (class_exists($class_name)) {
							
						//is it a template?
						if (get_parent_class($class_name)=='TemplateSimple') {
							return new $class_name;
							break;
						} else {
							/*
								It is a "new style" class
								processed by TemplateClass (default), defined by class_->template
							*/
							$class=new $class_name;
							$template = $this->getCategoryTemplate($class);
							return new $template($class);
						}
					}
				}
				return new Page404(404, trans(LAB_404));
				break;

/*
			case PAGE_STATIC : $template = $this->getCategoryTemplate();
							if ($template!='TemplateClass') {
								return new $template($this->page_id);
							} else {
								return new StaticPage($this->page_id);
							}
*/

			case PAGE_STATIC : 
				$SQL='select alternative_template from zn_page where page_id='.$this->page_id;
				$row=$this->getRow($SQL);
				$template = $row['alternative_template'] != '' 
					? $row['alternative_template']
					: $this->getCategoryTemplate();

				if ($template!='TemplateClass') {
					return new $template($this->page_id);
				} else {
					return new StaticPage($this->page_id);
				}
				break;


			default : 
					return new Page404(404, trans(LAB_404));
					break;
		} 
	}

}

?>
