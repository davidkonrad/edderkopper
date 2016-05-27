<?

include('../common/Db.php');

class createJSON extends Db {

	public function __construct() {
		parent::__construct();
		//$this->leg();
		$this->biller();
	}

	protected function leg() {
		$SQL='select distinct Leg from edderkopper order by Leg';
		mysql_set_charset('Latin1');
		$result=$this->query($SQL);
		$json='{ "lookup": [';
		while ($row = mysql_fetch_array($result)) {
			$leg=$row['Leg'];
			if ($leg!='') {
				//vertical tabs from Excel
				$leg= str_replace("\x0B", '', $leg);	
				$json.='"'.$leg.'",';
			}
		}
		$json=$this->removeLastChar($json);
		$json.='] }';
		echo $json;
	}

	protected function biller() {
		$SQL='select distinct taxon_name from billekatalog_taxon order by taxon_name';
		$result=$this->query($SQL);
		$json='{ "lookup": [';
		while ($row = mysql_fetch_array($result)) {
			$taxon=$row['taxon_name'];
			if ($taxon!='') {
				//vertical tabs from Excel
				$taxon= str_replace("\x0B", '', $taxon);	
				$json.='"'.$taxon.'",';
			}
		}
		$json=$this->removeLastChar($json);
		$json.='] }';
		echo $json;
	}

}

$json = new createJSON();

?>
