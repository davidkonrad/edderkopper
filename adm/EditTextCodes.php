<?

class EditTextCodes extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" src="js/edittrans.js"></script>
<?
	}

	protected function drawBody() {
		$this->buttonBar();
		echo '<div id="edit-trans"></div>';
		$this->drawTextCodes();
	}

	private function drawTextCodes() {
		$SQL='select * from zn_text_codes order by code ';
		$result = $this->query($SQL);
		echo '<table>';
		while ($row = mysql_fetch_array($result)) {
			echo '<tr><td>';
			echo '<input id="code_'.$row['text_id'].'" type="text" style="width:300px;" value="'.$row['code'].'">';
			echo '</td><td>';
			echo '<input type="button" value="Gem" onclick="EditTrans.updateTextCode('.$row['text_id'].');"/>';
			echo '<input type="button" value="Slet" onclick="EditTrans.deleteTextCode(&quot;'.$row['code'].'&quot;,'.$row['text_id'].');"/>';
			echo '</td></tr>';	
		}
		echo '</table>';
	}

	protected function buttonBar() {
		echo '<span style="float:left;text-align:left;clear:both;width:100%;">';
		echo '<input type="button" onclick="EditTrans.index();" value="'.trans(ZN_GOTO_FRONTPAGE).'"/>';
		echo '<input type="button" onclick="EditTrans.createTextCode();" value="Opret ny tekstkode"/>';
		echo '<span style="float:right;">';
		echo '<input type="button" onclick="EditTrans.gotoTranslations();" value="G&aring; til tekstovers&aelig;ttelse"/>';
		echo '</span>';
		echo '</span>';
		echo '<hr>';
	}


}

?>
