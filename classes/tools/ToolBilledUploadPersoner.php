<?

class ToolBilledUploadPersoner extends ClassBase {

	public function drawBody() {
		parent::drawBody();
		HTML::h2('Rediger billedupload personer.txt');
		HTML::hr('search');
		echo '<textarea id="personer" style="width:500px;height:300px;"></textarea>';
		HTML::hr('search');
		echo '<button id="save">Gem</button>';
	}

	public function drawBeforeFooter() {
?>
<script>
$.ajax({
	url: 'ajax/pictureupload_personer.php?action=get',
	type: 'text',
	success : function(text) {
		$('#personer').val(text);
	}
});
$("#save").click(function() {
	var text=$("#personer").val();
	text=encodeURIComponent(text);
	$.ajax({
		url: 'ajax/pictureupload_personer.php?action=put&text='+text,
		type: 'text',
		success : function(text) {
			//alert(text);
		}
	})
});
</script>
<?
	}
}

?>

	

