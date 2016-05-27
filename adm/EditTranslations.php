<?

class EditTranslations extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" src="js/edittrans.js"></script>
<?
	}

	protected function drawBody() {
		$this->buttonBar();
		echo '<div id="edit-translations"></div>';
	}

	//should be generated dynamically
	private function langSelect() {
		echo '<select id="lang_id" onchange="EditTrans.getTranslation();" style="float:left;clear:none;width:150px;">';
		echo '<option value="1">Dansk</option>';
		echo '<option value="2">English</option>';
		echo '</select>';
	}

	private function codeSelect() {
		echo '<select id="text_id" onchange="EditTrans.getTranslation();">';
		echo '<option value="">[v&aelig;lg tekstkode]</option>';
		$SQL='select * from zn_text_codes order by code ';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_array($result)) {
			echo '<option value="'.$row['text_id'].'">'.$row['code'].'</option>';
		}
		echo '</select>';
	}

	protected function buttonBar() {
		echo '<span style="float:left;text-align:left;">';
		echo '<input type="button" onclick="EditTrans.index();" value="&lt; Forside"/>';
		echo '</span>';
		echo '<span style="float:left;text-align:left;">';
		$this->langSelect();
		echo '</span>';
		echo '<span style="float:left;text-align:left;">';
		$this->codeSelect();
		echo '</span>';
		echo '<span style="float:right;">';
		echo '<input type="button" onclick="EditTrans.gotoTextCodes();" value="Gå til tekstkoder"/>';
		echo '<input type="button" onclick="EditTrans.createTranslationFile();" value="Generer sprogfil"/>';
		echo '</span>';
		echo '<hr style="clear:both;">';
	}


}

?>
