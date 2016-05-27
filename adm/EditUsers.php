<?

class EditUsers extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" src="js/edituser.js"></script>
<?
	}

	protected function drawBody() {
		$this->buttonBar();
		echo '<div id="edit-user"></div>';
	}

	private function userSelect() {
		$SQL='select user_id, username from zn_user ';
		$result = $this->query($SQL);
		$select = '<select name="user_id" id="user_id" onchange="EditUser.selectUser();" style="float:left;">';
		while ($row = mysql_fetch_array($result)) {
			$select.='<option value="'.$row['user_id'].'">'.$row['username'].'</option>';
		}
		$select .= '</select>';
		return $select;
	}

	protected function buttonBar() {
		echo '<span style="float:left;text-align:left;display:block;width:100%;height:40px;">';
		echo '<input type="hidden" name="lang" id="lang" value="'.$_SESSION['LANG'].'"/>';
		echo '<input type="button" onclick="EditUser.index();" value="'.trans(LAB_FRONTPAGE).'" style="float:left;"/>';
		echo '<input type="button" onclick="EditUser.createUser();" value="'.trans(LAB_USER_CREATE).'" style="float:left;"/>';
		echo $this->userSelect();
		echo '</span>';
		echo '<hr style="clear:both;">';
	}


}

?>
