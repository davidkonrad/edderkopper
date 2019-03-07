<?
ini_set('display_errors', '1');
error_reporting(E_ALL);

include('../common/Db.php');

class Checklist extends Db {
	private $distrikter=array('SJ','EJ','WJ','NWJ','NEJ','F','LFM','SZ','NWZ','NEZ','B');
	private $pdf='';
	private $filename = '../edderkopper-upload/tjekliste/tjekliste.html';

	public function __construct() {
		parent::__construct();

		$action=$_GET['action'];
		switch ($action) {
			case 'get' : 
				$this->getChecklist();
				break;
			case 'create' :
				$this->createChecklist();
				$this->MPDF();
				break;
			/*
			fremover genereres PDF'en een gang 
			case 'pdf' :
				$this->MPDF();
				break;
			*/
			default :
				break;
		}
	}

	private function getChecklist() {
?>
<style>
.rotate {
  -webkit-transform: rotate(-90deg);
  -moz-transform: rotate(-90deg);
  -ms-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  transform: rotate(-90deg);

  //also accepts left, right, top, bottom coordinates; not required, but a good idea for styling 
  -webkit-transform-origin: 50% 50%;
  -moz-transform-origin: 50% 50%;
  -ms-transform-origin: 50% 50%;
  -o-transform-origin: 50% 50%;
  transform-origin: 50% 50%;

  //Should be unset in IE9+ I think.
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}
td.border {
	border: 1px solid #dadada;
	text-align: center;
	padding: 0;
	margin: 0;
}
td.name {
	border-top : 1px solid #dadada;
	border-bottom : 1px solid #dadada;
}
</style>
<?
		$html = file_get_contents($this->filename);
		echo $html;
	}

	private function createCheckList() {
		$this->pdf.='<table cellpadding="0" style="border-spacing:0px;" id="table-checkliste">';

		$this->pdf.='<thead><tr>';
		$this->pdf.= '<th></th>';
		foreach ($this->distrikter as $d) $this->pdf.= '<th style="width:30px;"></th>';
		$this->pdf.= '</tr></thead>';

		$this->pdf.= '<tbody>';

		/*
		original lodretstående regioner
		$this->pdf.= '<tr><td></td>';
		foreach ($this->distrikter as $district) {
			$this->pdf.= '<td style="vertical-align:top;"><div class="rotate" style="vertical-align:top;width:30px;">'.$district.'</div></td>';
		}
		$this->pdf.= '</tr>';
		*/

		$this->makeList();

		$this->pdf.= '</tbody>';
		$this->pdf.= '</table>';
	
		file_put_contents($this->filename, $this->pdf);
	}	
		
	private function MPDF() {
		require_once '../mPDF61/vendor/autoload.php';
		$stylesheet = file_get_contents('../css/edderkopper_checkliste.css');
		$mpdf = new mPDF();
		$mpdf->allow_charset_conversion = true; //
		$mpdf->debug = false; //
		$mpdf->WriteHTML($stylesheet, 1);

		$html = file_get_contents($this->filename);
		$mpdf->WriteHTML($html);
		$mpdf->Output('../edderkopper-upload/tjekliste/tjekliste.pdf','F');
	}

	private function cleanPDF() {
		$this->pdf=str_replace('<td','<span',$this->pdf);
		$this->pdf=str_replace('</td','</span',$this->pdf);

		$this->pdf=str_replace('<tr','<span',$this->pdf);
		$this->pdf=str_replace('</tr','</span',$this->pdf);

		$this->pdf=str_replace('<th','<span',$this->pdf);
		$this->pdf=str_replace('</th','</span',$this->pdf);

		$this->pdf=str_replace('<table','<span',$this->pdf);
		$this->pdf=str_replace('</table','</span',$this->pdf);

		$this->pdf=str_replace('<tbody','<span',$this->pdf);
		$this->pdf=str_replace('</tbody','</span',$this->pdf);

		$this->pdf=str_replace('<thead','<span',$this->pdf);
		$this->pdf=str_replace('</thead','</span',$this->pdf);
	}

	private function makeList() {
		$SQL = 'select distinct Family from edderkopper order by Family';
		$result = $this->query($SQL);
		$divider = false;
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->makeFamilyList($row['Family'], $divider);
			$divider = true;
		}
	}

	private function makeFamilyList($family, $divider) {
		$SQL='select distinct Name from edderkopper where Family="'.$family.'" order by Name';
		$result = $this->query($SQL);
		$count = $result->rowCount();

		//create space before new family section	
		if ($divider) {	
			$this->pdf.= '<tr style="height:20px;"><td colspan="11">&nbsp;</td></tr>';
		}

		$this->pdf.= '<tr style="height:30px;"><td style="background-color:#ebebeb;" ><b>'.strtoupper($family).'</b> ('.$count.')</td>';

		//foreach ($this->distrikter as $d) $this->pdf.= '<td></td>';
		//regioner ud for hver familie, lodret region tekst er derfor fjernet / udkommenteret
		//foreach ($this->distrikter as $d) $this->pdf.= '<td style="text-align:center;">'.$d.'</td>';
		foreach ($this->distrikter as $d) $this->pdf.= '<td class="rotate" style="vertical-align:top;">'.$d.'</td>';

		$this->pdf.= '</tr>';

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$this->makeSpeciesList($row['Name']);
		}
	}

	private function getIcon($year) {
		if ($year==0) return "&nbsp;";
		if ($year<=1950) return '&#9675;'; //○
		//if ($year<=2012) return '&#9679;'; //
		return '&#9679;';
	}
		
	private function getNewest($dataset) {
		$year=0;
		while ($row = $dataset->fetch(PDO::FETCH_ASSOC)) {
			//if ($year<$row['Year_first']) $year=$row['Year_first'];
			if ($year<$row['Year_last']) $year=$row['Year_last'];
		}
		return $year;
	}

	private function makeSpeciesList($name) {
		$this->pdf.= '<tr>';
		$html='';
		$recent=0;
		foreach ($this->distrikter as $distrikt) {
			$SQL='select Year_last from edderkopper where Name="'.$name.'" and Region="'.$distrikt.'"';
			$dataset = $this->query($SQL);
			$newest = $this->getNewest($dataset);
			if ($newest>$recent) $recent=$newest;

			$html.='<td class="border">'.$this->getIcon($newest).'</td>';
		}
		$this->pdf.= '<td class="name"><em>'.$name.'</em>';
		if ($recent<1989) $this->pdf.= '&nbsp;*'; //&#1645;
		$this->pdf.= $html;
		$this->pdf.= '</tr>'; 
	}
				
}

$test = new Checklist();

?>
