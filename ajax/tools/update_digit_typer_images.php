<?

include('../../common/Db.php');

class UpdateDigitTyperImages extends Db {
	
	public function __construct() {
		parent::__construct();
		$target=(isset($_GET['target'])) ? $_GET['target'] : false;
		switch ($target) {
			case 'fileuri' :
				$this->fileuri();
				break;
			case 'datacode' :
				$this->datacode();
				break;
			default :
				echo 'Der opstod en fejl';
				break;
		}
	}

	private function datacode() {
		$count=0;
		$SQL='select auto_id, id from digit_typer_images';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			$SQL='select DataCode from digit_typer where CatalogNumber="'.$row['id'].'"';
			$datacode=$this->getRow($SQL);
			$SQL='update digit_typer_images set datacode="'.$datacode[0].'" where auto_id='.$row['auto_id'];
			$this->exec($SQL);
			$count++;
		}
		echo $count.' <code>datacode</code> opdateret.';
	}

	private function fileuri() {
		$count=0;
		$SQL='select auto_id, id, filename from digit_typer_images';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			$SQL='select DataCode from digit_typer where CatalogNumber="'.$row['id'].'"';
			$datacode=$this->getRow($SQL);
			$SQL='update digit_typer_images set FileURI="'.
				'http://digit.snm.ku.dk/www/'.$datacode[0].'/thumb/'.$row['filename'].'" '.
				'where auto_id='.$row['auto_id'];
			$this->exec($SQL);
			$count++;
		}
		echo $count.' <code>FileURI</code> opdateret.';
	}

}

$update = new UpdateDigitTyperImages();

?>
