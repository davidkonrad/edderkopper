<?
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('UNDEFINED', '%undefined%');
define('UPLOAD_PATH', '../../edderkopper-upload/');

/*
$columns = array(
	"LNR",
	"Family",
	"Genus",
	"Species",
	"AuthorYear",
	//"10year",
	//"26year",
	//"5year",
	"Collection",
	//"CurrentPlacement",
	"Date",
	"Det",
	"KatalogNrPers",
	"LatPrec",
	"Leg",
	"Locality",
	"LongPrec",
	//"Misc",
	"Month",
	"NotesF",
	"NotesonLoc",
	"NotesonSpecimen",
	"NotesonTube",
	"RadiusNew",
	"Region",
	//"SpecimenBased",
	"Specimens",
	"UTM10",
	//"UTM20",
	//"UTM50",
	//"WM",
	"Year",
	//"Yearspan",
	"MaleCount",
	"FemaleCount",
	"JuvenileCount"
	//"PrecMonth",
	//"Name"
);
*/

$columns = array(
	//"LNR",
	"Family",
	"Genus",
	"Species",
	"AuthorYear",
	"Collection",
	"KatalogNrPers",
	"Specimens",
	"Specimenbased",
	"MaleCount",
	"FemaleCount",
	"JuvenileCount",
	"Leg",
	"Det",
	"Date_first",
	"Month_first",
	"Year_first",
	"Date_last",
	"Month_last",
	"Year_last",
	"Region",
	"Locality",
	"LatPrec",
	"LongPrec",
	"RadiusNew",
	"NotesF",
	"NotesonLoc",
	"NotesonSpecimen",
	"NotesonTube",
	"UTM10"
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
		return $this->csv != UNDEFINED && file_exists(UPLOAD_PATH.$this->csv);
	}
	private function getCSV() {
		$this->csv = $_GET['csv'] != '' ? $_GET['csv'] : UNDEFINED;
	}
}

//truncate fund / table edderkopper 
class FundTruncate extends Db {
	public function __construct() {
		parent::__construct();
		$this->exec('truncate table edderkopper');
		echo 'Fund-tabellen er nulstillet ..';
	}
}

//insert to database
class Insert extends CSV {
	public function __construct() {
		parent::__construct();
		$this->run();
	}
	private function getStr($array, $quotes = false) {
		$str = '';
		foreach($array as $value) {
			$value=str_replace(array(
				"\t",   //tab.
				"\n",   //new line 
				"\r",   //return
				"\0",   //NUL-byte.
				"\x0B", //vertical tab.
				"ï",	//lissners mærkelige ï»¿ (BOM)
				"»",	//lissners mærkelige ï»¿
				"¿",	//lissners mærkelige ï»¿
				"\xEF", //BOM #2
				"\xBB", //BOM #2
				"\xBF" //BOM #2
				), '', $value);

			if ($str!='') $str.=',';
			if ($quotes) {
				$str.='"'.mysql_real_escape_string($value).'"';
			} else {
				$str.=str_replace('"', '', $value);
			}
		}
		return $str;
	}
	private function run() {
		global $columns;
		mysql_set_charset('utf8');
		$error = $this->csv.' er indsat i fund-tabellen ..';
		if (($handle = fopen(UPLOAD_PATH.$this->csv, "r")) !== false) {
			//read columns / first line
			$test = fgetcsv($handle, 1000, ";");

			//delete first column LNR
			$test = array_slice($test, 1);

			$fieldNames = '('.$this->getStr($test, false).')';

			$count=0;
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				//delete first column LNR
				$data = array_slice($data, 1);

				$values = $this->getStr($data, true);

				$SQL='insert into edderkopper '
					.$fieldNames
					.' values ('.$values.')';

				echo $SQL;
				
				$this->query($SQL);
				$count++;

				if (mysql_error()!='') {
					$error = '<span class="msg-error">'.mysql_error().'</span>';
					break;
				}

			}
		    fclose($handle);
			echo $error;
		}
	}
}

//delete
class Delete extends CSV { 
	public function __construct() {

		parent::__construct();
		$this->run();
	}
	private function run() {
		if ($this->CSV_exists()) {
			unlink(UPLOAD_PATH.$this->csv);
			echo $this->csv.' er slettet fra serveren.';
		} else {
			echo $this->csv.' kan ikke slettes.';
		}
	}
}

