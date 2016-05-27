<style>
.rotate {
  -webkit-transform: rotate(-90deg);
  -moz-transform: rotate(-90deg);
  -ms-transform: rotate(-90deg);
  -o-transform: rotate(-90deg);
  transform: rotate(-90deg);

  /* also accepts left, right, top, bottom coordinates; not required, but a good idea for styling */
  -webkit-transform-origin: 50% 50%;
  -moz-transform-origin: 50% 50%;
  -ms-transform-origin: 50% 50%;
  -o-transform-origin: 50% 50%;
  transform-origin: 50% 50%;

  /* Should be unset in IE9+ I think. */
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
include('../common/Db.php');

class Checklist extends Db {
	private $distrikter=array('SJ','EJ','WJ','NWJ','NEJ','F','LFM','SZ','NWZ','NEZ','B');

	public function __construct() {
		parent::__construct();
		echo '<table cellpadding="0" style="border-spacing:0px;" id="table-checkliste">';

		echo '<thead><tr>';
		echo '<th></th>';
		foreach ($this->distrikter as $d) echo '<th></th>';
		echo '</tr></thead>';

		echo '<tbody<';

		echo '<tr><td></td>';
		foreach ($this->distrikter as $district) {
			echo '<td style="vertical-align:top;"><div class="rotate" style="vertical-align:top;width:30px;">'.$district.'</div></td>';
		}
		echo '</tr>';
		$this->makeList();

		echo '</tbody>';
		echo '</table>';
	}

	private function makeList() {
		$SQL='select distinct Family from edderkopper order by Family';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_array($result)) {
			$this->makeFamilyList($row['Family']);
		}
	}

	private function makeFamilyList($family) {
		$SQL='select distinct Name from edderkopper where Family="'.$family.'" order by Name';
		$result=$this->query($SQL);
		$count=mysql_num_rows($result);
		
		//echo '<tr><td colspan="12" style="background-color:#ebebeb;font-weight:bold;" >'.strtoupper($family).' ('.$count.')</td></tr>';
		echo '<tr><td style="background-color:#ebebeb;" ><b>'.strtoupper($family).'</b> ('.$count.')</td>';
		foreach ($this->distrikter as $d) echo '<td></td>';
		echo '</tr>';

		while ($row = mysql_fetch_array($result)) {
			$this->makeSpeciesList($row['Name']);
		}
	}

	private function getIcon($year) {
		if ($year==0) return "&nbsp;";
		if ($year<=1950) return '&#9675;'; //○
		//if ($year<=2012) return '&#9679;'; //
		return '&#9679;';

		/* img test
		$href='http://daim.snm.ku.dk/ico/Circle_Grey.png';
		return '<img src="'.$href.'">';
		*/
	}
		
	private function getNewest($dataset) {
		$year=0;
		while ($row = mysql_fetch_array($dataset)) {
			if ($year<$row['Year_first']) $year=$row['Year_first'];
		}
		return $year;
	}

	private function makeSpeciesList($name) {
		echo '<tr>';
		//echo '<td><em>'.$name.'</em></td>';
		$html='';
		$recent=0;
		foreach ($this->distrikter as $distrikt) {
			$SQL='select Year_first from edderkopper where Name="'.$name.'" and Region="'.$distrikt.'"';
			$dataset=$this->query($SQL);
			$newest=$this->getNewest($dataset);
			if ($newest>$recent) $recent=$newest;

			$html.='<td class="border">'.$this->getIcon($newest).'</td>';
		}
		echo '<td class="name"><em>'.$name.'</em>';
		if ($recent<1989) echo '&nbsp;&#1645;';
		echo $html;
		echo '</tr>'; 
	}
					
}

$test = new Checklist();

?>





