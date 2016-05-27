<?
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('../common/Db.php');

class SaveTable extends Db {
	
	public function __construct() {
		parent::__construct();
		$this->save();
	}

	private function save() {
		$SQL='update '.$_POST['table'].' set ';
		foreach ($_POST as $field=>$value) {
			if ($field!='table' && $field!='where') {
				$SQL.=$field.='='.$this->q($value);
			}
		}
		$SQL=$this->removeLastChar($SQL);
		$SQL.=' where '.$_POST['where'];
		
		//echo $SQL;
		$this->fileDebug($SQL);

		mysql_set_charset('utf8');
		$this->query($SQL);
	}

}

$savetable = new SaveTable();

?>
