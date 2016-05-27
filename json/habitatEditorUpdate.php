<?
include('../common/Db.php');

class Update extends Db {

	public function __construct() {
		parent::__construct();
		
		$id=(isset($_GET['id'])) ? $_GET['id'] : false;
		$habitat=(isset($_GET['habitat'])) ? $_GET['habitat'] : false;
		$ef_habitat_id=(isset($_GET['ef_habitat_id'])) ? $_GET['ef_habitat_id'] : false;

		if (!$id || !$habitat) return;

		$SQL='select count(*) as c from habitat_kmlid_navne where id='.$id;
		echo $SQL.'<br>';
		$row=$this->getRow($SQL);
		
		if ($row['c']>0) {
			$SQL='update habitat_kmlid_navne set '.
					'navn="'.$habitat.'", '.
					'ef_habitat_id="'.$ef_habitat_id.'" '.
					'where id='.$id;
			$this->exec($SQL);
		} else {
			$SQL='insert into habitat_kmlid_navne set '.
				'id="'.$id.'", '.
				'navn="'.$habitat.'", '.
				'ef_habitat_id="'.$ef_habitat_id.'"';
			$this->exec($SQL);
		}

		//echo $SQL;
		header('location: habitatEditor.php');
	}

}

$update = new Update();

?>
