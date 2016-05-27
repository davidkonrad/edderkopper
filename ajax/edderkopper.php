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
<style>
span.funktioner-caption {
	padding-top: 5px;
	font-size: 10px;
	clear: both;
	float: left;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	Edderkopper.initSearchResult();
});
$(document).ready(function() {
	Geo.Habitater.populate("#vis-habitat");

	$('#kommune option').clone().appendTo('#vis-kommuner');
	$('#vis-kommuner option').each(function() {
		if ($(this).text()=='') $(this).remove();
	});

	$("#vis-kommuner").change(function() {
		var knr=$("#vis-kommuner option:selected").val()
		if (knr!='') {
			Geo.getKommuneGraense(knr, Details.map);
		}
	});	

	$("#vis-habitat").change(function() {
		var name=$("#vis-habitat option:selected").text();
		if (name!='') {
			Geo.Habitater.showHabitat(name, Details.map);
		}
	});	

	//$("#view").buttonset();

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

	Search.markers=[]; //??
	Search.submit("#edderkopper");
}
</script>
<?
	}

	private function createSQL() {
		/*
		foreach($_GET as $n=>$g) {
			$this->fileDebug($n.' '.$g);
		};
		*/

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
			//$where.='s.den_danske_roedliste<>"" ';
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

		//lookup
		/*
		if ($this->testParam('form-familie')) {
			if ($where!='') $where.=' and ';
			$where.='Family="'.$_GET['form-familie'].'" ';
		}
		if ($this->testParam('form-genus')) {
			if ($where!='') $where.=' and ';
			$where.='Genus="'.$_GET['form-genus'].'" ';
		}
		if ($this->testParam('form-species')) {
			if ($where!='') $where.=' and ';
			$where.='Species="'.$_GET['form-species'].'" ';
		}
		*/
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
		//echo $this->baseSQL;
		$this->result=$this->query($this->baseSQL);	
	}

	private function normalizeDate($date) {
		$test = preg_replace("/\D/", "", $date);
		if ($test!=$date) $date='0';
		$date=($date!='') ? $date : '0';
		return $date;
	}

	private function getDate($row) {
		/*
		$day=$row['Date_first'];
		$month=$row['Month_first'];
		$year=$row['Year_first'];
		*/
		$day=str_pad($row['Date_last'], 2, '0', STR_PAD_LEFT);
		$month=str_pad($row['Month_last'], 2, '0', STR_PAD_LEFT);
		$year=$row['Year_last'];
		return $day.'/'.$month.'/'.$year;
	}

	private function drawButtons() {
		$text = array();
		if ($this->getLanguage()==1) {
			$text['show']='Vis';
			$text['table']='Tabel';
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
		echo '<input type="button" value="'.trans(ZN_SEARCH_BACK).'" onclick="Edderkopper.back();" title="">';
		echo '</div>';
		echo '<div id="view" style="margin-left:20px;float:left;">';
		echo $text['show'].' : ';
		echo '<input type="radio" id="tabel" name="radio" checked="checked" onclick="Details.changeView(1);" style="vertical-align:text-bottom;">';
		echo '<label for="tabel" title="'.$text['table-title'].'">'.$text['table'].'</label>';		
		echo '<input type="radio" id="kort" name="radio" onclick="Details.changeView(2);" style="vertical-align:text-bottom;">';
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
		echo '<table id="result-table">';
		echo '<thead><tr>';

		echo '<th style="width:20px;"></th>'; //icon
		echo '<th style="width:20px;"></th>'; //info icon

		echo '<th style="width:150px;">'.trans(LAB_SPECIES).'</th>';
		echo '<th style="width:80px;">'.trans(LAB_DATE).'</th>';
		echo '<th style="width:300px;">'.trans(LAB_LOCALITY).'</th>';
		echo '<th style="width:70px;">UTM</th>';
		echo '<th style="width:100px;">Leg.</th>';
		echo '<th style="width:100px;">Det.</th>';
		echo '<th style="width:100px;">'.trans(LAB_COLLECTION).'</th>';
		echo '<th style="width:60px;">R</th>';

		/*
		echo '<th style="display:none;"></th>'; //lat
		echo '<th style="display:none;"></th>'; //long
		echo '<th style="display:none;"></th>'; //LNR
		*/
		//echo '<th style="width:20px;"></th>'; //edit icon

		echo '</tr></thead>';
		echo '<tbody>';
		while ($row=mysql_fetch_array($this->result)) {
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
			echo '<img style="cursor:pointer;margin-top:3px;margin-left:3px;" src="ico/info.gif" class="table-details-link" '.$params.' title="'.trans(LAB_SHOW_DETAILS).'">';
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

			/* 20.11.2013
			$edit='(&quot;edderkopper&quot;,&quot;LNR&quot;,&quot;'.$row['LNR'].'&quot;)';
			echo '<td><img src="ico/pencil.png" style="cursor:pointer" title="Rediger fund" onclick="Edderkopper.edit'.$edit.'"></td>';
			*/
			$edit='(&quot;edderkopper&quot;,&quot;LNR&quot;,&quot;'.$row['LNR'].'&quot;)';
			$href='edderkopper-rediger-fund?lnr='.$row['LNR'];
			//echo '<td><a href="'.$href.'" target=_blank><img src="ico/pencil.png" style="cursor:pointer" title="Rediger fund #'.$row['LNR'].'"></a></td>';
			echo '</tr>';
			}
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';

		echo '<div id="kort-cnt" style="display:none;">';
		HTML::divider(3);
		echo '<div id="map" style="width:685px;height:565px;float:left;clear:none;border:1px solid silver;"></div>';
		//funktioner
		echo '<fieldset class="mapview no-system-height" style="height:120px;">';
		$functions = $this->getLanguage()==1 ? 'Kortfunktioner' : 'Map functions';
 		echo '<legend style="font-size:14px;line-height:20px;">'.$functions.'</legend>';

		$utm = $this->getLanguage()==1 ? 'Vis UTM-felter' : 'Show UTM Grid';
		echo '<button style="margin-left:10px;cursor:pointer;" id="btn-utm-grid" onclick="Details.showUTMgrid();">'.$utm.'</button>';
		echo '<span style="margin-left:10px;float:none;clear:none;font-size:115%;font-weight:bold;" id="current-utm"></span><br>';

		//echo '<span class="funktioner-caption">Vis kommune(r) :</span>';
		echo '<br><select style="margin-left:10px;width:160px;" id="vis-kommuner"></option></select><br>';

		//echo '<span class="funktioner-caption">Vis habitatomr&aring;de(r) :</span>';
		$habitat = $this->getLanguage()==1 ? '[V&aelig;lg habitatområde]' : '[Select EU Habitat Site]';
		echo '<br><select style="margin-left:10px;width:160px;" id="vis-habitat"><option value="">'.$habitat.'</option></select><br>';

		//echo '<button style="margin-left:10px;" onclick="" disabled="disabled">Vis kommunegr&aelig;nser</button><br>';
		//echo '<button style="margin-left:10px;" onclick="" disabled="disabled">Vis distrikter</button><br>';
		//echo '<button style="float:right;font-size:10px;" onclick="Details.resetMap();">Nulstil kort</button>';
	
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
   <tr><td><img src="ico/Circle_Blue.png" alt=""/></td><td><? echo $f1900;?></td></tr>
   <tr><td><img src="ico/Circle_Orange.png" alt=""/></td><td><? echo $f1979;?></td></tr>
   <tr><td><img src="ico/Circle_Yellow.png" alt=""/></td><td><? echo $f2005;?></td></tr>
   <tr><td><img src="ico/Circle_Red.png" alt=""/></td><td><? echo $f2006;?></td></tr>
</table>
<?	
		echo '</fieldset>';
		echo '<fieldset class="mapview no-system-height" style="clear:none;height:305px;margin-top:30px;">';
		echo '<legend style="font-size:14px;line-height:20px;">Fund</legend>';
		echo '<div id="map-markers" style="font-size:12px;line-height:16px;height:250px;max-height:290px;overflow-x:hidden;overflow-y:scroll;vertical-align:top;clear:none;float:none;"></div>';
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
