<?

class Lang {

	static public function flagMenu($links = false) {
		echo '<span id="flag-menu" class="flag-menu-cnt">';
		if (!$links) {
			for ($i=1;$i<=2;$i++) {
				$selected=($_SESSION[LANGUAGE]==$i);
				Lang::drawLangIcon($i, $selected, 'index.php?lang='.$i);
				echo '&nbsp;';
			}
		} else {
			$current=$_SERVER['REQUEST_URI'];
			foreach ($links as $lang=>$url) {
				$p=pathinfo($current);
				$selected=($p['basename']==$url);
				Lang::drawLangIcon($lang, $selected, $url);
				echo '&nbsp;';
			}
		}
		echo '</span>';
	}

	static private function drawLangIcon($id, $selected, $url) {
		$src=($id==1) ? 'ico/wdk.gif' : 'ico/wgb.gif';
		$title=($id==1) ? 'Dansk' : 'English';
		$border=($selected) ? ' style="border:1px solid green;"' : ' style="border:1px solid transparent;"';
		echo '<a href="'.$url.'" title="'.$title.'"><img src="'.$src.'" width="20"'.$border.' alt="'.$title.'"></a>';
	}

	static public function load() {
		if (defined('ZN_PAGE_HEADER')) return;

		if (!isset($_SESSION[LANGUAGE])) {
			$_SESSION[LANGUAGE]=1;
		}

		switch ($_SESSION[LANGUAGE]) {
			case 2 : 
				include_once('lang/english.php'); 
				break;
			default :
			case 1 : 
				include_once('lang/dansk.php'); 
				break;
		}
	}

	static public function lang_id() {
		return $_SESSION[LANGUAGE];
	}

}

?>
