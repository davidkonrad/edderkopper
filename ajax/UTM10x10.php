<?

include('../common/Db.php');
include('../common/proxies.php');

class UTM10x10 extends Db {
	
	public function __construct() {
		parent::__construct();

		if ($this->testParam('Region')) {
			$this->getRegion();
		}
		if ($this->testParam('Egn')) {
			$this->getEgn();
		}
	}

	protected function testParam($param) {
		if (!isset($_GET[$param])) return false;
		if ($_GET[$param]=='') return false;
		return true;
	}

	private function getRegion() {
		$SQL='select Name from UTM10 where ';
		$where='';

		if ($this->testParam('Region')) {
			if ($where!='') $where.=' and ';
			$where.='Region='.$this->q($_GET['Region'], false);
		}
		$SQL.=$where;
		$result=$this->query($SQL);

		/*
		$JSON=Proxy::resultToJSON($result, 'Name', 'utm');
		echo $JSON;			
		*/
		foreach($result as $row) {
			print_r($row);
		}
	}

	private function getEgn() {
		$SQL='select Name from UTM10 where ';
		$where='';

		if ($this->testParam('Egn')) {
			if ($where!='') $where.=' and ';
			$where.='Egn='.$this->q($_GET['Egn'], false);
		}
		$SQL.=$where;
		$result=$this->query($SQL);
		$JSON=Proxy::resultToJSON($result, 'Name', 'utm');
		echo $JSON;			
	}

}

$utm=new UTM10x10();

?>
