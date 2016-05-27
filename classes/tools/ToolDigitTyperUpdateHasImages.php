<?

class ToolDigitTyperUpdateHasImages extends ClassBase {
	private $imageCount;

	public function drawBody() {
		parent::drawBody();
		HTML::h2('Opdater <code>hasImages</code> på <code>digit_typer</code>');
		HTML::br(2);
		HTML::hr('search');
		$this->drawStats();	
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
var imageCount=<? echo $this->imageCount;?>;
function process(from) {
	var url='ajax/digit_typer_update_hasimages.php?from='+from;
	$.ajax({
		url : url,
		success : function(html) {
			//alert('ok');
			//console.log(html);
			if (from=='reset') {
				process('update');
			} else {
				finish();
			}
			/*
			console.log(html);
			console.log(from);
			if (from=='reset') {
				from=0;
				process(from);
			} else {
				if (from<imageCount) {
					msg(from+500);
					process(from+500);
				} else {
					finish();
				}
			}
			*/
		}
	});
}
function msg(count) {
	var text=count+' billeder behandlet';
	$("#run-progress").html(text);
}
function finish() {
	$("#run-img").hide();
	msg(imageCount);
}
$(document).ready(function() {
	$('#run-btn').click(function() {
		$("#run-btn").hide();
		$("#run-cnt").show();
		process('reset');
	});
});
</script>
<?
	}

	private function drawStats() {
		echo '<span style="font-size:120%;">';
		$SQL='select count(*) as c from digit_typer';
		$row=$this->getRow($SQL);
		echo '<b>'.number_format($row['c'], 0, '.', '.').'</b> typer i alt.<br>';

		$SQL='select count(*) as c from digit_typer_images';
		$row=$this->getRow($SQL);
		$this->imageCount=$row['c'];
		echo '<b>'.number_format($this->imageCount, 0, '.', '.').'</b> billeder i alt.<br>';
		
		echo '</span>';

		HTML::hr('search');
		echo '<button id="run-btn">Opdater hasImages</button>';
		echo '<span id="run-cnt" style="display:none;">';
		echo '<img id="run-img" src="img/ajax-loader.gif"><br><br>';
		echo '<span id="run-progress" style="font-size:200%;"></span>';
		echo '</span>';
	}


}

?>
