<?
include_once('Db.php');

define('LOGIN','logged_in');
define('USERNAME','username');
define('ISADMIN','isadmin');

class Login extends Db {
	
	static public function drawLoginBox() {
		if (isset($_SESSION[LOGIN])) {
			echo '<div style="display:block;float:right;font-size:90%;width:100%;clear:both;text-align:right;"><a href="common/Login.php?action=logout">Log af</a></div>';
		} else {
			echo '<div style="float:left;width:100%;clear:both;">';
			echo '<form name="login" method="get" action="common/Login.php">';
			echo '<input type="hidden" name="action" value="login"/>';
			HTML::divider(1);
			echo 'Login<br/>';
			echo '<input type="text" name="username" style="width:90px;float:left;"/>';
			echo '<input type="password" name="password" style="width:90px;float:left;margin-left:4px;"/>';
			HTML::divider(2);
			echo '<input type="submit" value="Log ind" style="float:left;clear:both;"/>';
			echo '</form>';
			echo '</div>';
		}
	}

	static public function drawLoginBoxInline() {
		if (isset($_SESSION[LOGIN])) {
			echo '<div style="display:block;float:right;font-size:90%;width:100%;clear:both;text-align:right;"><a href="common/Login.php?action=logout">Log af</a></div>';
		} else {
			echo '<form name="login" method="get" action="common/Login.php" style="float:left;clear:none;">';
			echo '<input type="hidden" name="action" value="login">';
			echo '<input type="text" name="username" style="width:90px;float:left;">';
			echo '<input type="password" name="password" style="width:90px;float:left;margin-left:4px;">';
			echo '<input type="submit" value=" '.trans(LAB_LOGIN).'  " style="padding:0px;height:26px;">';
			echo '</form>';
		}
	}

	static public function isLoggedIn() {
		return isset($_SESSION[LOGIN]);
	}

	static public function getUsername() {
		return (isset($_SESSION[USERNAME])) ? $_SESSION[USERNAME] : '';
	}

	static public function isAdmin() {
		return $_SESSION[ISADMIN];
	}

	public function __construct() {
		session_start();
		if ($_GET['action']=='logout') {
			$this->logout();
		} else {
			parent::__construct();
			$username = isset($_GET['username']) ? $_GET['username'] : '';
			$password = isset($_GET['password']) ? $_GET['password'] : '';
			$SQL='select user_id, username, is_admin from  zn_user where username="'.$username.'" and password="'.$password.'"';
			$row=$this->getRow($SQL);
			if ($row) {
				$this->login($row['user_id'], $row['username'], $row['is_admin']);
			} else {
				$this->redirect(); 
			}
		}
	}

	private function login($user_id, $username, $isadmin) {
		$_SESSION[LOGIN]=$user_id;
		$_SESSION[USERNAME]=$username;
		$_SESSION[ISADMIN]=($isadmin==1) ? true : false;
		$this->redirect();
	}

	private function logout() {
		unset($_SESSION[LOGIN]);
		unset($_SESSION[USERNAME]);
		unset($_SESSION[ISADMIN]);
		$this->redirect();
	}
	
	private function redirect() {
		//header('location: ../index.php');
		header('location: ../edderkopper-administration');
	}

	//page_id is preferly the page semantic name, $semantic
	public function userHasRights($page_id) {
		if ($page_id=='') return true;
		if (isset($_SESSION[ISADMIN])) {
			if ($_SESSION[ISADMIN]) return true;
		}

		//remove any params from $semantic / $page_id
		$page_id=explode('?',$page_id);
		$page_id=$page_id[0];

		$SQL=(is_numeric($page_id)) 
			? 'select category_id, visible from zn_page where page_id='.$page_id
			: 'select p.category_id, p.visible from zn_page p, zn_page_content c '.
				'where c.semantic_name="'.$page_id.'" and p.page_id=c.page_id';
		$page=$this->getRow($SQL);

		//if (!Login::isLoggedIn() && ($page['visible']==0)) return false;
	
		$SQL=(Login::isLoggedIn()) 
			? 'select count(*) as c from zn_user_rights where user_id='.$_SESSION[LOGIN].' and category_id='.$page['category_id']
			: 'select count(*) as c from zn_user_rights where user_id=1 and category_id='.$page['category_id'];
		$row=$this->getRow($SQL);
		if ($row['c']<=0) return false;
	
		return true;
	}
}

if (isset($_GET['action'])) {
	$login=new Login();
}
?>
