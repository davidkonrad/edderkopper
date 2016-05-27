<?

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', '1');

class ClassEdderkopperLex extends ClassBase {
	public $template = 'TemplateEdderkopper';
	public $index;

	public function __construct() {
		parent::__construct();
		$this->index = $this->getParam('index')!='' ? $this->getParam('index') : 'a';
	}

	public function extraHead() {
?>
<style>
.lex-index {
	width: 100px;
	border-right: 1px solid #ebebeb;
	float: left;
	clear: none;
}
.lex-index .index {
	clear: both;
	float: left;
	font-size: 30px;
	font-weight: bold;
	font-family : 'times', 'courier', 'courier new';
	margin-left: 30px;
	padding:10px;
}
.lex-index .selected {
	color: black;
	background-color: #ebebeb;
}
.lex-index .dimmed {
	color: #ebebeb;
}
.lex-species {
	width: 400px;
	clear: none;
	float: left;
	padding-left: 30px;
}
</style>
<?
	}

	private function isValidIndex($index) {
		$SQL='select count(*) as c from edderkopper_species where Genus like "'.$index.'%"';
		$row = $this->getRow($SQL);
		return $row['c']>0;
	}

	private function drawIndexes() {
		$url=$this->currentSemanticName(true);
		echo '<div class="lex-index">';
		for ($i = ord('A'); $i <= ord('Z'); $i++) {
			if (strtolower(chr($i))==$this->index) {
				echo '<span class="index selected">'.chr($i).'</span>';
			} else {
				if ($this->isValidIndex(chr($i))) {
					$href=$url.'?index='.strtolower(chr($i));
					echo '<a class="index" href="'.$href.'">'.chr($i).'</a>';
				} else {
					echo '<span class="index dimmed">'.chr($i).'</span>';
				}
				
			}
		}

		echo '</div>';

	}

	private function drawSpecies($char) {
		$href = $_SESSION[LANGUAGE]==1
			? 'artsbeskrivelse'
			: 'species-description';

		echo '<div class="lex-species">';

		$SQL='select Species, Genus, NameDK, NameUK from edderkopper_species '.
			'where Genus like "'.$char.'%" order by Genus, Species';

		mysql_set_charset('utf8');
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			echo '<em>';
			echo '<a href="'.$href.'?taxon='.$row['Genus'].'%20'.$row['Species'].'">';
			echo $row['Genus'].' '.$row['Species'];
			echo '</a>';
			echo '</em>';
			if ($_SESSION[LANGUAGE]==1) {
				if ($row['NameDK']!='') {
					echo '&nbsp;&nbsp;('.$row['NameDK'].')';
				}
			} else {
				if ($row['NameUK']!='') {
					echo '&nbsp;&nbsp;('.$row['NameUK'].')';
				}
			}
			echo '<br>';
		}
		echo '</div>';		
	}

	public function drawBody() {
		parent::drawBody();
?>
<fieldset><legend>
<? 
/*
echo $_SESSION[LANGUAGE]==1
	? 'Danmarks edderkopper - alle arter fra A til Z'
	: 'Danish Spiders A-Z';
*/
echo $this->info['title'];
?>

</legend>
<? 
$this->drawIndexes();
//echo '<div class="lex-divider">&nbsp;</div>';
$this->drawSpecies($this->index);
HTML::divider(70);
?>

</fieldset>
<?
	}


}


?>


