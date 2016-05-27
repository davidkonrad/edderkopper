<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PageFaroeAlgerDetail extends TemplateDetail {
	private $musnr;
	private $record;

	public function __construct() {
		$this->musnr=$_GET['musnr'];
		parent::__construct();
	}
	
	private function getRecord() {
		mysql_set_charset('utf8');
		$SQL='select * from faroealger where musnr="'.$this->musnr.'"';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function drawBody() {
		$this->getRecord();

?>
<fieldset>
<legend><? trans(DB_LINK_FAROEALGER, true);?></legend>
<table>
<?
$this->drawRow(LAB_SCIENTIFIC_NAME, $this->record['artnavn']);
$this->drawRow(LAB_TAXON_GROUP, $this->record['klassenavn']);
$this->drawRow(LAB_DANISH_NAME, $this->record['dknavn']);
$this->drawRow(LAB_LOCALITY, $this->record['lokalitet']);
$this->drawRow(LAB_WATER_DEPTH, $this->record['dybde']);
$this->drawRow(LAB_COLLECTOR, $this->record['leg']);
$this->drawRow(LAB_DETERMINATOR, $this->record['det']);
?>
</table>
</fieldset>
<?
		HTML::divider(1);
		$this->drawCloseLink();
	}

	protected function getPageTitle() {
		return trans(DB_LINK_FAROEALGER).' - '.$this->musnr;
	}

}

?>
