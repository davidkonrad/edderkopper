<?
include('../common/Db.php');

class Upload extends Db {
	private $path = '../pictureupload/';
	private $folder;
	private $folderId;
	private $catalognumber;
	private $FileDir;

	public function __construct() {
		parent::__construct();
		$this->catalognumber=$_POST['catalognumber'];
		$this->getFolder();
		$this->insertRecord();
		$this->uploadFiles();
		$this->htaccess();
		header('location: ../upload-billeder?id='.$this->folderId);
	}

	private function htaccess() {
		file_put_contents($this->folder.'/.htaccess', 'Options +Indexes');
	}

	private function getFolder() {
		$SQL='insert into picture_upload () values()';
		$this->exec($SQL);
		$this->folderId=mysql_insert_id();

		$today=getdate();
		$date=$today['year'].'-'.$today['mon'].'-'.$today['mday'];
		$this->FileDir=$date.'_'.$this->folderId;
		$this->folder=$this->path.$this->FileDir;
		mkdir($this->folder);
		
		//echo $this->folder;
	}

	private function insertRecord() {
		$SQL='update picture_upload set '.
			'CatalogNumber='.$this->q($this->catalognumber).
			'CollectionID='.$this->q($_POST['collection']).
			'Photographer='.$this->q($_POST['photographer']).
			'UploadPerson='.$this->q($_POST['uploadperson']).
			'FileDir='.$this->q($this->FileDir).
			'Date='.$this->q($_POST['date']).
			'Notes='.$this->q($_POST['notes'], false).' '.
			'where id='.$this->folderId;
		$this->exec($SQL);
	}

	private function insertFile($fileName, $originalFilename) {
		$SQL='insert into picture_upload_files (upload_id, filename, original_filename) values('.
			$this->q($this->folderId).
			$this->q($fileName).
			$this->q($originalFilename, false).
			')';
		$this->exec($SQL);
	}		

/*
	private function uploadFiles() {	
		for($i=0; $i<count($_FILES['photos']['name']); $i++) {
			$tmpFilePath = $_FILES['photos']['tmp_name'][$i];
			if ($tmpFilePath != "") {
				$ext = pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION);
				$id=$i+1;
				$filename=$this->catalognumber.'_'.$id.'.'.$ext;;
				$name=$this->folder.'/'.$filename;
				move_uploaded_file($tmpFilePath, $name);
				$this->insertFile($filename, $_FILES['photos']['name'][$i]);
			}
		}
	}
*/

	private function uploadFiles() {	
		for($i=0; $i<count($_FILES['photos']['name']); $i++) {
			$tmpFilePath = $_FILES['photos']['tmp_name'][$i];
			if ($tmpFilePath != "") {
				$ext = pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION);
				$originalFilename = pathinfo($_FILES['photos']['name'][$i], PATHINFO_FILENAME);

				if (strpos($originalFilename, $this->catalognumber) === false) {
					$filename=$this->catalognumber.'_'.$originalFilename.'.'.$ext;;
				} else {
					$filename=$originalFilename.'.'.$ext;;
				}

				$name=$this->folder.'/'.$filename;
				move_uploaded_file($tmpFilePath, $name);
				$this->insertFile($filename, $originalFilename);
			}
		}
	}

}

$upload = new Upload();

?>
