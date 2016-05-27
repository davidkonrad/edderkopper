<?

include('common/Db.php');
include('common/proxies.php');

class createJSON extends Db {

	public function __construct() {
		parent::__construct();
		//$this->lokalitet();
		//$this->taxon();
		//$this->dkname();
		//$this->fullYear(2012);
		//$this->billeLokalitet();
		//$this->billeFinder();
		$this->billeBestemmer();
	}

	private function billeLokalitet() {
		$SQL='select distinct lokalitet from billekatalog where lokalitet<>"" order by lokalitet';
		$result=$this->query($SQL);
		echo Proxy::resultToJSON($result, 'lokalitet', 'lokalitet');
	}

	private function billeFinder() {
		$SQL='select distinct leg from billekatalog where leg<>"" order by leg';
		$result=$this->query($SQL);
		echo Proxy::resultToJSON($result, 'leg', 'leg');
	}

	private function billeBestemmer() {
		$SQL='select distinct det from billekatalog where det<>"" order by det';
		$result=$this->query($SQL);
		echo Proxy::resultToJSON($result, 'det', 'det');
	}

	protected function lokalitet() {
		$SQL='select localitybasicString from atlasGaz order by localitybasicString';
		$result=$this->query($SQL);
		$json='{ "lookup": [';
		while ($row = mysql_fetch_array($result)) {
			if ($row['localitybasicString']!='') {
				$json.='"'.$row['localitybasicString'].'",';
			}
		}
		$json=$this->removeLastChar($json);
		$json.='] }';
		echo $json;
	}

	protected function taxon() {
		$SQL='select GenSpec, SubCat, SubSpecies from atlasTaxon order by GenSpec';
		$result=$this->query($SQL);
		$json='{ "lookup": [';
		while ($row = mysql_fetch_array($result)) {
			if ($row['GenSpec']!='') {
				$genspec=$row['GenSpec'];
				if ($row['SubCat']!='') $genspec.=' '.$row['SubCat'];
				if ($row['SubSpecies']!='') $genspec.=' '.$row['SubSpecies'];
				//$json.='"'.$row['GenSpec'].'",';
				$json.='"'.$genspec.'",';
			}
		}
		$json=$this->removeLastChar($json);
		$json.='] }';
		echo $json;
	}

	protected function dkname() {
		$SQL='select DKName from atlasTaxon order by DKName';
		$result=$this->query($SQL);
		$json='{ "lookup": [';
		while ($row = mysql_fetch_array($result)) {
			$s=str_replace('"', '', $row['DKName']);
			if ($s!='') {
				$json.='"'.ltrim($s).'",';
			}
		}
		$json=$this->removeLastChar($json);
		$json.='] }';
		echo $json;
	}

	protected function fullYear($year) {
		$SQL='select AtlasUserlati, AtlasUserLong, _UTM10 from atlasFund '.
			'where AtlasForumVali="Godkendt" and date_year='.$year;
		$result=$this->query($SQL);
		$json='';
		while ($row = mysql_fetch_array($result)) {
			$json.="\n".'{';
			$json.='"lat" : "'.$row['AtlasUserlati'].'",';
			$json.='"long" : "'.$row['AtlasUserLong'].'",';
			$json.='"utm" : "'.$row['_UTM10'].'"';
			$json.='}';
			$json.=',';
		}
		$json=$this->removeLastChar($json);
		$json='['.$json.']';
		echo $json;
	}

}

$json = new createJSON();

?>
		
			
			
