<?

class PageTBUKartoteketDetail extends TemplateDetail {
	private $LNR;
	private $record;

	public function __construct() {
		$this->LNR=$_GET['id'];
		parent::__construct();
	}

	public function drawScript() {
	}
	
	private function getRecord() {
		mysql_set_charset('utf8');
		$SQL='select * from tbu_kartoteket where LNR='.$this->LNR;
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function drawBody() {
		$this->getRecord();

?>
<fieldset class="detail-left">
<legend>TBU Kartoteket</legend>
<table>
<?
$this->drawRow(trans(LAB_SCIENTIFIC_NAME), $this->record['Artsnavnfuld']);
$this->drawRow(trans(LAB_DANISH_NAME), $this->record['DKnavn']);
//$this->drawRow(trans(LAB_DATE), $this->record['dato_day'].'/'.$this->record['dato_month'].'/'.$this->record['dato_year']);
$this->drawRow(trans(LAB_DATE), $this->record['DatoCorr']);
$this->drawRow(trans(LAB_TBU), $this->record['TBU']);
$this->drawRow(trans(LAB_STATUS), $this->statusToName($this->record['STATUS']));
$this->drawRow(trans(LAB_COLLECTOR), $this->record['LEG']);
$this->drawRow(trans(LAB_DETERMINATOR), $this->record['DET']);
$this->drawRow(trans(LAB_ALL_NAMES), $this->record['TotalNavne']);
$this->drawRow(trans(LAB_NOTE), $this->record['NOTE']);
?>
</table>
</fieldset>

<fieldset class="detail-right">
<legend>Kort, lokalitet</legend>
<center>
<?
$src='map/'.$this->record['TBU'].'.gif';
if (file_exists($src)) {
	echo '<img src="'.$src.'" alt="Kort - findested"/>';
} else {
	$src='img/brand_ku.gif';
	echo '<img src="'.$src.'" alt="Kort kunne ikke findes"/>';	
}
?>
</center>
</fieldset>
<?
	HTML::divider(1);
	$this->drawCloseLink();
	}

	public function statusToName($status_code) {
		switch ($status_code) {
			case 'Ex' : return 'Udd&oslash;d'; break;
			case 'E'  : return 'Truet'; break;	
			case 'V'  : return 'S&aring;rbar'; break;
			case 'R'  : return 'Sj&aelig;lden';
			default : return 'Ukendt'; break;
		}
	}

	protected function getPageTitle() {
		return 'TBU-kartoteket -'.$this->LNR;
	}

}

?>
