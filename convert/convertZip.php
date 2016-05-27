<?
include('convertBase.php');

//convert svampedata.mer =>data
class Zip extends ConvertBase {

	public function run() {
		$this->loadCSV();
		echo $this->getCreateTableSQL('zip') ;
	}
}

$zip = new Zip('zip/free-zipcode-database-Primary.csv');
$zip->run();


?>


