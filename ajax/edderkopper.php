<?
include('SearchBase.php');
include('PolygonSearch.php');

class Search extends SearchBase {
	private $baseSQL = '';
	private $result;
	private $coords = array();
	private $polygon = null; //PolygonSearch object

	public function __construct() {
		parent::__construct(false);
		$this->polygon=new PolygonSearch('LatPrec','LongPrec');
	}

	public function run() {
		$this->createSQL();
		$this->performSearch();
		$this->drawTable();
		$this->drawScript();
	}

	private function drawScript() {
?>
<script type="text/javascript">
$(document).ready(function() {
	Edderkopper.initSearchResult();
});
$(document).ready(function() {
	var wait = setInterval(function() {
		if (Details.mapLoaded) {
			clearInterval(wait);
			var knr = $('input[name="hidden-kommune"]').val();
			if (knr) {
				Geo.showKommune(knr, Details.map, true);
			}
			var hname = $('input[name="hidden-habitat"]').val();
			if (hname) {
				Geo.Habitater.showHabitat(hname, Details.map, true);
			}
			var utm = $('#utm').val();
			if (utm) {
				Geo.showUTM10(utm, Details.map, true);
			}
		}
	}, 500);

	$('.table-details-link').click(function(e) {
		e.preventDefault();
		Details.loadDetailsFromTable(
				$(this).attr('utm'),
				$(this).attr('lat'),
				$(this).attr('long'),
				$(this).attr('lnr'), 
				e.pageY
		);
	});
});
function tdLinkClick(field, value) {
	$("#edderkopper").find('input[type=hidden]').val('');
	$("#edderkopper").find('input[type=text]').val('');
	$("#edderkopper").find("#"+field).val(value);
	$('select').prop('selectedIndex',0);
	Details.reset();
	Search.markers=[]; //??
	Search.submit("#edderkopper");
}
</script>
<?
	}

	private function createSQL() {
		$this->baseSQL='select e.*, s.den_danske_roedliste from edderkopper e, edderkopper_species s ';
		$where='s.Genus=e.Genus and s.Species=e.Species ';
		if ($this->testParam('region')) {
			if ($where!='') $where.=' and ';
			$where.='e.Region="'.$_GET['region'].'" ';
		}
		if ($this->testParam('utm')) {
			if ($where!='') $where.=' and ';
			$where.='e.UTM10="'.$_GET['utm'].'" ';
		}
		if ($this->testParam('taxon')) {
			if ($where!='') $where.=' and ';
			$where.='e.Name like "%'.$_GET['taxon'].'%" ';
		}
		if ($this->testParam('leg')) {
			if ($where!='') $where.=' and ';
			$where.='e.Leg like "%'.utf8_decode($_GET['leg']).'%" ';
		}

		//rødliste
		if ($this->testParam('redlisted')) {
			if ($where!='') $where.=' and ';
			$where.='(';
			$redlist = array('NT', 'VU', 'EN', 'CR', 'RE');
			foreach ($redlist as $r) {
				$where.='s.den_danske_roedliste like "'.$r.'%"';
				if ($r!='RE') $where.=' or ';
			}
			$where.=') ';
		}

		//interval
		if ($this->testParam('e.year')) {
			if ($where!='') $where.=' and ';

			switch ($this->testParam('to-year')) {
				case false : $where.='e.Year_last="'.$_GET['year'].'" ';
						break;
				case true : $where.='(e.Year_last>="'.$_GET['year'].'" and e.Year_last<="'.$_GET['to-year'].'") ';
						break;
			}
		}
		if ($this->testParam('month')) {
			if ($where!='') $where.=' and ';

			switch ($this->testParam('to-month')) {
				case false : $where.='e.Month_last="'.$_GET['month'].'" ';
						break;
				case true : $where.='(e.Month_last>="'.$_GET['month'].'" and e.Month_last<="'.$_GET['to-month'].'") ';
						break;
			}
		}
		if ($this->testParam('day')) {
			if ($where!='') $where.=' and ';

			switch ($this->testParam('to-day')) {
				case false : $where.='e.Date_last="'.$_GET['day'].'" ';
						break;
				case true : $where.='(e.Date_last>="'.$_GET['day'].'" and e.Date_last<="'.$_GET['to-day'].'") ';
						break;
			}
		}

		if ($this->testParam('familie')) {
			if ($where!='') $where.=' and ';
			$where.='e.Family="'.$_GET['familie'].'" ';
		}
		if ($this->testParam('genus')) {
			if ($where!='') $where.=' and ';
			$where.='e.Genus="'.$_GET['genus'].'" ';
		}
		if ($this->testParam('species')) {
			if ($where!='') $where.=' and ';
			$where.='e.Species="'.$_GET['species'].'" ';
		}
		if ($this->testParam('locality')) {
			if ($where!='') $where.=' and ';
			$where.='e.Locality like "%'.utf8_decode($_GET['locality']).'%" ';
		}

		//hidden fields, extra
		if ($this->testParam('hidden-det')) {
			if ($where!='') $where.=' and ';
			$where.='e.Det="'.utf8_decode($_GET['hidden-det']).'" ';
		}
	
		//polygon
		$poly=$this->polygon->getSQL($_GET);
		$this->fileDebug('poly '.$poly);
		if ($poly!='') {
			$where=($where!='') ? $where.' and '.$poly : $poly;
		}

		if ($where!='') $this->baseSQL.='where '.$where;

		$this->fileDebug($this->baseSQL);
	}

