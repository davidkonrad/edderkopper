<?

include('SearchBase.php');

error_reporting(E_ALL);
ini_set('display_errors', '1');

class WebcamRecords extends SearchBase {
	private $project_id;
	
	public function __construct() {
		parent::__construct();
		$this->project_id = (isset($_GET['project_id'])) ? $_GET['project_id'] : 1;
		$this->draw();
		$this->implementHoverImg();
	}

	protected function draw() {
		$SQL='select * from webcam where project_id='.$this->project_id;
		$result=$this->query($SQL);

		echo '<table id="result-table">';
		echo '<thead><tr>';

		echo '<th style="width:100px;">Indtaster</th>';
		echo '<th style="width:350px;">Label</th>';
		echo '<th style="width:200px;">Taxon</th>';
		echo '<th style="width:150px;">Dato/tid</th>';
		echo '</tr></thead>';
		echo '<tbody>';

		while ($row = mysql_fetch_assoc($result)) {
			$src='digitalisering/uploads/'.$this->project_id.'/'.$row['jpeg_image'];
			echo '<tr pic="'.$src.'" id="'.$row['id'].'">';
			echo '<td>'.$row['creator'].'</td>';
			echo '<td>'.$row['label'].'</td>';
			echo '<td>'.$row['taxon'].'</td>';
			//echo '<td>'.date('Y-m-d H:i:s', $row['_timestamp']).'</td>';
			echo '<td>'.$row['_timestamp'].'</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
?>
<div id="edit" style="display:none;">
<h3>Rediger</h3>
<input type="hidden" value="" name="id" id="id">
<table>
<tr>
<td>Label</td><td><input type="text" id="label" name="label" size="30"></td>
</tr>
<tr>
<td>Taxon</td><td><input type="text" id="taxon" name="taxon" size="30"></td>
</tr>
<tr>
<td>Password</td><td><input type="text" id="password" name="password" size="15"></td>
</tr>
<tr><td colspan="2"><hr></td></tr>
<tr><td></td><td>
<input type="button" value="Fortryd" id="cancel_btn">
<input type="button" value="Gem" id="update_btn">
<input type="button" value="Slet" id="delete_btn">
</td></tr>
</table>
</div>
<style type="text/css">
#result-table tr {
	cursor: pointer;
}
#edit {
	width: 350px;
	height: 180px;
	padding-left: 8px;
	border:1px solid silver;
	position: absolute;
	z-index: 100;
	background-color: #fff;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$("#result-table").on('click', 'tr', function(e) {
		var id=$(this).attr('id');
		if (id>0) {
			//console.log(e);
			$("#edit").css('top', (e.clientY-120)+'px');
			$("#id").val();
			$("#label").val($(this).find('td:eq(1)').text());
			$("#taxon").val($(this).find('td:eq(2)').text());
			$("#edit").show();
		}
	});
});
function pw() {
	if ($("#password").val()=='') {
		alert('Password skal udfyldes');
		return false;
	}
	return true;
}
function update(action) {
	var project_id=$("#project_id").val();
	var url='digitalisering/ajax_save.php';
	url+='?action='+action;
	url+='&project_id='+$("#project_id").val();
	url+='&id='+$("#id").val();
	url+='&label='+$("#label").val();
	url+='&taxon='+$("#taxon").val();
	url+='&password='+$("#password").val();
	$.ajax({
		url: url,
		success : function(html) {
			//alert(html);
			//call function on "mother" page / container, calling this
			getRecords(project_id);
		}
	});
}
$("#cancel_btn").click(function() {
	$("#edit").hide();
});
$("#update_btn").click(function() {
	update('update');
});
$("#delete_btn").click(function() {
	update('delete');
});
</script>
<?

	}
}

$records = new WebcamRecords();

?>
