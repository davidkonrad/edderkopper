<?

class Upload {
	private $returnTo;

	public function __construct() {
		if (isset($_GET['delete'])) {
			$this->returnTo = (isset($_GET['return-to'])) ? '../'.$_GET['return-to'] : '../index.php';		
			$this->doDelete();
		} else {
			$this->returnTo = (isset($_POST['return-to'])) ? '../'.$_POST['return-to'] : '../index.php';		
			$this->doUpload();
		}

		header('location: '.$this->returnTo);
	}

	protected function doUpload() {
		if ($_FILES["upload-file"]["error"] > 0) return;

		//define ('SITE_ROOT', realpath(dirname(__FILE__)));
		//move_uploaded_file($_FILES['file']['temp_name'], SITE_ROOT.'/static/images/slides/1/1.jpg');

		$path='../resources/'.$_FILES["upload-file"]["name"];
		move_uploaded_file($_FILES["upload-file"]["tmp_name"], $path);

		$host = $_SERVER["SERVER_ADDR"]; 
		if (($host=='127.0.0.1') || ($host=='::1')) {
			$realpath='http://localhost/samlinger/resources/'.$_FILES["upload-file"]["name"];
		} else {
			$realpath='http://daim.snm.ku.dk/resources/'.$_FILES["upload-file"]["name"];
		}

	}

	protected function doDelete() {
		$file=$_GET['file'];
		$path='../resources/'.$file;
		unlink($path);
	}
		
}

$upload = new Upload();

?>