	private function performSearch() {
		$this->setLatin1();
		$this->result=$this->query($this->baseSQL);	
	}

	private function normalizeDate($date) {
		$test = preg_replace("/\D/", "", $date);
		if ($test!=$date) $date='0';
		$date=($date!='') ? $date : '0';
		return $date;
	}

	private function getDate($row) {
		$day=str_pad($row['Date_last'], 2, '0', STR_PAD_LEFT);
		$month=str_pad($row['Month_last'], 2, '0', STR_PAD_LEFT);
		$year=$row['Year_last'];
		return $day.'/'.$month.'/'.$year;
	}

	private function drawButtons() {
		$text = array();
		if ($this->getLanguage()==1) {
			$text['show']='Vis';
			$text['table']='Oversigt';
			$text['table-title']='Vis s&oslash;geresultater som tabel';
			$text['map']='Kort';
			$text['map-title']='Vis s&oslash;geresultater p&aring; kort';
		} else {
			$text['show']='Show';
			$text['table']='Table';
			$text['table-title']='Show results as table';
			$text['map']='Map';
			$text['map-title']='Show results on a map';
		}

		echo '<div style="float:left;clear:left;">';
		echo '<a class="small edderkopper-back" href="#" onclick="Edderkopper.back();" title="Ret s&oslash;gning eller foretag en ny">'.trans(ZN_SEARCH_BACK).'</a>';
		echo '</div>';

		echo '<div id="view" style="margin-left:20px;float:left;">';
		echo $text['show'].': ';
		echo '<input type="radio" id="tabel" name="radio" title="'.$text['table-title'].'" checked="checked" onclick="Details.changeView(1);" class="edderkopper-show" >';
		echo '<label for="tabel" title="'.$text['table-title'].'">'.$text['table'].'</label>';		
		echo '<input type="radio" id="kort" name="radio" title ="'.$text['map-title'].'" onclick="Details.changeView(2);" class="edderkopper-show">';
		echo '<label for="kort" title ="'.$text['map-title'].'" >'.$text['map'].'</label>';
		echo '</div>';
	}

	private function tdLink($field, $value, $italic=false) {
		$a='<a class="search-refine" href="#" onclick="tdLinkClick(&quot;'.$field.'&quot;,&quot;'.$value.'&quot;);" title="'.trans(LAB_SEARCH_FOR).'&quot;'.$value.'&quot;">';
		if ($italic) {
			echo '<td><em>'.$a.$value.'</em></a></td>';
		} else {
			echo '<td>'.$a.$value.'</a></td>';
		}
	}

