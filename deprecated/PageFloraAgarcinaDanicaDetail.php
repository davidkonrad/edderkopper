<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PageFloraAgarcinaDanicaDetail extends TemplateDetail {
	private $FAD;
	private $record;

	public function __construct() {
		parent::__construct();
		$this->FAD=$_GET['FAD'];
	}
	
	private function getRecord() {
		mysql_set_charset('utf8');
		$SQL='select * from floraagarcinadanica where FADtavlenumber="'.$this->FAD.'"';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function drawBody() {
		$this->getRecord();

?>
<fieldset class="detail-left">
<legend>Flora Agaricina Danica</legend>
<table>
<?
$this->drawRow(trans(LAB_SCIENTIFIC_NAME) ,$this->record['Currentnavn']);
$this->drawRow(trans(LAB_FAD_NAME),$this->record['FADnavn']);
$this->drawRow(trans(LAB_DANISH_NAME), $this->record['DanskNavn']);
$this->drawRow(trans(LAB_PLATE), $this->record['FADtavlenumber']);
$this->drawRow(trans(LAB_INTERPRETED_BY), $this->record['Moderator']);
$this->drawRow(trans(LAB_REMARKS), $this->record['Noter']);
?>
</table>
</fieldset>
<?
?>
<fieldset class="detail-right">
<legend><? echo trans(LAB_AQV_MINIATURE);?></legend>
<?
echo '<img src="Agaricina/'.strtoupper($this->record['URLpic']).'" alt="" style="width:240px;"/>';
?>
</fieldset>
<?
HTML::divider(10);
?>
<fieldset class="detail-wide">
<legend><? echo trans(LAB_AQV_TEXT);?></legend>
<center>
<?
echo '<img src="Agaricina/'.strtoupper($this->record['URLprintpic']).'" alt="" style=""/>';
$p=pathinfo($this->record['URLtext']);
$url='Agaricina/FAD-TXT/'.strtoupper($p['filename'].'.'.$p['extension']);
echo '<img src="'.$url.'" alt="Tavle tekst" style=""/>';
?>
</center><br>
</fieldset>
<?
		HTML::divider(1);
		$this->drawCloseLink();
	}

	protected function getPageTitle() {
		return 'Tavle '.$this->FAD.' - Flora Agaricina Danica';
	}

}

?>
