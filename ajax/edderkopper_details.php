<?

include('../common/Db.php');
include('../common/proxies.php');

//for now (test), current lang should somehow be parameterized
include('../lang/dansk.php');

class EdderkoppeDetails extends Db {

	public function __construct() {
		parent::__construct();
		if (!isset($_GET['LNR'])) return;
		$this->getDetails();
	}

	private function drawRow($caption, $value) {
		echo '<tr>';
		echo '<td class="caption">'.$caption.'</td>';
		echo '<td class="info">'.$value.'</td>';
		echo '</tr>';
	}

	private function getDetails() {
		$SQL='select * from edderkopper where LNR='.$_GET['LNR'];
		$this->setUtf8();
		$row=$this->getRow($SQL);

		echo '<table class="details">';
	
		$this->drawRow(LAB_NAME, '<em>'.$row['Name'].'</em>');
		$this->drawRow(LAB_AUTHOR, $row['AuthorYear']);

		//$this->drawRow(LAB_DATE, Proxy::formatDateDK($row['Date_first'], $row['Month_first'], $row['Year_first']));
		$fromDate = $row['Date_first']!='' 
			? Proxy::formatDateDK($row['Date_first'], $row['Month_first'], $row['Year_first']).'-<br>'
			: '';
		$date = Proxy::formatDateDK($row['Date_last'], $row['Month_last'], $row['Year_last']);

		$this->drawRow(LAB_DATE, $fromDate.$date);


		$this->drawRow(LAB_COLLECTION, $row['Collection']);
		$this->drawRow('Leg.', $row['Leg']);
		$this->drawRow('Det.', $row['Leg']);
		$this->drawRow(LAB_LOCALITY, $row['Locality']);
		$this->drawRow(LAB_LATLONG_SHORT, $row['LatPrec'].' / '.$row['LongPrec']);
		$this->drawRow('UTM10', $row['UTM10']);
		$this->drawRow(LAB_COLLECTION_NO, $row['KatalogNrPers']);
		$this->drawRow('#'.LAB_NO, $row['LNR']);

		/*
		??
		$redlist = ($row['Redlistlink']!='') ? '<a href="'.$row['Redlistlink'].'" target=_blank>'.$row['Redlistlink'].'</a>' : false;
		if ($redlist) $this->drawRow(LAB_REDLIST, $redlist);
		*/

		echo '</table>';
	}
}

$details = new EdderkoppeDetails();

?>
