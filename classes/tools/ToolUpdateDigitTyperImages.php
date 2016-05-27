<?

class ToolUpdateDigitTyperImages extends ClassBase {

	public function __construct() {
		parent::__construct();
	}

	public function drawBody() {
		HTML::h2('Opdater felter på <code>digit_typer_images</code>');
		HTML::hr('search');
		HTML::br(2);

		echo '<button id="btnDatacode">Opdater <code>datacode</code></button>';
		echo '&nbsp;Opdaterer <code>datacode</code> på <code>digit_typer_images</code> i forhold til <code>DataCode</code> på <code>digit_typer</code><br>';
		HTML::br(2);

		echo '<button id="btnFileURI">Opdater <code>FileURI</code></button>';
		echo '&nbsp;Opdaterer <code>FileURI</code> på <code>digit_typer_images</code>';
		HTML::br(3);		
		
		HTML::hr('search');
		echo '<div id="result"></div>';
	}

	public function drawBeforeFooter() {
?>
<script>
$('#btnDatacode').click(function() {
	System.ajaxWheel('#result');
	$.ajax({
		url: 'ajax/tools/update_digit_typer_images.php?target=datacode',
		success : function(html) {
			$("#result").html(html);
			System.wait(false);
		}
	});
});
$('#btnFileURI').click(function() {
	System.ajaxWheel('#result');
	$.ajax({
		url: 'ajax/tools/update_digit_typer_images.php?target=fileuri',
		success : function(html) {
			$("#result").html(html);
			System.wait(false);
		}
	});
});
</script>
<?
	}


}

?>