	private function drawTable() {
		if (isset($this->polygon->center)) {
			echo '<input type="hidden" id="lat-cnt" value="'.$this->polygon->center->x.'">';
			echo '<input type="hidden" id="long-cnt" value="'.$this->polygon->center->y.'">';
		}

		switch ($this->getLanguage()) {
			case 1 : $details='artsbeskrivelse'; break;
			default : $details='species-description'; break;
		}

		echo '<div id="items">';
		$this->drawButtons();
		echo '<div id="tabel-cnt">';
		HTML::divider(1);
		echo '<table id="result-table" class="display" style="width:100%">';
		echo '<thead><tr>';

		echo '<th style="width:20px;"></th>'; //icon
		echo '<th style="width:20px;"></th>'; //info icon

		echo '<th style="width:150px;">'.trans(LAB_SPECIES).'</th>';
		echo '<th style="width:80px;">'.trans(LAB_DATE).'</th>';
		echo '<th style="width:300px;">'.trans(LAB_LOCALITY).'</th>';
		echo '<th style="width:50px;">UTM</th>';
		echo '<th style="width:140px;">Leg.</th>';
		echo '<th style="width:140px;">Det.</th>';
		echo '<th style="width:100px;">'.trans(LAB_COLLECTION).'</th>';
		echo '<th style="width:50px;">R</th>';

		echo '</tr></thead>';
		echo '<tbody>';
		while ($row = $this->result->fetch(PDO::FETCH_ASSOC)) {
			if ($this->polygon->isIncludeable($row['LatPrec'], $row['LongPrec'])) {
			echo '<tr>';

			$long=str_replace(',','',$row['LongPrec']);
			$params=' utm="'.$row['UTM10'].'"'.
				' lat="'.$row['LatPrec'].'"'.
				' long="'.$long.'"'.
				' lnr="'.$row['LNR'].'" ';

			echo '<td>';
			echo '<a href="'.$details.'?taxon='.$row['Name'].'" target=_blank>';
			echo '<img src="ico/spider.png"></td>';
			echo '</a>';

			echo '<td>';
			echo '<img style="cursor:pointer;position:relative;top:5px;" src="ico/info.gif" class="table-details-link" '.$params.' title="'.trans(LAB_SHOW_DETAILS).'">';
			echo '</td>';

			$this->tdLink('taxon',$row['Name'], true);
			echo '<td>'.$this->getDate($row).'</td>';
			$this->tdLink('locality',$row['Locality']);
			$this->tdLink('utm',strtoupper($row['UTM10']));
			$this->tdLink('leg',$row['Leg']);
			$this->tdLink('hidden-det',$row['Det']);
			echo '<td>'.$row['Collection'].'</td>';

			echo '<td>'.$row['den_danske_roedliste'].'</td>';

			/*
			echo '<td style="display:none;">'.$row['LatPrec'].'</td>';
			echo '<td style="display:none;">'.$row['LongPrec'].'</td>';
			echo '<td style="display:none;">'.$row['LNR'].'</td>';
			*/

			echo '</tr>';
			}
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';

		echo '<div id="kort-cnt" style="display:none;">';
		HTML::divider(15);
		echo '<div id="map" style="width:685px;height:565px;float:left;clear:none;border:1px solid silver;"></div>';
		//funktioner
		echo '<fieldset class="mapview no-system-height" style="height:40px;">';
		$functions = $this->getLanguage()==1 ? 'Kortfunktioner' : 'Map functions';
 		echo '<legend style="font-size:14px;line-height:20px;">'.$functions.'</legend>';
		$utm = $this->getLanguage()==1 ? 'Vis UTM-felter' : 'Show UTM Grid';
		echo '<button style="margin-left:10px;margin-top:4px;cursor:pointer;" id="btn-utm-grid" onclick="Details.showUTMgrid();">'.$utm.'</button>';
		echo '<span style="margin-left:10px;float:none;clear:none;font-size:115%;font-weight:bold;" id="current-utm"></span><br>';
		echo '</fieldset>';

		//prikker legend
		$codes = $this->getLanguage()==1 ? 'Farvekoder' : 'Color codes';
		$f1900 = $this->getLanguage()==1 ? 'Fund &#8805; 1900' : 'Observations &#8805; 1900';
		$f1979 = $this->getLanguage()==1 ? 'Fund 1901-1979' : 'Observations 1901-1979';
		$f2005 = $this->getLanguage()==1 ? 'Fund 1980-2005' : 'Observations 1980-2005';
		$f2006 = $this->getLanguage()==1 ? 'Fund 2006 &#8804;' : 'Observations 2006 &#8804';

		echo '<fieldset class="mapview no-system-height" style="height:105px;margin-top:30px;">';
		echo '<legend style="font-size:14px;line-height:20px;">'.$codes.'</legend>';

?>
<table class="farvekoder">
   <tr><td><img src="ico/Circle_Blue.png" alt=""/ style="margin-top:2px;"></td><td><? echo $f1900;?></td></tr>
   <tr><td><img src="ico/Circle_Orange.png" alt=""/></td><td><? echo $f1979;?></td></tr>
   <tr><td><img src="ico/Circle_Yellow.png" alt=""/></td><td><? echo $f2005;?></td></tr>
   <tr><td><img src="ico/Circle_Red.png" alt=""/></td><td><? echo $f2006;?></td></tr>
</table>
<?	
		echo '</fieldset>';
		echo '<fieldset class="mapview no-system-height" style="clear:none;height:355px;margin-top:30px;">';
		echo '<legend style="font-size:14px;line-height:20px;">Fund</legend>';
		echo '<div id="map-markers" style="font-size:12px;line-height:16px;height:320px;max-height:390px;overflow-x:hidden;overflow-y:scroll;vertical-align:top;clear:none;float:none;"></div>';
		echo '</fieldset>';
		echo '</div>';		
	
		include('edderkopper_popup.php');
		HTML::divider(10);
	}

	private function drawPopup() {
	}

}

$search = new Search();
$search->run();

?>
