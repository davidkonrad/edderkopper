<?

include('../common/Db.php');

class EdderkopperFund extends Db {
	private $species;
	
	public function __construct() {
		parent::__construct();
		if (isset($_GET['species'])) {
			$this->species=$_GET['species'];
		} else {
			return; //
		}
		$this->createJSON();		
	}

	private function createJSON() {
		//$SQL='select UTM10, LNR, LatPrec, LongPrec, Year_first from edderkopper where Name="'.$this->species.'"';
		$SQL='select UTM10, LNR, LatPrec, LongPrec, Year_last from edderkopper where Name="'.$this->species.'"';
		$result=$this->query($SQL);
		$json='';
		while ($row = $result->fetch()) {
			if ($json!='') $json.=',';
			$utm=$row['UTM10'];			
			$lnr=$row['LNR'];			
			$lat=$row['LatPrec'];
			$long=$row['LongPrec'];
			$year=$row['Year_last'];
			$json.='{ "utm" : "'.$utm.'", "lnr" : "'.$lnr.'", "lat" : "'.$lat.'", "long": "'.$long.'", "year" : "'.$year.'" }';
		}
		$json='['.$json.']';
		echo $json;
	}

}

$fund = new EdderkopperFund();

?>