//check		
class Check extends CSV {
	public function __construct() {
		parent::__construct();
		$this->run();
	}
	private function run() {
		global $columns;
		if (($handle = fopen(UPLOAD_PATH.$this->csv, "r")) !== false) {
			$fields = fgetcsv($handle, 1000, ";");
			$err = false;
			foreach ($columns as $column) {
				if (!in_array($column, $fields)) {
					$err = true;
					echo '<b>'.$column.'</b> mangler. ';
				}
			}
		}
	    fclose($handle);
    }
}


/*****************************
	get specie by SpeciesID
*****************************/
class GetSpecies extends Db {
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		$SQL='select * from edderkopper_species where SpeciesID='.$_GET['SpeciesID'];
		$row=$this->getRow($SQL, true);
		echo json_encode($row);
	}
}

/*****************************
	lookup species with FullName
*****************************/
class LookupFullSpecies extends Db {
	private $search = '';
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->search = $_GET['search'];
		$this->run();
	}
	protected function getJSON($result) {
		$json='';
		while ($row = mysql_fetch_array($result)) {
			if ($json!='') $json.=',';
			$species = $row['Species'].', '.$row['Genus'].' '.$row['SAuthor'].' ['.$row['SpeciesID'].']';
			$json.='{"value" : "'.$species.'", "text": "'.$species.'"}';
		}
		$json='['.$json.']';
		return $json;
	}
	private function run() {
		header('Content-type: application/json; charset=utf-8');
		$SQL='select distinct s.SpeciesID, s.Species, s.SAuthor, g.Genus '.
			'from edderkopper_species s, edderkopper_genus g '.
			'where s.Species like "%'.$this->search.'%" and s.GenusID=g.GenusID '.
			'order by Species ';

		$result=$this->query($SQL);
		echo $this->getJSON($result);
	}
}

/*****************************
	update a Species
*****************************/
class updateSpecies extends Db {
	public function __construct() {
		parent::__construct();
		$this->run();
	}
	private function run() {
		mysql_set_charset('utf8');
		$SQL='update edderkopper_species set ';
		foreach($_GET as $key => $value) {
			if (!in_array($key, array('SpeciesID', 'action'))) {
				$SQL.=$key.'='.$this->q($value);
			}
		}
		$SQL=$this->removeLastChar($SQL);
		$SQL.=' where SpeciesID='.$_GET['SpeciesID'];

		$this->exec($SQL);
		$updateError = mysql_error();


		//update Genus on all Species
		$SQL='update edderkopper_species set edderkopper_species.Genus = ' .
					'(select edderkopper_genus.Genus from edderkopper_genus ' .
						'where edderkopper_genus.GenusID = edderkopper_species.GenusID)';
		$this->exec($SQL);

		$updateError .= mysql_error();

		echo $updateError!='' ? $updateError : 'Ændringerne er blevet gemt ...';
	}
}


//lookup genus with full names
class LookupFullGenus extends Db {
	private $search = '';
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->search = $_GET['search'];
		$this->run();
	}
	protected function getJSON($result) {
		$array = array();
		while ($row = mysql_fetch_array($result)) {
			$array[] = array(
				'GenusID' => $row['GenusID'],
				'Genus' => $row['Genus'],
				'FullName' => $row['Genus'].', '.$row['Family'].' ('.$row['GAuthor'].') ['.$row['GenusID'].']'
			);
		}
		return json_encode($array);
	}
	private function run() {
		header('Content-type: application/json; charset=utf-8');
		$SQL='select distinct s.GenusID, s.Genus, s.GAuthor, g.Family '.
			'from edderkopper_genus s, edderkopper_family g '.
			'where s.Genus like "%'.$this->search.'%" and s.FamilyID=g.FamilyID '.
			'order by Genus ';

		$result=$this->query($SQL);
		echo $this->getJSON($result);
	}
}

//count of fund
class FundCount extends Db {
	public function __construct() {
		parent::__construct();
		$this->run();
	}
	private function run() {
		$SQL='select max(LNR) as m from edderkopper';
		$row=$this->getRow($SQL);
		return $row['m'];
	}
}

/*****************************
	get fund by LNR
*****************************/
class FundGet extends Db {
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		$SQL='select * from edderkopper where LNR='.$_GET['LNR'];
		$row=$this->getRow($SQL, true);
		echo json_encode($row);
	}
}

