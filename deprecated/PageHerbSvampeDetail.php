<?

class PageHerbSvampeDetail extends TemplateDetail {
	private $LNR;
	private $record;

	public function __construct() {
		$this->LNR=$_GET['id'];
		parent::__construct();
	}
	
	private function getRecord() {
		mysql_set_charset('utf8');
		$SQL='select distinct taxon.DKName as DKName, taxon.FULLNAME, data.land, data.LOK, data.DISTR, '.
			'data.dato_day, data.dato_month, data.dato_year, data.NO, data.UTM, data.HAB, data.GRUPPE, '.
			'(select person.LEGIT from herbsvampe_person person, herbsvampe_data2 data where person.LEGKODE=data.LEGKODE and data.LNR='.$this->LNR.') as indsamler, '.
			'(select person.LEGIT from herbsvampe_person person, herbsvampe_data2 data where person.LEGKODE=data.DETKODE and data.LNR='.$this->LNR.') as bestemmer '.
			'from herbsvampe_data2 data, herbsvampe_person person, herbsvampe_taxon taxon '.
			'where data.LNR='.$this->LNR.' '.
			'and taxon.ARTKODE=data.ARTKODE';
		
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$this->record=$row;
		}
	}

	protected function drawBody() {
		$this->getRecord();

?>
<fieldset class="detail-left">
<legend>Svampeherbariet</legend>
<table>
<?
if ($this->record['DKName']!='') {
	echo '<tr><td colspan="2">';
	HTML::h3($this->record['DKName']);
	echo '</td></tr>';
}
?>
<tr><td colspan="2">
<?
HTML::h3($this->record['FULLNAME']);
?>
</td></tr>
<?
$this->drawRow(trans(LAB_COUNTRY), $this->record['land']);
$this->drawRow(trans(LAB_LOCALITY), $this->record['LOK']);
$this->drawRow(trans(LAB_DATE), $this->record['dato_day'].'/'.$this->record['dato_month'].'/'.$this->record['dato_year']);
$this->drawRow(trans(LAB_COLLECTION_NO), $this->record['NO']);
$this->drawRow(trans(LAB_COLLECTOR), $this->record['indsamler']);
$this->drawRow(trans(LAB_DETERMINATOR), $this->record['bestemmer']);
$this->drawRow('UTM',$this->record['UTM']);
$this->drawRow(trans(LAB_HABITAT), $this->record['HAB']);
$this->drawRow('Herbarienr.', 'C-F-'.$this->LNR);
$this->drawRow(trans(LAB_GROUP), $this->idToSvampeGruppe($this->record['GRUPPE']));
?>
</table>
</fieldset>

<fieldset class="detail-right">
<legend>Kort, lokalitet</legend>
<center>
<?
echo '<img src="map/'.$this->record['DISTR'].'.gif" alt="Kort - findested"/>';
?>
</center>
</fieldset>
<?
	HTML::divider(1);
	$this->drawCloseLink();
	}

	private function idToSvampeGruppe($id) {
		switch ($id) {
			case 1 : return 'Phycomycetes'; break;
			case 2 : return 'Discomycetes'; break;
			case 3 : return 'Pyrenomycetes'; break;
			case 4 : return 'Protascomycetes'; break;
			case 5 : return 'Laboulbeniomycetes'; break;
			case 6 : return 'Aphyllophorales'; break;
			case 7 : return '"Agaricales" (incl. Polyporus)'; break;
			case 8 : return 'Gastromycetes'; break;
			case 9 : return 'Heterobasidiomycetes'; break;
			case 10: return 'Rust fungi'; break;
			case 11: return 'Smut fungi'; break;
			case 12: return 'Myxomycetes';
			case 13: return 'Mycelia sterilia-anamorphs'; break;
			case 14: return 'Other anamorphs'; break;
			case 15: return 'Bacteriae'; break;
			default : return 'Ukendt'; break;
		}
	}

	protected function getPageTitle() {
		return 'Dataark C-F-'.$this->LNR.' - Botanical Museum, University of Copenhagen';
	}

}

?>
