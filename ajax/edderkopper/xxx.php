adadsadasd
<?
echo 'ok';

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('UNDEFINED', '%undefined%');
define('UPLOAD_PATH', '../../edderkopper-upload/');

$columns = array(
	"LNR",
	"Family",
	"Genus",
	"Species",
	"AuthorYear",
	"10year",
	"26year",
	"5year",
	"Collection",
	"CurrentPlacement",
	"Date",
	"Det",
	"KatalogNrPers",
	"LatPrec",
	"Leg",
	"Locality",
	"LongPrec",
	"Misc",
	"Month",
	"NotesF",
	"NotesonLoc",
	"NotesonSpecimen",
	"NotesonTube",
	"RadiusNew",
	"Region",
	"SpecimenBased",
	"Specimens",
	"UTM10",
	"UTM20",
	"UTM50",
	"WM",
	"Year",
	"Yearspan",
	"MaleCount",
	"FemaleCount",
	"JuvenileCount",
	"PrecMonth"
);

include('../../common/Db.php');

//base class for all actions
class CSV extends Db {
	public $csv = '';
	
	public function __construct() {
		parent::__construct();
		$this->getCSV();
	}
	public function CSV_exists() {
		return $this->csv != UNDEFINED && file_exist(PATH.$this->csv);
	}
	private function getCSV() {
		$this->csv = $this->param('csv') != '' ? $this->param('csv') : UNDEFINED;
	}
}
	
//insert to database
class Insert extends CSV {

	public function __construct() {
		parent::__construct();
		$this->run();
	}

	private function run() {
	}

}

//delete
class Delete extends CSV { 
		
	public function __construct() {
		parent::__construct();
		$this->run();
	}

	private function run() {
		echo UPLOAD_PATH.$this->csv;
		//unlink(UPLOAD_PATH.$this->csv);
		echo $this->csv.' er slettet fra serveren.';
	}
}

//check		
class Check {

	public function __construct() {
		$this->run();
	}

	private function run() {
	}
}


$action = $_GET['action'];
echo $action;

switch ($action) {
	case 'insert': 
		$insert = new Insert();
		break;
	case 'check': 
		break;
	case 'delete' : 
		$delete = new Delete();
		break;
	}	
	default :
		break;
}
			
?>
