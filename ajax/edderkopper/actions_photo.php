<?
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../../common/Db.php');


class Photo extends Db {

	public function __construct() {
		parent::__construct();
		
		$action = $_GET['action'];
		switch ($action) {
			case 'list' :
				$this->getList();
				break;
			default :
				break;
		}
	}

	private function getList() {
$SQL = <<<SQL
		select 
			p.PhotoId, 
			p.SpeciesId, 
			p.SubjectDK,
			p.SubjectUK,
			p.Filename,
			concat(s.Genus, ' ', s.Species) as specie_name
		from
			edderkopper_photo p
		left join edderkopper_species s on s.SpeciesID = p.SpeciesId
SQL;
		$result=$this->queryJSON($SQL);
		echo json_encode($result);
	}
}

$photo = new Photo();

?>
