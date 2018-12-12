<?

ini_set('display_errors', '1');
error_reporting(E_ALL);

//TemplateBase, ancestor to all templates
class TemplateBase extends Db {
	public function __construct() {
		parent::__construct();
	}

	protected function loggedIn() {
		return isset($_SESSION[LOGIN]);
	}

	protected function isAdmin() {
		if (!isset($_SESSION[ISADMIN])) return false;
		return $_SESSION[ISADMIN];
	}

	//is the user server or located at SNM / geologisk?
	protected function isSNM() {
		$snm = array('127.0.0.1', '::1', '192.38.112', '192.38.113', '192.38.114', '130.225.');
		$ip = $_SERVER['REMOTE_ADDR'];
		foreach ($snm as $s) {
			if (strpos($ip, $s)>-1) return 'true';
		}
		return 'false';
	}
	
	protected function drawSessLang() {
		echo '<input type="hidden" name="sess_lang" value="'.$_SESSION[LANGUAGE].'"/>'."\n";
	}

	//from Base.php
	//return language specific URL's for page_id
	protected function getLangLinks($page_id) {
		$links=array();
		$SQL='select lang_id, semantic_name from zn_page_content where page_id='.$page_id.' order by lang_id';
		$result=$this->query($SQL);
		//while ($row = mysql_fetch_assoc($result)) {
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$href=$row['semantic_name'];
			$href=str_replace(' ', '%20', $href);
			$links[$row['lang_id']]=$href;
		}
		return $links;
	}	

	//google analytics account
	protected function GA() {
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-60706573-1', 'auto');
  ga('send', 'pageview');

</script>
<?
	}


	//get all pages associated with CURRENT_PAGE_ID
	protected function getRelatedContent($includeSelf=false) {
		$links=array();
		$visible=(Login::isLoggedIn()) ? 'visible>=1 ' : 'visible=1';
		if (isset($_SESSION[CURRENT_PAGE_ID])) {
			$SQL='select category_id from zn_page where page_id='.$_SESSION[CURRENT_PAGE_ID];
			$res=$this->getRow($SQL);
			$cat=$res['category_id'];

			$SQL='select p.page_id, p.kolofon, c.anchor_caption, c.anchor_title, c.semantic_name '.
				'from zn_page p, zn_page_content c '.
				'where c.lang_id='.$_SESSION[LANGUAGE].' and c.page_id=p.page_id ';

			if (!$includeSelf) {
				$SQL.='and p.page_id<>'.$_SESSION[CURRENT_PAGE_ID].' ';
			}

			$SQL.='and p.category_id='.$cat.' and '.$visible.' order by weight';

			//mysql_set_charset('Latin1');
			$this->setLatin1();
			$result = $this->query($SQL);
			//$this->debug($result);
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$links[]=$row;
			}
			return $links;
		}
	}

	
}

?>
