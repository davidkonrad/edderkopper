<?

class ToolDigitTyperFullName extends ClassBase {

	public function drawBody() {
		HTML::h2('digit_typer : Updater FullName & FullNameOriginal');
		HTML::hr('search');
		echo '<button id="fullname">Opdater FullName</button>';
		HTML::br(2);
		echo '<button id="fullnameoriginal">Opdater FullNameOriginal</button>';
		echo '<div id="msg"></div>';
	}

	public function extraHead() {
?>
<style>
button {
	float: left;
	clear: both;
}
#msg {
	clear: both;
	padding: 10px;
	float: left;
}
</style>
<?
	}
		
	public function drawBeforeFooter() {
?>
<script>
var	url = 'ajax/digit_typer_update_fullname.php';
function setWheel() {
	$("#msg").html('<img src="img/ajax-loader.gif">');
	System.wait(true);
}
$("#fullname").click(function() {
	setWheel();
	$.ajax({
		url : url+'?field=FullName',
		success : function(text) {
			$("#msg").html(text);
			System.wait(false);
		}
	});
});
$("#fullnameoriginal").click(function() {
	setWheel();
	$.ajax({
		url : url+'?field=FullNameOriginal',
		success : function(text) {
			$("#msg").html(text);
			System.wait(false);
		}
	});
});
</script>
<?
	}

}
?>
