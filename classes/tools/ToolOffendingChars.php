<?

class ToolOffendingChars extends ClassBase {

	public function drawBody() {
		parent::drawBody();
		HTML::h2('Fjern "offending chars" / kontroltegn på en tabel');
		HTML::hr('search');
?>
<p>
Fjerner
<ul>
<li><code>\v</code>&nbsp;vertical tab</li>
<li><code>\t</code>&nbsp;tab</li>
<li><code>\r</code>&nbsp;return</li>
<li><code>\n</code>&nbsp;new line</li>
<li><code>#21</code>&nbsp;NAK</li>
</ul>
På samtlige felter i den valgte tabel
</p>
<?
		HTML::hr('search');
		$SQL='show tables';
		$result=$this->query($SQL);
		echo '<select id="table" class="no-auto-select">';
		echo '<option value="">[ vælg tabel ]</option>';
		while ($row = mysql_fetch_array($result)) {
			if (strpos($row[0], 'zn_')===false) {
				echo '<option value="'.$row[0].'">'.$row[0].'</option>';
			}
		}
		echo '</select>';
		echo '<button id="run">Kør</button>';
		HTML::hr('search');
		echo '<div id="result" style="width:600px;height:500px;overflow:auto;float:left;clear:both;"></div>';
	}

	public function drawBeforeFooter() {
?>
<script>
$("#run").click(function() {
	System.ajaxWheel('#result');
	$.ajax({
		
		url: 'ajax/tools/offending_chars.php?table='+$("#table").val(),
		success : function(html) {
			$("#result").html(html);
			System.wait(false);
		},
		error : System.ajaxError
	});
});
</script>
<?
	}
}

?>
