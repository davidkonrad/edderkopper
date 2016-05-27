<?

include('common/Db.php');

class Test extends Db {

	public function __construct() {
		parent::__construct();

		$SQL='select * from  floradanica';
		$result=$this->query($SQL);

		while ($row = mysql_fetch_assoc($result)) {
		//foreach($result as $row) {
			print_r($row);
		}
	}
}

$test = new Test();

?>
