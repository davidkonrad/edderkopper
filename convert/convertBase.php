<?

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('../common/Db.php');

class convertBase extends Db {
	protected $CSVFile = ''; // 
	protected $delimiter = ';';
	protected $fieldNames = array();
	protected $records = array();
	
	public function __construct($CSVFile) {
		parent::__construct();
		ini_set("auto_detect_line_endings", true);
		$this->CSVFile=$CSVFile;
	}

	protected function detectDelimiter() {
		$handle = @fopen($this->CSVFile, "r");
		if ($handle) {
			$line=fgets($handle, 4096);
			fclose($handle);			

			$test=explode(',', $line);
			if (count($test)>1) return ',';

			$test=explode(';', $line);
			if (count($test)>1) return ';';
		}
		return $this->delimiter;
	}
		
	protected function arrVal($array, $name) {
		return (isset($array[$name])) ? $array[$name] : '';
	}

	protected function loadCSV() {
		if (($handle = fopen($this->CSVFile, "r")) !== false) {
			$this->fieldNames = fgetcsv($handle, 20000, $this->delimiter);
			//$this->debug($this->fieldNames);
			while (($record = fgetcsv($handle, 20000, $this->delimiter)) !== false) {
				$array = array();
				$index = 0;
				foreach ($this->fieldNames as $fieldName) {
					$array[$fieldName] = $record[$index];
					$index++;
				}
				$this->records[]=$array;
				//$this->debug($array);
			}
		}
	}

	//encoding / decoding helpers
	protected function asciiToUTF8($arr) {
	}

	//generates a simple 'create table xxx based on field names
	protected function getCreateTableSQL($tablename) {
		$count=0;
		$SQL='create table '.$tablename.'(';
		foreach ($this->fieldNames as $fieldName) {
			$SQL.=$fieldName.' varchar(20)';
			$count++;
			if ($count<count($this->fieldNames)) $SQL.=',';
			$SQL.="\n";
		}
		$SQL.=')';
		return $SQL;
	}

				
}

?>
