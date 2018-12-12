<?

class ClassEdderkopperStatistik extends ClassBase {
	private $period_names;
	private $period_stats;
	private $region_names;
	private $region_stats;

	public function __construct() {
		parent::__construct();
		$this->getPeriods();
		$this->getRegions();
	}
	
	public function extraHead() {
		parent::extraHead();
?>
<script type="text/javascript" src="js/charts.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load('visualization', '1.0', {'packages':['corechart'], 'language': 'da' });</script>
<script type="text/javascript">
$(document).ready(function() {
	Chart.colChart('region-graph',
		'Fordelingen af fund i regioner', 
		'Region', 'Antal fund', 
		<? echo $this->region_names; ?>, 
		<? echo $this->region_stats; ?>,
		{
			title: '',
			titlePosition : 'out',
			titleTextStyle : {
				fontName : 'verdana',
				color: 'gray',
				fontSize: 14,
				bold: false,
				italic: false
			},
			colors : ['#33613D'], //'#A52A2A'
			'vAxis' : { 
				'textPosition' : 'out', 
				'gridlines' : { 'count' : 4 }, 
				"viewWindow" : { "min": 0 }, 
				"format" :'#'
			},
			'hAxis' : { 
				'slantedTextAngle' : 90, 
				'maxAlternation' : 1, 
				"viewWindow" : { "min": 0 },
				"format" :'#'
			},
			'width': "100%",
			'height': "100%",
			'legend' : { 'position' : 'none'} ,
			'backgroundColor' : 'transparent' ,
			'chartArea' : { left: 40, top: 30, width: "100%", height: "75%"}
		}
	);

	Chart.colChart('period-graph',
		'Fordelingen af fund over perioder', 
		'Periode', 'Antal fund', 
		<? echo $this->period_names; ?>, 
		<? echo $this->period_stats; ?>,
		{
			title: '', 
			titlePosition : 'out',
			titleTextStyle : {
				fontName : 'verdana',
				color: 'gray',
				fontSize: 14,
				bold: false,
				italic: false
			},
			colors : ['#33613D'], //'#A52A2A'
			'vAxis' : { 
				'textPosition' : 'out', 
				'gridlines' : { 'count' : 4 }, 
				"viewWindow" : { "min": 0 }, 
				"format" :'#'
			},
			'hAxis' : { 
				'slantedTextAngle' : 90, 
				'maxAlternation' : 1, 
				"viewWindow" : { "min": 0 },
				"format" :'#'
			},
			'width': "100%",
			'height': "100%",
			'legend' : { 'position' : 'none'} ,
			'backgroundColor' : 'transparent' ,
			'chartArea' : { left: 40, top: 30, width: "100%", height: "75%"}
		}
	);

});
</script>
<style type="text/css">
.container {
	float: left;
	clear: none;
	width: 420px;
	text-align: center;
	color: gray;
	font-family: 'verdana';
	font-size: '14px';
}
.container span {
	padding-top: 8px;
}
.graph {
	float: left;
	clear: both;
	width: 400px;
	height: 300px;
}
</style>
<?
	}

	public function drawBody() {
		parent::drawBody();
		$this->drawGeneral();
		$this->drawFund();
		$this->drawNew();
	}

	private function drawGeneral() {
		echo '<fieldset>';
		echo '<legend>'.trans(LAB_SPIDERS_OVERVIEW, false).'</legend>';

		$family=$this->getRecCount('edderkopper_family');
		$genus=$this->getRecCount('edderkopper_genus');		
		$species=$this->getRecCount('edderkopper_species');
		$fund=$this->getRecCount('edderkopper');

		switch ($_SESSION[LANGUAGE]) {
			case 1 :
				echo '<div>';
				echo '&#39;Danmarks edderkopper&#39; indeholder oplysninger om <b>'.$species.'</b> forskellige arter, fordelt på <b>'.$genus.'</b> slægter og <b>'.$family.'</b> familier.';
				echo '&nbsp;Fund-databasen rummer <b>'.$fund.'</b> godkendte fund.';
				echo '</div>';
				break;
			default :
				echo '<div>';
				echo '&#39;Danish Spiders&#39; contain information about <b>'.$species.'</b> known species in <b>'.$genus.'</b> genera and <b>'.$family.'</b> families.';
				echo '&nbsp;The observation database holds <b>'.$fund.'</b> accepted records.';
				echo '</div>';
				break;
		}

		echo '</fieldset>';
	}
	
