<?
/**
 * 
 * Template with body and right column
 *
**/
class TemplateRightCol extends TemplateSimple {

	public function __construct() {
		parent::__construct();
	}

	public function draw() {
		$this->pageHead();
		$this->drawWrapper();
		echo '<div id="ensure-overflow-y-auto" style="overflow:auto;">';
		echo '<div id="rightcol-left">';
		$this->drawBody();
		echo '</div> <!-- rightcol-left end -->'."\n";

		echo '<div id="rightcol-right">';
		$this->drawRightCol();
		echo '</div> <!-- rightcol-right end -->'."\n";
		echo '</div>';
		$this->drawFooter();
	}

	//page right column
	protected function drawRightCol() {
	}


}

?>
