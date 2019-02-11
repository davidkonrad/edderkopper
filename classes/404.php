<?

class Page404 extends TemplateEdderkopperGallery /*TemplateSimple*/ {
	private $error;
	private $code;

	public function __construct($code, $error) {
		$this->error=$error;	
		$this->code=$code;
	}

	protected function drawBody() {
		HTML::h2('<span style="color:darkred;">'.$this->code.'</span>&nbsp;&nbsp-&nbsp;&nbsp;'.$this->error);
		echo '<br>';
		echo '<a href="'.$this->getIndexPage().'">';
		echo '<span style="font-size:40px;">&#9668;</span>';
		echo '</a>';
	}
}

?>