	private function drawFund() {
		echo '<fieldset>';
		echo '<legend>'.trans(LAB_STATISTICS, false).'</legend>';

?>
<div class="container">
	<div id="region-graph" class="graph"></div>
<? trans(LAB_SPIDERS_GRAPH_REGIONS, true);?>
</div>
<div class="container" style="margin-left:40px;">
	<div id="period-graph" class="graph"></div>
<? trans(LAB_SPIDERS_GRAPH_PERIODS, true);?>
</div>
<?
		echo '</fieldset>';
	}

	private function getPeriods() {
		$SQL='select '.
		'(select count(*) from edderkopper where Year_last<1901) as p1900, '.
		'(select count(*) from edderkopper where Year_last>1900 and Year_last<1980) as p1979, '.
		'(select count(*) from edderkopper where Year_last>1979 and Year_last<2006) as p2005, '.
		'(select count(*) from edderkopper where Year_last>2005) as Nyeste ';
		$result=$this->query($SQL);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
		$fixed=array();
		$fixed['<1900']=$row['p1900'];
		$fixed['1901-1979']=$row['p1979'];
		$fixed['1980-2005']=$row['p2005'];
		$fixed['Seneste']=$row['Nyeste'];
		Proxy::assocToJS($fixed, $this->period_names, $this->period_stats);
	}

	private function getRegions() {
		$SQL='select '.
		'(select count(*) from edderkopper where Region="SJ") as SJ, '.
		'(select count(*) from edderkopper where Region="EJ") as EJ, '.
		'(select count(*) from edderkopper where Region="WJ") as WJ, '.
		'(select count(*) from edderkopper where Region="NWJ") as NWJ, '.
		'(select count(*) from edderkopper where Region="NEJ") as NEJ, '.
		'(select count(*) from edderkopper where Region="F") as F, '.		
		'(select count(*) from edderkopper where Region="LFM") as LFM, '.
		'(select count(*) from edderkopper where Region="SZ") as SZ, '.
		'(select count(*) from edderkopper where Region="NWZ") as NWZ, '.
		'(select count(*) from edderkopper where Region="NEZ") as NEZ, '.
		'(select count(*) from edderkopper where Region="B") as EL ';
		$result=$this->query($SQL);
		$row = $result->fetch(PDO::FETCH_ASSOC);

		Proxy::assocToJS($row, $this->region_names, $this->region_stats);
	}

	private function drawNew() {
		$SQL='select Genus, Species from edderkopper_species order by Genus';
		$result=$this->query($SQL);

		$new = array();

		//new species is from within the last 10 years
		$from_year = date("Y")-11;

		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$SQL='select min(Year_last) as y from edderkopper where '.
				'Genus="'.$row['Genus'].'" and Species="'.$row['Species'].'"';
			$min=$this->getRow($SQL);
			if ($min['y']>$from_year) {
				$name= '<em>'.$row['Genus'].' '.$row['Species'].'</em>';
				$new[]=array('name' => $name, 
							'year' => $min['y'],
							'Genus' => $row['Genus'],
							'Species' => $row['Species']
						);
			}
		}

		usort($new, function($a, $b) {
			return $a['year'] - $b['year'];
		});

		echo '<fieldset>';
		echo '<legend>'.trans(LAB_SPIDERS_NEW_SPECIES, false).'</legend>';
		foreach ($new as $art) {
			//echo '<a href="edderkopper-detaljer?species='.$art['Genus'].'%20'.$art['Species'].'">';
			echo '<a href="artsbeskrivelse?taxon='.$art['Genus'].'%20'.$art['Species'].'">';
			echo $art['name'];
			echo '</a>';
			echo '&nbsp;('.$art['year'].')'.'<br>';
		}		
		echo '</fieldset>';
		HTML::divider(70);
	}
						
}

?>
