<?
include('../common/Db.php');
include('../common/HTML.php');
//include('../classes/Template.php');

//lang, assume hidden field sess_lang is defined
if (isset($_GET['sess_lang'])) {
	switch ($_GET['sess_lang']) {
		case 2 : include('../lang/english.php'); break;
		default : include('../lang/dansk.php'); break;
	}
} else {
	include('../lang/dansk.php');
}

//translation
function trans($text, $print=false) {
	if (!$print) {
		if (defined($text)) {
			return constant($text);
		} else return $text;
	} else {
		if (defined($text)) {
			echo constant($text);
		} else echo $text;
	}
}

// generic class for presenting the result of a SQL-search in a jquery DataTable 
// result-table should be rendered by the child class
// assumes legend is present (by #content-headline)
// set $initResultTable to false, if descendent of this class need to render the DataTable  
class SearchBase extends Db {

	public function __construct($initResultTable=true) {
		parent::__construct();
		header('Content-Type: text/html; charset=ISO-8859-1');
		$this->drawScript();
		if ($initResultTable) $this->initResultTable();
	}

	protected function drawBackButton() {
		echo '<div style="float:left;clear:both;width:100%;;">';
		echo '<input type="button" value="'.trans(ZN_SEARCH_BACK).'" onclick="window.Search.back();" title="'.trans(ZN_SEARCH_BACK).'">';
		echo '</div>';
	}

	protected function getLanguage() {
		return (isset($_GET['sess_lang'])) ? $_GET['sess_lang'] : 1;
	}

	protected function testParam($param) {
		if (!isset($_GET[$param])) return false;
		if ($_GET[$param]=='') return false;
		return true;
	}

	//return proper like-statement regarding multiple words and whitespaces
	protected function getLike($field, $value) {
		$values=explode(' ', $value);
		$sql='';
		foreach($values as $val) {
			if ($sql!='') $sql.=' and ';
			$sql.=$field.' like "%'.$val.'%"';
		}
		return $sql.' ';
	}

	protected function implementHoverImg() {
?>
<style type="text/css">
#hoverimg {
	z-index: -1;
	position: absolute;
	width: 200px;
	padding: 5px;
	background-color: white;
	border: 1px solid #ccc; /*#33613D;*/
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	//add the hover image to search-result container
	$("#search-result").append('<img src="" id="hoverimg" alt="snm.ku.dk">');
	//implement hover function for result-table rows
	$("#result-table").on('mouseover', 'tr', function(e) {
		var pic=$(this).attr('pic');
		//no picture
		if (pic=='none') return;
		$("#hoverimg").css('z-index', '44');
		$("#hoverimg").attr('src', pic);
		//adjust if user is scrolling
		var top=$(window).scrollTop(); 
		top = (top>0) ? parseInt(top)-parseInt(127)+'px' : '7px'; 
		$("#hoverimg").css('left', '719px'); //815
		$("#hoverimg").css('top', top);
	});
	$("#result-table").on('mouseout', function(e) {
		$("#hoverimg").css('z-index', '-1');
	});
});
</script>
<?
	}

	private function drawScript() {
?>
<style type="text/css">
#result-table td {
	vertical-align: top;
}
#result-table td a {
	text-decoration: underline;
}
.dataTable td {
	font-size: 12px;
}
#result-table th {
	text-align: left;
}
tr.odd {
	background-color: #dbe5eb; 
}
tr.odd td.sorting_1 {
	background-color: #dbe5eb; 
}
tr.even td.sorting_1 {
	background-color: #ffffff;
}
.DataTables_sort_wrapper {
	font-size: 14px;
	cursor: pointer;
}
</style>
<?
	}

	private function initResultTable() {
?>
<!-- correct buttons -->
<style type="text/css">
.DTTT_container {
	position: absolute;
	right:0px;
	z-index: 66;
	top: -22px;
	cursor: pointer;
}
button.DTTT_button {
	margin-right : 5px;
	width: 50px;
	cursor:pointer;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$("#result-table").dataTable({
		bJQueryUI: true,
        bPaginate: false,  
        bInfo: false,  
		bLengthChange: false,
        bFilter: false,
        bAutoWidth: false,
		asStripClasses:[],
		bSortClasses: false,
		sDom: 'T<"clear">lfrtip'
	});
	$(".dataTables_filter").hide();
	$(".fg-toolbar").hide();
	$(".DTTT_container").hide();
});
</script>
<?
	}	

}

?>
