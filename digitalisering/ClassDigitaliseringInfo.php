<?

class ClassDigitaliseringInfo extends ClassDigitaliseringBase {

	public function __construct() {
		parent::__construct();
	}

	public function extraHead() {
?>
<style type="text/css">
#digit-records {
	width:800px;
	clear: both;
	height: 500px;
}
</style>
<?
	}

	public function drawBody() {
		echo '<div id="digit-records">';
		echo 'Projekt: '.$this->getProjects('project_id');
		HTML::divider(3);
		//echo '<hr>';
		echo '<div id="search-result"></div>';
		echo '</div>';
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
$("#project_id").change(function() {
	var id=$(this).val();
	getRecords(id);
});
$(document).ready(function() {
	getRecords(1);
});
function getRecords(id) {
	var url='ajax/webcamrecords.php';
	url+='?project_id='+id;
	$.ajax({
		url: url,
		success : function(html) {
			$("#search-result").html(html);
		}
	});
}
</script>		
<?
	}
	
}

?>
