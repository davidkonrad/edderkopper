

<?

include('common/Db.php');

class T extends Db {

	public function __construct() {
		parent::__construct();

		$SQL='Insertt into test (test) values ("test")';
		$result=$this->query($SQL);
		
		if ($result) {
			echo 'OK';
		}
	}

}


$t= new T();

?>
