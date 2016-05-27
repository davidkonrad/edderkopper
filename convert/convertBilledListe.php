<?

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('../common/Db.php');

class Convert extends Db {
	protected $filename = 'billedlister/typer1.txt';

	public function __construct() {
		parent::__construct();
		ini_set("auto_detect_line_endings", true);
		$this->doConvert();
	}

	protected function doConvert() {
		$handle = @fopen($this->filename, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$a=explode(' ',$buffer);
				$name=$a[count($a)-1];
				$name=rtrim($name);
				$SQL='insert into digit_typer_images values ("'.$name.'")';
				echo $SQL;
				$this->query($SQL);
			}
			fclose($handle);
		}
	}
}

$convert = new Convert();

?>		

