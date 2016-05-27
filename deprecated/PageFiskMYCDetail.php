<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PageFiskMYCDetail extends TemplateDetail {
	private $auto_id;
	private $record;

	public function __construct() {
		$this->auto_id=$_GET['auto_id'];
		parent::__construct();
	}
	
	private function getRecord() {
		$SQL='select * from fisk_myc where auto_id="'.$this->auto_id.'"';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function drawBody() {
		$this->getRecord();

?>
<fieldset>
<legend>Fisk MYC</legend>
<table>
<?
$this->drawRow('P', $this->record['P']);
$this->drawRow('CatFrom', $this->record['CatFrom']);
$this->drawRow('CatTo', $this->record['CatTo']);
$this->drawRow(trans(LAB_FAMILY), $this->record['Family']);
$this->drawRow(trans(LAB_GENUS), $this->record['Genus']);
$this->drawRow(trans(LAB_SPECIES), $this->record['Species']);
$this->drawRow(trans(LAB_AUTHOR), $this->record['Author']);
$this->drawRow(trans(LAB_TYPE), $this->record['Type']);
$this->drawRow(trans(LAB_LENGTH_MM), $this->record['Length_mm']);
$this->drawRow(trans(LAB_SEX), $this->record['Sex']);
$this->drawRow(trans(LAB_DNA_SAMPLE), $this->record['DNA_Sample']);
$this->drawRow(trans(LAB_DATE), $this->record['Date'].' / '.$this->record['Month'].' / '.$this->record['Year']);
$this->drawRow(trans(LAB_CRUISE), $this->record['Cruise']);
$this->drawRow(trans(LAB_STATION), $this->record['Station']);
$this->drawRow(trans(LAB_HAUL), $this->record['Haul']);
$this->drawRow(trans(LAB_GEAR), $this->record['Gear']);
$this->drawRow(trans(LAB_LOCALITY), $this->record['Locality']);
$this->drawRow(trans(LAB_DEPTH_M), $this->record['Depth_m']);
$this->drawRow(trans(LAB_WIRE_M), $this->record['Wire_m']);
$this->drawRow(trans(LAB_TEMP_DEGC), $this->record['Temp__DegC']);
$this->drawRow(trans(LAB_REMARKS), $this->record['Remarks']);
?>
</table>
</fieldset>
<?
		HTML::divider(1);
		$this->drawCloseLink();
	}

	protected function getPageTitle() {
		return 'Fisk MYCLevermosser - #'.$this->auto_id;
	}

}

?>
