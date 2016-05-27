<?

include('lookup_base.php');

class EdderkoppeLookup extends Lookup {
	
	public function __construct() {
		parent::__construct();

		switch($_GET['target']) {
			case 'leg' : $this->lookupLeg(); break;
			case 'familie' : $this->lookupFamilie(); break;
			case 'genus' : $this->lookupGenus(); break;
			case 'species' : $this->lookupSpecies(); break;
			default : break;
		}
	}

	private function lookupLeg() {
		header('Content-type: application/json; charset=utf-8');

		$lookup=$_GET['lookup'];
		if ($lookup=='' || $lookup==' ') {
			$SQL='select distinct Leg from edderkopper order by Leg';
		} else {
			$SQL='select distinct Leg from edderkopper where Leg like "'.$_GET['lookup'].'%" order by Leg';
		}
		mysql_set_charset('utf8');
		$result=$this->query($SQL);
		$html='';
		while ($row = mysql_fetch_array($result)) {
			if ($html!='') $html.=',';
			$leg=$row['Leg'];
			$html.='{"value" : "'.$leg.'", "text": "'.$leg.'"}';
		}
		$html='['.$html.']';
		$this->fileDebug($html);
		echo $html;
	}

	private function lookupFamilie() {
		$lookup=$_GET['lookup'];
		if ($lookup=='' || $lookup==' ') {
			$SQL='select distinct Family from edderkopper order by Family';
		} else {
			$SQL='select distinct Family from edderkopper where Family like "'.$_GET['lookup'].'%" order by Family';
		}
		$result=$this->query($SQL);
		/*
		$html='';
		while ($row = mysql_fetch_array($result)) {
			if ($html!='') $html.=',';
			$html.='{"value" : "'.$row['Family'].'", "text": "'.$row['Family'].'"}';
		}
		$html='['.$html.']';
		echo $html;
		*/
		echo $this->getJSON($result, 'Family');
	}

	private function lookupGenus() {
		$lookup=$_GET['lookup'];
		//$extra=(isset($_GET['familie']) && ($_GET['familie']!='')) ? 'Family="'.$_GET['familie'].'"' : false;
		$extra=$this->getExtra('familie','Family');

		if ($lookup=='' || $lookup==' ') {
			if ($extra) {
				$SQL='select distinct Genus from edderkopper where '.$extra.' order by Genus';
			} else {
				$SQL='select distinct Genus from edderkopper order by Genus';
			}
		} else {
			if ($extra) {
				$SQL='select distinct Genus from edderkopper where Genus like "'.$_GET['lookup'].'%" and '.$extra.' order by Family';
			} else {
				$SQL='select distinct Genus from edderkopper where Genus like "'.$_GET['lookup'].'%" order by Genus';
			}
		}
		$result=$this->query($SQL);
		/*
		$html='';
		while ($row = mysql_fetch_array($result)) {
			if ($html!='') $html.=',';
			$html.='{"value" : "'.$row['Genus'].'", "text": "'.$row['Genus'].'"}';
		}
		$html='['.$html.']';
		echo $html;
		*/
		echo $this->getJSON($result, 'Genus');
	}

	private function lookupSpecies() {
		$lookup=$_GET['lookup'];
		
		$extra=false;
		/*
		if (isset($_GET['familie']) && ($_GET['familie']!='')) {
			$extra='Family="'.$_GET['familie'].'"';
		}
		if (isset($_GET['genus']) && ($_GET['genus']!='')) {
			if (is_string($extra)) $extra.=' and ';
			$extra.='Genus="'.$_GET['genus'].'"';
		}
		*/
		$e=$this->getExtra('familie','Family');	
		if ($e) $extra=$e;
		$e=$this->getExtra('genus','Genus');	
		if ($e) {
			if (is_string($extra)) $extra.=' and ';
			$extra.=$e;
		}

		if ($lookup=='' || $lookup==' ') {
			if ($extra) {
				$SQL='select distinct Species from edderkopper where '.$extra.' order by Species';
			} else {
				$SQL='select distinct Species from edderkopper order by Species';
			}
		} else {
			if ($extra) {
				$SQL='select distinct Species from edderkopper where Species like "'.$lookup.'%" and '.$extra.' order by Species';
			} else {
				$SQL='select distinct Species from edderkopper where Species like "'.$lookup.'%" order by Species';
			}
		}
		$result=$this->query($SQL);
		/*
		$html='';
		while ($row = mysql_fetch_array($result)) {
			if ($html!='') $html.=',';
			$html.='{"value" : "'.$row['Species'].'", "text": "'.$row['Species'].'"}';
		}
		$html='['.$html.']';
		echo $html;
		*/
		echo $this->getJSON($result, 'Species');
	}
			
		
}

$edderkopper = new EdderkoppeLookup();

?>
