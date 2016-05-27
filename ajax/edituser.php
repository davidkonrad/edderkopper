<?

include('AjaxBase.php');

class EditUser extends AjaxBase {

	public function __construct() {
		parent::__construct();
		$id=(isset($_GET['user_id'])) ? $_GET['user_id'] : -1;
		switch ($_GET['action']) {
			case 'create' : $this->createUser(); break;
			case 'update' : $this->updateUser($id); break;
			case 'select' : $this->showUser($id); break;
			default : break;
		}
	}

	private function createUser() {
		$SQL='select max(user_id) as id from zn_user';
		$row=$this->getRow($SQL);
		$id=$row['id']+1;
		$SQL='insert into zn_user (username, password) values("username'.$id.'", "password'.$id.'")';
		$this->exec($SQL);
		$this->showUser(mysql_insert_id());
	}
		
	private function updateUser($id) {
		$SQL='update zn_user set '.		
			'username='.$this->q($_GET['username']).
			'password='.$this->q($_GET['password'], false).' '.
			'where user_id='.$id;
		$this->exec($SQL);

		$SQL='delete from zn_user_rights where user_id='.$id;
		$this->exec($SQL);

		foreach($_GET as $key=>$value) {
			if (strpos($key,'cat_')>-1) {
				$s=@split('_',$key); //split is deprecated
				$SQL='insert into zn_user_rights (user_id, category_id) '.
					'values('.$_GET['user_id'].','.$s[1].')';
				$this->exec($SQL);
			}
		}

		$this->showUser($id);
	}

	private function showUser($id) {
		$SQL='select * from zn_user where user_id='.$id;
		$row=$this->getRow($SQL);
		$dis = ($id==1) ? ' disabled="disabled"' : '';
?>
<table>
<tr>
<td><? trans(LAB_USERNAME, true);?></td><td><input name="username" id="username" value="<? echo $row['username']; ?>"<? echo $dis;?>/></td>
</tr>
<tr>
<td><? trans(LAB_PASSWORD, true);?></td><td><input name="password" id="password" value="<? echo $row['password']; ?>"<? echo $dis;?>/></td>
</tr>
<?
		$SQL='select category_id, caption from zn_category_desc '.
			'where lang_id='.$_GET['lang'].' order by category_id';
		$result=$this->query($SQL);
		while($row = mysql_fetch_array($result)) {
			$SQL='select * from zn_user_rights where user_id='.$id.' and category_id='.$row['category_id'];
			$checked = $this->hasData($SQL) ? ' checked="checked"' : '';
			$catid='cat_'.$row['category_id'];
			echo '<tr><td></td><td>';
			echo '<input type="checkbox" id="'.$catid.'"'.$checked.' style="vertical-align:middle;"/>';
			echo '<label for="'.$catid.'" style="vertical-align:middle;">'.trans($row['caption']).'</label>';
			echo '</td></tr>';
		}
		echo '<tr><td colspan=2><hr></td></tr>';
		echo '<tr><td></td><td><input type="button" onclick="EditUser.saveUser();" value="'.trans(LAB_SAVE).'"/></td></tr>';
?>
</table>
<?
	}
}

$user = new EditUser();

?>
