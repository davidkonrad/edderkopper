<?

include('../common/Db.php');

class Art extends Db {

	public function __construct() {
		parent::__construct();

		mysql_set_charset('utf8');

		$action = isset($_POST['action']) ? $_POST['action'] : false;
		switch ($action) {
			case 'get' :
				$this->get();
				break;
			case 'put' :
				$this->put();
				break;
			default:
				break;
		}
	}

	private function get() {
		$id = $_POST['id'];
		$SQL='select * from edderkopper_species where SpeciesID='.trim($id);
		$row = $this->getRow($SQL, true);
		echo json_encode($row);
	}

	private function put() {
	}

}

$art = new Art();

?>
