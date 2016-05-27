<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PageFloraDanicaDetail extends TemplateDetail {
	private $auto_id;
	private $record;

	public function __construct() {
		$this->auto_id=$_GET['auto_id'];
		parent::__construct();
	}
	
	private function getRecord() {
		mysql_set_charset('utf8');
		$SQL='select * from floradanica where auto_id="'.$this->auto_id.'"';
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function getTextImgPath($fasc_code, $roman) {
		return 'FloraDanica/TEXT/'.$fasc_code.'/'.strtoupper($roman).'.jpg';
	}

	protected function drawBody() {
		$this->getRecord();

?>
<fieldset class="detail-left">
<legend>Flora Danica - <? echo $this->record['Volume'];?></legend>
<table>
<?
$this->drawRow(trans(LAB_FASCICLE_CODE) ,$this->record['Fascicle_code']);
$this->drawRow('Volume ',$this->record['Volume']);
//$this->drawRow('Fascicle_number ',$this->record['Fascicle_number']);
$this->drawRow(trans(LAB_PLATE_NO), $this->record['Plate_Arabic']);
$this->drawRow(trans(LAB_PLATE_ROMAN), $this->record['Plate_Roman']);
//$this->drawRow('Plate_Arabic ',$this->record['Plate_Arabic']);
$this->drawRow(trans(LAB_FASCICLE_EDITOR), $this->record['Fasc_editor']);
$this->drawRow(trans(LAB_FASCICLE_YEAR), $this->record['Fasc_year']);
$this->drawRow(trans(LAB_NAME_ORIGINAL), $this->record['OriginalName']);
$this->drawRow(trans(LAB_AUTHOR_ORIGINAL), $this->record['original_author']);
//$this->drawRow('DanishNameFD ',$this->record['DanishNameFD']);
//$this->drawRow('CurrName ',$this->record['CurrName']);
//$this->drawRow('new_author ',$this->record['new_author']);
$this->drawRow(trans(LAB_CURR_FULLNAME), $this->record['CurrFullName']);
//$this->drawRow('CurrDanishName ',$this->record['CurrDanishName']);
//$this->drawRow('AllDKNames ',$this->record['AllDKNames']);
$this->drawRow(trans(LAB_ALL_NAMES), $this->record['AllNames']);
//$this->drawRow('Drawer ',$this->record['Drawer']);
//$this->drawRow('Editor ',$this->record['Editor']);
$this->drawRow(trans(LAB_GROUP), $this->record['group']);
//$this->drawRow('groups_danish ',$this->record['groups_danish']);
$this->drawRow(trans(LAB_LANGE_NAME), $this->record['LangeName']);
$this->drawRow(trans(LAB_LANGE_FULLINFO), $this->record['LangeFullInfo']);
//$this->drawRow('Taxon ',$this->record['Taxon']);
//$this->drawRow('Comments_danish ',$this->record['Comments_danish']);
//$this->drawRow('Comments_english ',$this->record['Comments_english']);
?>
</table>
</fieldset>
<fieldset class="detail-right">
<legend><? echo trans(LAB_AQV_MINIATURE);?></legend>
<center>
<?
echo '<img src="'.$this->record['URLsmalPic'].'" alt="Flora Danica" style="margin-left:auto;margin-right:auto;width:240px;"/>';
?>
</center>
</fieldset>
<fieldset class="detail-wide"">
<legend>Akvarel / tegning og tekst</legend><center>
<?
echo '<img src="'.$this->record['URLstorPic'].'" alt="Flora Danica" style="width:880px;"/>';
echo '<br/><br/><img src="'.$this->getTextImgPath($this->record['Fascicle_code'], $this->record['Plate_Roman']).'" alt="Flora Danica" style="margin-left:auto;margin-right:auto;"/>';
?>
</center></fieldset>
<?
		HTML::divider(1);
		$this->drawCloseLink();
	}

	protected function getPageTitle() {
		return 'Flora Danica - #'.$this->auto_id;
	}

}

?>
