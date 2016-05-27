<?
//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('../common/Db.php');
include('../common/HTML.php');

//params : table, field, value
class EditRecord extends Db {
	protected $table;
	protected $field;
	protected $record;

	public function __construct() {
		parent::__construct();

		if (!isset($_GET['action'])) return;
		switch ($_GET['action']) {
			case 'show' : 
				$this->table=isset($_GET['table']) ? $_GET['table'] : false;
				$this->field=isset($_GET['field']) ? $_GET['field'] : false;
				$this->value=isset($_GET['value']) ? $_GET['value'] : false;
				if ((!$this->table) || (!$this->field) || (!$this->value)) {
					return;
				}
				$this->drawScript();
				$this->loadRecord();
				$this->drawRecord();
				break;
			case 'save' : 
				$this->saveRecord();
				break;
		}
	}

	protected function saveRecord() {
		$a = array('action','table','field','value', $_GET['field']);
		$SQL='update '.$_GET['table'].' set ';
		foreach ($_GET as $f=>$v) {
			if (!in_array($f, $a)) {
				$SQL.=$f.'="'.utf8_decode($v).'",';
			}
		}
		$SQL=$this->removeLastChar($SQL);
		$SQL.=' where '.$_GET['field'].'="'.$_GET['value'].'"';

		$this->query($SQL);
		/*
		echo '<pre>';
		print_r($SQL);
		echo '</pre>';
		*/
	}

	protected function loadRecord() {
		$SQL='select * from '.$this->table.' where '.$this->field.'="'.$this->value.'"';
		mysql_set_charset('utf8');
		$this->result=$this->query($SQL);
	}

	protected function saveButton() {
		echo '<input type="button" value="Gem" onclick="saveRecord();">';
		echo '&nbsp;<span class="message"></span>';
	}

	protected function drawRecord() {
		$row=mysql_fetch_assoc($this->result);

		echo '<input type="hidden" id="value" value="'.$_GET['value'].'">';
		echo '<input type="hidden" id="table" value="'.$_GET['table'].'">';
		echo '<input type="hidden" id="field" value="'.$_GET['field'].'">';

		//echo '<input type="button" onclick="editBack();" value="&#9668;&nbsp;Tilbage til s&oslash;geresultater">';
		$this->saveButton();
		HTML::hr('search');
		echo '<form id="editform" method="get">';
		echo '<table>';
		foreach ($row as $field=>$value) {
			echo '<tr>';
			echo '<td><b>'.$field.'</b></td>';

			$extra=($field==$this->field) ? 'disabled="disabled" readonly="readonly"' : '';
									
			echo '<td><input '.$extra.' type="text" name="'.$field.'" id="'.$field.'" value="'.$value.'" style="width:400px;"></td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</form>';
		HTML::hr('search');
		$this->saveButton();
	}

	protected function drawScript() {
?>
<script type="text/javascript">
function editBack() {
	$("#edit-record").hide();
	$("#search-result").show();
}
function saveRecord() {
	Search.wait(true);
	var params=$("#editform").serialize();
	params+='&action=save';
	params+='&table='+$("#table").val();
	params+='&field='+$("#field").val();
	params+='&value='+$("#value").val();
	//params=encodeURIComponent(params);
	//call itself
	$.ajax({
		url: 'ajax/edit_record.php?',
		data: params,
		success : function(html) {
			//var time=new Date().getTime();
			$(".message").html('Ændringer gemt .. '+'<em>'+DateEval.getCurrentTime()+'</em>');
			Search.wait(false);
		}
	});
}
</script>
<?
	}

}

$edit = new EditRecord();

?>
