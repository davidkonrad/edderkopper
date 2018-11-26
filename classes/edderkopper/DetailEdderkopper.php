<?
//error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
error_reporting(1);
ini_set('display_errors', '1'); //

class DetailEdderkopper extends DetailBase {
	private $species;

	private $species_name;
	private $genus_name;
	
	private $species_id;
	private $species_dansk='';
	private $species_author;
	private $genus_id;
	private $family_id;

	private $text='';
	public $template = 'TemplateEdderkopper';
	private $processedText = array();
	private $collectionsMonths = array();
	private $code;

	private $region_names;
	private $region_stats;

	private $bodyText = '';

	public function __construct() {
		parent::__construct();

		$this->species=$this->getParam('taxon');
		$s=explode(' ',$this->species);
		$this->species_name=$s[1];
		$this->genus_name=$s[0];
		$this->code=($_SESSION[LANGUAGE]==1) ? 'DK' : 'UK';

		$this->getRegionStats();
		$this->getCollectionStats();
		$this->getIDs();
		$this->bodyText = $this->getSpeciesDesc().
						$this->getGenusDesc().
						$this->getFamilyDesc();

		//set title
		$title=$this->species;
		$popular = $_SESSION[LANGUAGE]==1 ? $this->species_dansk : $this->species_eng;
		$site = $_SESSION[LANGUAGE]==1 ? 'Danmarks Edderkopper' : 'Danish Spiders';
		if ($popular!='') {
			$title.= ', '.$popular;
		}
		$this->info['title']=$title.' - '.$site;

		//set meta
		$meta = $title.'. ';
		//$meta.= $_SESSION[LANGUAGE]==1 ? 'Artsbeskrivelse : ' : 'Species description : ';
		$meta.= strip_tags(substr($this->bodyText, 0, 120)).' ...';
		$this->info['meta_desc']=$meta;
	}

