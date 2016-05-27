<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PageLevermosserDetail extends TemplateDetail {
	private $auto_id;
	private $record;

	public function __construct() {
		$this->auto_id=$_GET['auto_id'];
		parent::__construct();
	}
	
	private function getRecord() {
		mysql_set_charset('utf8');
		$SQL='select * from levermosser where auto_id="'.$this->auto_id.'"';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function extraHead() {
?>
<style type="text/css">
td.caption {
	width: 120px;
	vertical-align: top;
	font-weight: bold;
}
</style>
<?
	}

	protected function getMapFilename($TBU) {
		$filename='map/'.$TBU.'.gif';
		if (!file_exists($filename)) {
			$filename='map/'.$TBU.'a.gif';
		}
		return $filename;
	}
	
	protected function drawBody() {
		$this->getRecord();

?>
<fieldset class="detail-left">
<legend>Levermosser</legend>
<table>
<?
$this->drawRow('Artkode ',$this->record['ARTKODE']);
$this->drawRow(trans(LAB_GENUS), $this->record['GEN']);
$this->drawRow(trans(LAB_SPECIES), $this->record['SPEC']);
$this->drawRow(trans(LAB_COLLECTOR), $this->record['LEG']);
$this->drawRow('Aut1 ',$this->record['AUT1']);
$this->drawRow(trans(LAB_DATE), $this->record['DAG'].'/'.$this->record['MD'].'/'.$this->record['AAR']);
$this->drawRow('Dato note ',$this->record['DATO_NOTE']);
$this->drawRow(trans(LAB_LOCALITY), $this->record['LOK']);
$this->drawRow(trans(LAB_HABITAT), $this->record['HAB']);
?>
</table>
</fieldset>
<fieldset class="detail-right">
<legend>Kort / lokalitet</legend><center><br>
<?
echo '<img src="'.$this->getMapFilename($this->record['TBU']).'" alt="Kort - findested" style="margin-left:auto;margin-right:auto;"/>';
?>
<center></fieldset>
<?
		HTML::divider(1);
		$this->drawCloseLink();
	}

	protected function getPageTitle() {
		return 'Levermosser - #'.$this->auto_id;
	}

}

?>
