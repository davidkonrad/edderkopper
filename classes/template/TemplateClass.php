<?
/**
 * 
 * Template for class pages
 *
**/
class TemplateClass extends TemplateSimple {
	private $class_;
	private $info;
	public $isDetailPage;

	public function __construct($class) {
		parent::__construct();
		$this->class_=$class;
		$this->info=$class->getInfo();
		//$this->isDetailPage=(get_parent_class($this->class_)=='DetailBase');
		$this->isDetailPage=is_subclass_of($this->class_, 'DetailBase');
	}

	protected function extraHead() {
		$this->class_->extraHead();
	}

	protected function getMetaDesc() {
		if ($this->isDetailPage) {
			$meta=$this->class_->getMeta();
		} else {
			$meta=$this->info['meta_desc'];
		}
		echo $meta;
	}

	protected function getPageTitle() {
		if ($this->isDetailPage) {
			$title=$this->class_->getTitle();
			if ($title=='') $title=$this->info['title'];
		} else {
			$title=$this->info['title'];
		}
		echo $title;
	}

	protected function drawBeforeFooter() {
		$this->class_->drawBeforeFooter();
	}

	protected function drawBody() {
		$this->class_->drawBody();
		//get related content if class is anything else of type DetailBase and not marked as standalone
		//if (get_parent_class($this->class_)!='DetailBase') {
		if (!$this->isDetailPage) {
			if (!$_SESSION[STANDALONE]) {
				$this->drawRelatedContent();	
			}
		}
	}


}

?>