	public function extraHead() {
		parent::extraHead();
		if ($_SESSION['LANG']==1) {
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true&language=da&v=3.33"></script>
<?
		} else {
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAOj0_u0DRE2dK8X9YptdCXtxt89UCqfoo&amp;sensor=true&language=en&v=3.33"></script>
<?
		}
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load('visualization', '1.1', {'packages':['corechart, bar']});</script>
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/keydragzoom/src/keydragzoom.js?"></script>
<script type="text/javascript" src="js/edderkopper.js?ver=11"></script>
<script type="text/javascript" src="js/edderkopper_details.js?ver=3311"></script>
<script type="text/javascript" src="js/utm.js?ver=212"></script>
<script type="text/javascript" src="js/charts.js"></script>
<link rel="stylesheet" href="css/edderkopper.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper_popup.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/geo.js"></script>
<style type="text/css">
.img-box {
	border:1px solid #dadada;
	width: 205px;
	float: left;
	clear: none;
	padding: 5px;
	padding-right: 0px;
	margin-top:0px;
	margin-bottom: 8px;
	margin-left: 0px;
	margin-right: 8px;
	height: 160px;
}
img.species {
	width: 200px;
	cursor: pointer;
}
.img-caption {
	width: 100%;
	height: 16px;
	text-overflow: ellipsis;
	white-space: nowrap;
	font-size: 12px;
	overflow: hidden;
	line-height: 16px;
}
.images {
	float: left;
	clear: left;
	margin-top: 10px;
}
p {
	margin-top: 2px;
	display: inline;
}
#popup-image {
	display: none;
	position: absolute;
	margin: 0px;
	z-index: 500;
	width: 900px;
	cursor: pointer;
	padding: 0px;
	height: 585px;
	border: 1px solid #dadada;
	box-shadow: 3px 3px 3px #888888;
	box-shadow: 5px 3px 5px #dadada;
	overflow: hidden;
}	
#popup-image img {
	position: relative;
	top: 0px;
	width: 900px;
	padding: 0px;
	margin: 0px;
	border: 0px;
}
#map {
	width:400px;
	height:270px;
	border-left:1px solid #dadada;
	border-top:1px solid #dadada;
	border-right:1px solid #dadada;
	clear: none;
}
#map-legend {
	width:400px;
	height:30px;
	text-align:center;
	padding-top:5px;
	border-left:1px solid #dadada;
	border-bottom:1px solid #dadada;
	border-right:1px solid #dadada;
}
#map-legend img {
	position: relative;
	top:3px;
}
/* !!!! */
.details h3 {
	float: left;
	clear: left;
	overflow: hidden;
	display: inline;
}
.details p {
	float: left;
	clear: left;
	display: inline;
	overflow: hidden;
}
</style>
<?
	}

	private function getCollectionForMonth($species, $genus, $month) {
		$SQL='select '.
		'(select sum(MaleCount) from edderkopper where Genus="'.$genus.'" and Species="'.$species.'" and Month_last='.$month.') as males, '.
		'(select sum(FemaleCount) from edderkopper where Genus="'.$genus.'" and Species="'.$species.'" and Month_last='.$month.') as females, '.
		'(select sum(JuvenileCount) from edderkopper where Genus="'.$genus.'" and Species="'.$species.'" and Month_last='.$month.') as juveniles ';

		$result=$this->query($SQL);
		$row=mysql_fetch_assoc($result);
		return $row;
	}

	private function getCollectionStats() {
		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$this->collectionMonths = array();
		for ($month=1;$month<13;$month++) {
			$data = $this->getCollectionForMonth($this->species_name, $this->genus_name, $month);
			//set empty values to 0
			foreach($data as $key=>$value) {
				if ($value=='') $data[$key]=0;
			}
			$data['month']=$months[$month-1];
			$this->collectionMonths[]=$data;
		}
	}

	private function getRegionStats() {
		$spec='and (species="'.$this->species_name.'" and Genus="'.$this->genus_name.'") ';
		$SQL='select '.
		'(select count(*) from edderkopper where Region="SJ"'.$spec.') as SJ, '.
		'(select count(*) from edderkopper where Region="EJ"'.$spec.') as EJ, '.
		'(select count(*) from edderkopper where Region="WJ"'.$spec.') as WJ, '.
		'(select count(*) from edderkopper where Region="NWJ"'.$spec.') as NWJ, '.
		'(select count(*) from edderkopper where Region="NEJ"'.$spec.') as NEJ, '.
		'(select count(*) from edderkopper where Region="F"'.$spec.') as F, '.		
		'(select count(*) from edderkopper where Region="LFM"'.$spec.') as LFM, '.
		'(select count(*) from edderkopper where Region="SZ"'.$spec.') as SZ, '.
		'(select count(*) from edderkopper where Region="NWZ"'.$spec.') as NWZ, '.
		'(select count(*) from edderkopper where Region="NEZ"'.$spec.') as NEZ, '.
		'(select count(*) from edderkopper where Region="B"'.$spec.') as B ';
		$result=$this->query($SQL);
		$row=mysql_fetch_assoc($result);
		Proxy::assocToJS($row, $this->region_names, $this->region_stats);
	}

	private function getIDs() {
		$s=explode(' ',$this->species);
		$SQL='select SpeciesID, NameDK, NameUK, SAuthor from edderkopper_species where species="'.strtolower($s[1]).'"';
		mysql_set_charset('utf8');
		$row=$this->getRow($SQL);
		$this->species_id=$row['SpeciesID'];
		$this->species_dansk=$row['NameDK'];
		$this->species_eng=$row['NameUK'];
		$this->species_author=$row['SAuthor'];

		$SQL='select GenusID, FamilyID from edderkopper_genus where Genus="'.$s[0].'"';
		$row=$this->getRow($SQL);
		$this->genus_id=$row['GenusID'];
		$this->family_id=$row['FamilyID'];
	}

	private function bold($s) {
		return '<b>'.$s.'</b>';
	}

	private function getSizeDesc($m, $f) {
		$male = $_SESSION[LANG]==1 ? 'han ' : 'male ';
		$female = $_SESSION[LANG]==1 ? 'Hun ' : 'Female ';
		$size = $_SESSION[LANG]==1 ? 'Størrelse: ' : 'Size: ';

		$text.=$this->bold($size);
		$m = $m!='' ? $m.' mm' : '? mm';
		$f = $f!='' ? $f.' mm' : '?  mm';

		return $this->bold($size).$female.$f.'; '.$male.$m.'.';
	}

	private function getSpeciesDesc() {
		$beskrivelse = $_SESSION[LANG]==1 ? 'Beskrivelse: ' : 'Description: ';
		$udbredelse = $_SESSION[LANG]==1 ? 'Udbredelse: ' : 'Distribution: ';
		$habitat = $_SESSION[LANG]==1 ? 'Habitat: ' : 'Habitat: ';
		$biologi = $_SESSION[LANG]==1 ? 'Biologi: ' : 'Biology: ';

		$text = '';

		$SQL='select '.
			'SChar'.$this->code.' as art,'.
			'SBiolEu'.$this->code.' as bio,'.
			'SHabDk'.$this->code.' as hab,'.
			'SDistriDk'.$this->code.' as dis,'.
			'MSize, FSize '.
			'from edderkopper_species '.
			'where SpeciesID="'.$this->species_id.'"';			

		$row = $this->getRow($SQL);			

		if ($row['art']!='') {
			//if ($text!='') $text.='.&nbsp;';
			$text.=$this->bold($beskrivelse).$row['art'];
		}
		if ($row['bio']!='') {
			if ($text!='') $text.='&nbsp;';
			$text.=$this->bold($biologi).$row['bio'];
		}
		if ($row['hab']!='') {
			//if ($text!='') $text.='.&nbsp;';
			$text.=$this->bold($habitat).$row['hab'];
		}
		if ($row['dis']!='') {
			//if ($text!='') $text.='.&nbsp;';
			$text.=$this->bold($udbredelse).$row['dis'];
		}

		if ($text!='') $text.='&nbsp;';
		$text.=$this->getSizeDesc($row['MSize'], $row['FSize']);

		return $text;
	}

	private function getGenusDesc() {
		$karakteristik = $_SESSION[LANGUAGE]==1 ? 'Slægtsbeskrivelse: ' : 'Characters of genus: ';
		$genus = $_SESSION[LANGUAGE]==1 ? 'Slægt: ' : 'Genus: ';

		$SQL='select '.
			'Genus,'.
			'GAuthor,'.
			'GName'.$this->code.' as gna,'.
			'GCharacters'.$this->code.' as cha '.
			'from edderkopper_genus '.
			'where GenusID="'.$this->genus_id.'"';

		$row = $this->getRow($SQL);
		
		$text = '<br><br><b>'.$genus.'</b>';
		$text.='<em>'.$row['Genus'].'</em> '.$row['GAuthor'].'. ';
		if (trim($row['gna'])!='') {
			$text.=' ('.$row['gna'].'). ';
		}
		if ($row['cha']!='') {
			$text.=$this->bold($karakteristik).$row['cha'];
		}

		return $text;
	}

	private function getFamilyDesc() {
		$familie = $_SESSION[LANGUAGE]==1 ? 'Familie: ' : 'Family: ';
		$biologi = $_SESSION[LANG]==1 ? 'Biologi: ' : 'Biology: ';
		$karakteristik = $_SESSION[LANG]==1 ? 'Familiekarakteristik: ' : 'Characters of family: ';

		$SQL='select '.
			'Family,'.
			'Author,'.
			'Family'.$this->code.' as nam,'.
			'FBiologyDk'.$this->code.' as bio,'.
			'FCharacters'.$this->code.' as cha '.
			'from edderkopper_family '.
			'where FamilyID="'.$this->family_id.'"';

		$row = $this->getRow($SQL);

		$text='<br><br>'.$this->bold($familie);
		$text.='<em>'.$row['Family'].'</em> '.$row['Author'];
		if ($row['nam']!='') $text.=' ('.$row['nam'].')';
		$text.='.';

		if ($row['bio']!='') {
			$text.=$this->bold($biologi).$row['bio'];
		}

		if ($row['cha']!='') {
			$text.='&nbsp;'.$this->bold($karakteristik).$row['cha'];
		}

		return $text;
	}

	private function getImages() {
		if ($this->species_id=='') return;
		$SQL='select * from edderkopper_photo where SpeciesId='.$this->species_id;
		mysql_set_charset('utf8');
		$result=$this->query($SQL);
		while ($row=mysql_fetch_array($result)) {
			$filename=stripslashes($row['Filename']);
			$filename='lissner/'.$filename;
			if (file_exists($filename)) {
				$caption=$_SESSION[LANGUAGE]==1 ? $row['SubjectDK'] : $row['SubjectUK'];
				echo '<div class="img-box">';
				echo '<img title="'.$caption.'" src="'.$filename.'" class="species" onclick="popup(&quot;'.$filename.'&quot;);">';
				echo '<div class="img-caption">';
				echo $caption;
				echo '</div>';
				echo '</div>';
			}
		}
	}

	public function drawBody() {
		$links = array();
		$links[1] = 'artsbeskrivelse?taxon='.$this->species;
		$links[2] = 'species-description?taxon='.$this->species;
		LANG::flagMenu($links);

		echo '<div id="popup-image" style="display:none;"></div>';

		echo '<fieldset class="details">';
		echo '<legend class="details">';
		echo '<em>'.$this->species.'</em>';
		echo '&nbsp;&nbsp;'.$this->species_author;
		$popular = $_SESSION[LANGUAGE]==1 ? $this->species_dansk : $this->species_eng;
		if ($popular!='') {
			echo ' ('.$popular.')';
		}

		echo '</legend>';

		echo '<div style="float:left;clear:none;width:420px;">';
		echo '<div id="map"></div>';

		echo '<div id="map-legend">';
		echo '<img src="ico/Circle_Blue.png" alt=""/>&#8804; 1900';
		echo '&nbsp;&nbsp;&nbsp;<img src="ico/Circle_Orange.png" alt=""/>1901-1979';
		echo '&nbsp;&nbsp;&nbsp;<img src="ico/Circle_Yellow.png" alt=""/>1980-2005';
		echo '&nbsp;&nbsp;&nbsp;<img src="ico/Circle_Red.png" alt=""/>2006 &#8804;';
		echo '</div>';
		echo '</div>';

		echo '<div id="region-graph" style="float:left;width:400px;margin-top:40px;height:265px;"></div>';

		echo '<div id="collection_months" style="width:830px;padding-top:30px;height:185px;clear:both;"></div>';

		HTML::divider(30);
		echo $this->bodyText;
		HTML::divider(30);

		echo '<div class="images">';
		$this->getImages();
		echo '</div>';
		echo '</fieldset>';

?>
<script type="text/javascript">
$(document).ready(function() {
	var species="<? echo $this->species;?>";
	Art.initMap();
	Art.showFund(species);

	Chart.colChart('region-graph',
		'<? trans(LAB_SPIDERS_GRAPH_REGIONS, true);?>',
		'Region',
		'<? trans(LAB_OBSERVATIONS_COUNT, true);?>',
		<? echo $this->region_names; ?>,
		<? echo $this->region_stats; ?>,
		{
			title: '<? trans(LAB_SPIDERS_GRAPH_REGIONS, true);?>',
			titlePosition : 'out',
			titleTextStyle : {
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
			'chartArea' : { left: 40, top: 30, width: "100%", height: "65%"}
		}
		);


});
//males, females, juveniles
$(document).ready(function() {
	function drawCollectionMonths() {
	   var data = google.visualization.arrayToDataTable([
	         ['', '<? trans(LAB_MALES, true);?>', '<? trans(LAB_FEMALES, true);?>', '<? trans(LAB_JUVENILES, true);?>'],
<?
	foreach($this->collectionMonths as $cm) {
		echo '["'.$cm['month'].'", '.$cm['males'].','.$cm['females'].','.$cm['juveniles'].']';
		if ($cm['month']!='Dec') echo ',';
	}
?>
		]);

		var title = "<? echo $_SESSION['LANG']==2 ? 'Phenogram showing number of records of adult males, adult females and juveniles  by month (January to December). The phenogram is based on a subset of the dataset as not all records provide information on precise number of males, females and juveniles.'	: 'Indsamlede hanner, hunner og ungdyr henover måneder';?>";

		var options = {
			colors: ['#33613D', '#61380B', '#424242'],
			titlePosition : 'out',
			chart : {
				//google charts does not like &xyz; entities
				title: title
			},
			chartArea : { left: 10, top: 150, width: "100%", height: "100%"},
			vAxis : {
				format : '####'
			}

        };

        var chart = new google.charts.Bar(document.getElementById('collection_months'));
        chart.draw(data, options);
	}
	drawCollectionMonths();
});

function popup(img) {
	var top = window.pageYOffset || document.documentElement.scrollTop;
	top = top - 80;
	var html='<img src="'+img+'" title="<? trans(LAB_CLICK_TO_CLOSE_PICTURE, true);?>">';
	$("#popup-image").css('top', top+'px');
	$("#popup-image").html(html);
	$("#popup-image").show();
}
$("#popup-image").click(function() {
	$("#popup-image").hide();
});
</script>
<?
		echo '<div style="clear:both;float:left;width:100%;"><hr class="search"></div>';
		include('ajax/edderkopper_popup.php');
	}

}

?>