/*****************************
	update fund 
*****************************/
class FundSave extends Db {
	public function __construct() {
		parent::__construct();
		$this->run();
	}
	private function run() {
		mysql_set_charset('utf8');
		$SQL='update edderkopper set ';
		foreach($_GET as $key => $value) {
			if (!in_array($key, array('LNR', 'action'))) {
				$SQL.=$key.'='.$this->q($value);
			}
		}
		$SQL=$this->removeLastChar($SQL);
		$SQL.=' where LNR='.$_GET['LNR'];
		$this->exec($SQL);
		$updateError = mysql_error();

		//update Name
		$SQL='update edderkopper set Name=Concat(Genus," ",Species) where LNR='.$_GET['LNR'];
		$this->exec($SQL);

		echo $updateError!='' ? $updateError : 'Ændringerne er blevet gemt ...';
	}
}

//update fund Name ALL
class FundUpdateNameAll extends Db {
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		//update Name
		$SQL='update edderkopper set Name=Concat(Genus," ",Species)';
		$this->exec($SQL);
	}
}

//update fund Genus and Species
class FundUpdateSpeciesName extends Db {
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		$oldSpecies = $_GET['oldspecies'];
		$oldGenus = $_GET['oldgenus'];
		$newSpecies = $_GET['newspecies'];
		$newGenus = $_GET['newgenus'];

		$SQL='update edderkopper set '.
			'Species='.$this->q($newSpecies).
			'Genus='.$this->q($newGenus, false).
			' where '.
			'Species='.$this->q($oldSpecies, false).' and '.
			'Genus='.$this->q($oldGenus, false);

		echo $SQL;

		$this->fileDebug($SQL);
		$this->exec($SQL);
	}
}

//delete fund
class FundDelete extends Db {
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		$LNR = isset($_GET['lnr']) ? $_GET['lnr'] : false;
		if ($LNR) {
			$SQL='delete from edderkopper where LNR='.$LNR;
			$this->exec($SQL);
		}
	}			
}

//get all species with specie, genus, family names and ids
class GetTaxonomy extends Db {
	private $search = '';
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		header('Content-type: application/json; charset=utf-8');

		$SQL= 'select distinct s.SpeciesID, g.GenusID, f.FamilyID, s.Species, g.Genus, f.Family '.
			'from edderkopper_species s, edderkopper_genus g, edderkopper_family f '.
			'where s.GenusID=g.GenusID and f.FamilyID=g.FamilyID ';

		$result=$this->query($SQL);
		$json='';
		while ($row = mysql_fetch_assoc($result)) {
			if ($json!='') $json.=',';
			$json.=json_encode($row);
		}
		echo '['.$json.']';
	}
}

//create new specie
class CreateSpecie extends Db {
	public function __construct() {
		parent::__construct();
		mysql_set_charset('utf8');
		$this->run();
	}
	private function run() {
		$specie = $_GET['specie'];
		
		$SQL='select max(SpeciesID)+1 as id from edderkopper_species';
		$row=$this->getRow($SQL);
		$id=$row['id'];

		$SQL='insert into edderkopper_species (SpeciesID, Species) values('.
			$id.','.
			$this->q($specie, false).
			')';

		$this->exec($SQL);
		echo $id;
	}
}		
	
	
$action = $_GET['action'];


switch ($action) {
	case 'truncate' :
		$create = new FundTruncate();
		break;
	case 'insert': 
		$insert = new Insert();
		break;
	case 'check': 
		$check = new Check();
		break;
	case 'delete' : 
		$delete = new Delete();
		break;

	//species
	case 'lookupSpecies' :
		$lookup = new LookupFullSpecies();
		break;
	case 'getSpecies' :
		$lookup = new GetSpecies();
		break;
	case 'updateSpecies' :
		$lookup = new updateSpecies();
		break;

	//genus	
	case 'lookupGenus' :
		$lookupGenus = new LookupFullGenus();
		break;

	//fund
	case 'fundcount' :
		$fundcount = new FundCount();
		break;
	case 'fundsave' :
		$fundsave = new FundSave();
		break;
	case 'funddelete' :
		$funddelete = new FundDelete();
		break;
	case 'fundGet' :
		$fundGet = new FundGet();
		break;


	case 'taxonomy' :
		$taxonomy = new GetTaxonomy();
		break;
	case 'fundupdatespeciesname' :
		$fundupdatespeciesname = new FundUpdateSpeciesName();
		break;
	case 'fundupdatenameall' :
		$updatename = new FundUpdateNameAll();
		break;
	case 'createspecie' :
		$create = new CreateSpecie();
		break;

	default :
		break;
}
			
?>
