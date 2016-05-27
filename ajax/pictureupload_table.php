<?

include('SearchBase.php');

class PictureUploadTable extends SearchBase {
	private $result;

	public function __construct() {
		parent::__construct();
		$this->getRecords();
		$this->drawTable();
	}

	private function getRecords() {
		$SQL='select id, timestamp_, FileDir, UploadPerson, CatalogNumber, Notes, '.
			'(select count(*) from picture_upload_files where upload_id=picture_upload.id) as filer '.
			'from picture_upload ';
		//mysql_set_charset('utf8');
		$this->result=$this->query($SQL);
	}

	private function drawTable() {
		echo '<table id="result-table">';
		echo '<thead><tr>';
		echo '<th>Timestamp</th>';
		echo '<th>Person</th>';
		echo '<th>CatalogNumber</th>';
		echo '<th>#</th>';
		echo '<th>Noter</th>';
		echo '<th>daim katalog</th>';
		echo '</tr></thead>';
		echo '<tbody>';
		while ($row = mysql_fetch_assoc($this->result)) {
			echo '<tr>';
			echo '<td>'.$row['timestamp_'].'</td>';
			echo '<td>'.utf8_decode($row['UploadPerson']).'</td>';
			echo '<td>'.$row['CatalogNumber'].'</td>';
			echo '<td>'.$row['filer'].'</td>';
			echo '<td>'.$row['Notes'].'</td>';
			echo '<td><a href="pictureupload/'.$row['FileDir'].'">'.$row['FileDir'].'</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
}

$upload = new PictureUploadTable();

?>
