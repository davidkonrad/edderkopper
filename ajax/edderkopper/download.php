<?
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../../common/Db.php');


class Download extends Db {

	public function __construct() {
		parent::__construct();
		$this->download();
	}

	private function download() {
		//header('Content-type: application/text');
		header('Content-Type: application/text; charset=utf-8');
		header('Content-Disposition: attachment; filename="edderkopper.csv"');

		$SQL='select * from edderkopper';
		mysql_set_charset('utf8');
		$result = $this->query($SQL);

		//output header
		$line = '';
		$row = mysql_fetch_assoc($result);
		foreach($row as $key=>$value) {
			$line = $line != '' ? $line.=';' : $line;
			$line.='"'.$key.'"';
		}
		echo $line."\n";		

		//output rows
		mysql_data_seek($result, 0);
		while ($row = mysql_fetch_assoc($result)) {
			$line = '';
			foreach($row as $key=>$value) {
				$line = $line != '' ? $line.=';' : $line;
				$line.='"'.$value.'"';
			}
			echo $line."\n";
		}

		//header('location : edderkopper-administration');
	}

}

$download = new Download();

?>
