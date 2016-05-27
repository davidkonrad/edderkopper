<?
include('SearchBase.php');

echo $_SERVER['HTTP_REFERER'];

//!='/home/svampedk/public_html/soeg/cron/UpdateDB.php'
error_reporting(E_ALL);
ini_set('display_errors', '1');

class Search extends SearchBase {
	private $baseSQL = '';
	private $result;
	private $coords = array();

	public function __construct() {
		parent::__construct(false);
	}

	public function run() {
		$this->drawScript();
		$this->createSQL();
		$this->performSearch();
		$this->drawButtons();
		HTML::divider(10);
		$this->drawDetaljer();
		$this->drawForekomst();
	}

	private function drawDetaljer() {
		echo '<div id="detaljer-cnt">';
		echo '<table class="matrix-cnt" id="bille-result-table">';
		echo '<thead><tr>';

		echo '<th>Sml. navn??</th>';
		echo '<th>SJ</th>';
		echo '<th>EJ</th>';
		echo '<th>WJ</th>';
		echo '<th>NWJ</th>';
		echo '<th>NEJ</th>';
		echo '<th>F</th>';
		echo '<th>LFM</th>';
		echo '<th>SZ</th>';
		echo '<th>NWZ</th>';
		echo '<th>NEZ</th>';
		echo '<th>B</th>';
		echo '</tr></thead>';
		echo '<tbody>';
		$count=0;
		$id=0;
		$records=array();
		$distrikter=array('SJ','EJ','WJ','NWJ','NEJ','F','LFM','SZ','NWZ','NEZ','B');
		while ($row=mysql_fetch_array($this->result)) {
			$count++;
			$records[]=$row;
			$id++;
			if ($count==11) {
				echo '<tr>';
				$thisid='map'.$id;
				$taxon=str_replace(' ','<br/>',$row['taxon_name']);
				echo '<td class="taxon" style="vertical-align:top;font-weight:bold;width:200px;" id="'.$thisid.'"><i style="position:relative;z-index:100;">'.$taxon.'</i></td>';
?>
<script type="text/javascript">
map=[];
</script>
<?
				$arraycount=0;				
				foreach ($distrikter as $d) {
					$r=$this->getDistrikt($records,$d);
					if (($r) && ($r['lokalitet']!='')) {
?>
<script type="text/javascript">
map[<? echo $arraycount;?>]={ region: "<? echo $d;?>", color : "#2f4f4f" };
</script>
<?
					} else {
?>
<script type="text/javascript">
map[<? echo $arraycount;?>]={ region: "<? echo $d;?>", color : "#ebebeb" };
</script>
<?
					}
					$arraycount++;
				}
					
					
?>
<script type="text/javascript">
drawMap("<? echo $thisid;?>", 1.0, map);
</script>
<?
			

				foreach ($distrikter as $d) {
					$r=$this->getDistrikt($records,$d);
					//echo '<td>'.$r['lokalitet'].'</td>';
					//$this->drawDistriktCol($r);
					$this->drawDistriktCol2($r);
				}
				$count=0;
				$records=array();
				echo '</tr>';
			}
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';
	}

	private function createSQL() {
		$this->baseSQL='select t.taxon_name, b.auto_id, b.distrikt, b.lokalitet, '.
				'b.dato, b.leg, b.det, b.coll, b.note, b.dato_day, b.dato_month, b.dato_year '.
				'from billekatalog_taxon t, billekatalog b ';
		$where='where t.taxon_id=b.taxon_id ';
		if ($this->testParam('scientific')) {
			if ($where!='') $where.=' and ';
			$where.='t.taxon_name like "%'.$_GET['scientific'].'%" ';
		}
		if ($this->testParam('leg')) {
			if ($where!='') $where.=' and ';
			$where.='leg like "%'.$_GET['leg'].'%" ';
		}
		if ($this->testParam('det')) {
			if ($where!='') $where.=' and ';
			$where.='det like "%'.$_GET['det'].'%" ';
		}
		if ($this->testParam('lok')) {
			if ($where!='') $where.=' and ';
			$where.='lokalitet like "%'.$_GET['lok'].'%" ';
		}

		//dato interval, should be in a function
		if ($this->testParam('year')) {
			if ($this->testParam('to-year')) {
				if ($where!='') $where.=' and ';
				$where.='((dato_year>='.$_GET['year'].') and (dato_year<='.$_GET['to-year'].')) ';
			} else {
				if ($where!='') $where.=' and ';
				$where.='dato_year='.$_GET['year'].' ';
			}
		}
		if ($this->testParam('month')) {
			if ($this->testParam('to-month')) {
				if ($where!='') $where.=' and ';
				$where.='((dato_month>='.$_GET['month'].') and (dato_month<='.$_GET['to-month'].')) ';
			} else {
				if ($where!='') $where.=' and ';
				$where.='dato_month='.$_GET['month'].' ';
			}
		}
		if ($this->testParam('day')) {
			if ($this->testParam('to-day')) {
				if ($where!='') $where.=' and ';
				$where.='((dato_day>='.$_GET['day'].') and (dato_day<='.$_GET['to-day'].')) ';
			} else {
				if ($where!='') $where.=' and ';
				$where.='dato_day='.$_GET['day'].' ';
			}
		}

		//hvis ingen søgekriterier, alle poster søges
		//if ($where!='') $this->baseSQL.='where '.$where;
		$this->baseSQL.=$where;
		$this->baseSQL.=' order by t.taxon_name';
	}

	private function performSearch() {
		//echo $this->baseSQL;
		//mysql_set_charset('Latin1');
		//mysql_set_charset('ASCII');
		$this->result=$this->query($this->baseSQL);	
	}

	private function getDistrikt($records, $distrikt) {
		foreach ($records as $record) {
			if ($record['distrikt']==$distrikt) {
				return $record;
			}
		}
		return false;
	}

	private function drawScript() {
?>
<style type="text/css">
span.detail {
	border:1px solid gray;
	border-top:0px;
	border-bottom:0px;
	border-left: 0px;
	width:45px;
	min-width:46px;
	height:20px;
	max-height: 20px;
	text-align: center;
	clear:right;
	display: inline-block;
	overflow: hidden;
	margin: 0px;
	padding: 0px;
	font-size: 80%;
	white-space: nowrap;
}
span.note {
	border:1px solid gray;
	border-left: 0px;
	width:140px;
	min-width:140px;
	height:20px;
	max-height: 20px;
	clear:both;
	display: block;
	overflow: hidden;
	padding-bottom: 5px;
	font-size: 80%;
}
span.lok {
	border:1px solid gray;
	border-left: 0px;
	width:140px;
	min-width:140px;	
	height:80px; /* 60px*/
	clear:both;
	display: block;
	font-size: 80%;
}
table.matrix-cnt {
	float:left;
	border-collapse: collapse;
	border-spacing: 0px;
	margin-right: 16px;
}	
table.matrix-cnt td {
	padding: 0px;
	vertical-align: top;
	background-color: white;
}
table.matrix-cnt th {
	border:1px solid gray;
	padding: 0px;
	font-size: 130%;
	font-weight:normal;
	letter-spacing: 2px;
	text-align: center;
	background-color: white;
}
table.matrix-cnt td.taxon {
	border:1px solid gray;
	border-bottom: 2px solid gray;
	height: 82px;
	padding-left: 5px;
	width:130px;
}
span.forekomst-yes {
	background-color: green;
}
span.forekomst-no {
	background-color: white; /*red;*/
}
span.forekomst-box {
	min-width: 30px;
	width: 30px;
	height: 30px;
	display: block;
	margin-top: 24px;
	margin-left: 20px;
}
span.forekomst {
	border: 1px solid gray;
	border-left : 0px;
	min-width: 60px;
	display: block;
	width: 68px;
	height: 82px;
}

span.forekomst-taxon {
	font-family : 'times','times new roman';
	white-space: nowrap;
	font-style: italic;
}
span.forekomst-icon {
	font-family : 'times','times new roman';
	font-style: normal;
	font-size: 14px;
}
#forekomst-cnt th {
	width: 50px;
	border: 0px;
	font-family : 'times','times new roman';
	font-size: 13px;
	font-weight: normal;
}
.visuallyhidden { 
	position: absolute; 
	overflow: hidden; 
	clip: rect(0 0 0 0); 
	height: 1px; width: 1px; 
	margin: -1px; padding: 0; border: 0; 
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$("#view").buttonset();
	$(".detail-popup").each(function() {
		$(this).qtip({
			content: {
				ajax: {	url: 'ajax/billekatalog_popup.php?auto_id='+$(this).attr('auto_id') },
				title: { text: 'Originalt dataset', button: false }
			},
			position: { at: 'top left', my: 'top left', viewport: $(window), effect: true },
			show: {	event: 'mouseover', solo: true },
			hide: 'unfocus',
			style: { classes : 'ui-tooltip qtip ui-helper-reset ui-tooltip-default ui-tooltip-shadow ui-tooltip-plain ui-tooltip-pos-rc' }
		})
	})
 	// Make sure it doesn't follow the link when we click it
	.click(function(event) { event.preventDefault(); });
});
function changeView(v) {
	if (v==1) { 
		$("#detaljer-cnt").removeClass('visuallyhidden');
		$("#forekomst-cnt").addClass('visuallyhidden');
		$("#detaljer-cnt").show();
	} else {
		$("#detaljer-cnt").addClass('visuallyhidden');
		$("#forekomst-cnt").removeClass('visuallyhidden');
		$("#forekomst-cnt").show(); 
	}
}
</script>
<?
	}

	private function drawButtons() {
		echo '<div style="float:left;clear:left;">';
		echo '<input type="button" value="'.trans(ZN_SEARCH_BACK).'" onclick="window.Search.back();" title="">';
		echo '</div>';
		echo '<div id="view" style="margin-left:20px;float:left;">';
		echo 'Vis : ';
		echo '<input type="radio" id="detaljer" name="radio" checked="checked" onclick="changeView(1);"/><label for="detaljer">Detaljer</label>';
		echo '<input type="radio" id="forekomst" name="radio"  onclick="changeView(2);"/><label for="forekomst">Forekomst</label>';
		echo '</div>';
	}

	private function isEmpty($record) {
		//$this->debug($record);
		$a=array('taxon_name','auto_id','distrikt','1','2','3','4','5','6','7','8','9','10');
		if (!is_array($record)) return true;
		foreach($record as $field=>$value) {
			if ($value!='') {
				if (!in_array($field, $a)) {
					//echo 'X'.$value.'X'.$field;
					return false;
				}
			}
		}
		return true;
	}

	private function drawDistriktCol2($record) {
		//if (!$this->isEmpty($record)) {
		if (!$record==false) {
			echo '<td title="Hold musen henover at se alle originale data" class="detail-popup" auto_id="'.$record['auto_id'].'">';
		} else {
			echo '<td style="background-color:#ebebeb;">';
		}
		echo '<span class="lok">';
		echo wordwrap($record['lokalitet'], 18, "<br/>", true);
		echo '<br/>';
		if ($record['dato_day']!='') {
			echo $record['dato_day'].'.'.$record['dato_month'].'.'.$record['dato_year'];
		} else {
			echo $record['dato'];
		}
		echo '&nbsp;</span>';
		echo '<span class="detail">'.$record['leg'].'&nbsp;</span>';
		echo '<span class="detail">'.$record['det'].'&nbsp;</span>';
		echo '<span class="detail" style="width:42px;">'.$record['coll'].'&nbsp;</span>';

		echo '<span class="note">'.wordwrap($record['note'], 18, '<br/>', true).'&nbsp;</span>';
		echo '</td>';
	}

	private function drawForekomst() {
		echo '<div id="forekomst-cnt" style="display:none;">';
		HTML::divider(25);
		//echo '<hr class="search">';
		echo '<table class="matrix-cnt">';
		echo '<thead><tr>';

		echo '<th XXXstyle="width:200px;"></th>';
		echo '<th>SJ</th>';
		echo '<th>EJ</th>';
		echo '<th>WJ</th>';
		echo '<th>NWJ</th>';
		echo '<th>NEJ</th>';
		echo '<th>F</th>';
		echo '<th>LFM</th>';
		echo '<th>SZ</th>';
		echo '<th>NWZ</th>';
		echo '<th>NEZ</th>';
		echo '<th>B</th>';
		echo '</tr></thead>';
		echo '<tbody>';
		$count=0;
		$records=array();
		$distrikter=array('SJ','EJ','WJ','NWJ','NEJ','F','LFM','SZ','NWZ','NEZ','B');
		mysql_data_seek($this->result, 0);
		while ($row=mysql_fetch_array($this->result)) {
			$count++;
			$records[]=$row;
			if ($count==11) {
				echo '<tr>';
				//$taxon=str_replace(' ','<br/>',$row['taxon_name']);
				$taxon=$row['taxon_name'];
				echo '<td XXXXclass="taxon" style="vertical-align:middle;"><span class="forekomst-taxon">'.$taxon.'</span></td>';
				$count=0;
				foreach ($distrikter as $d) {
					echo '<td style="text-align:center;">';
					/*
					echo '<span class="forekomst">';
					$r=$this->getDistrikt($records,$d);
					if (($r==false) || ($this->isEmpty($r))) {
						echo '<span class="forekomst-box forekomst-no">&nbsp;</span>';
					} else {				
						echo '<span class="forekomst-box forekomst-yes">&nbsp;</span>';
					}
					echo '</span>';
					*/

					//echo '<span class="forekomst">';
					echo '<span class="forekomst-icon">';
					$r=$this->getDistrikt($records,$d);
					if (($r==false) || ($this->isEmpty($r))) {
						echo '&#8209;';
					} else switch($r['dato_year']) {
						case ($r['dato_year']<1900) :
							echo '1';
							break;
						case ($r['dato_year']<=1959) :
							echo '2';
							break;
						case ($r['dato_year']<=1999) :
							echo '&#9679;';
							break;
						default : 
							echo ' &#9633;';
							break;
					}
					echo '</span>';
					echo '</td>';
				}
				$count=0;
				$records=array();
				echo '</tr>';
			}
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';

	}

}

$search = new Search();
$search->run();

?>
