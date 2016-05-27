<?

include('../common/Db.php');

class EditTranslations extends Db {

	//assumes $_GET -> text_id, lang_id, translation
	public function __construct() {
		parent::__construct();
		//header('Content-Type: text/html; charset=ISO-8859-1');
		switch ($_GET['action']) {
			case 'get' : $this->getTranslation(); break;
			case 'update' : $this->updateTranslation(); break;
			case 'file' : $this->getTranslationFile(); break;
			default : break;
		}	
	}

	private function encode($s) {
		$s=iconv('latin1','UTF8',$s);

		$s=str_replace('æ', '&aelig;', $s);
		$s=str_replace('Æ', '&AElig;', $s);
		$s=str_replace('ø', '&oslash;', $s);
		$s=str_replace('Ø', '&Oslash;', $s);
		$s=str_replace('å', '&aring;', $s);
		$s=str_replace('Å', '&Aring;', $s);

		echo $s;
		return $s;
	}

	private function decode($s) {
		$s=str_replace('&aelig;', 'æ', $s);
		$s=str_replace('&AElig;', 'Æ', $s);
		$s=str_replace('&oslash;', 'ø', $s);
		$s=str_replace('&Oslash;', 'Ø', $s);
		$s=str_replace('&aring;', 'å', $s);
		$s=str_replace('&Aring;', 'Å', $s);
		return $s;
	}

	private function createTranslation() {
		$SQL='insert into zn_text_translations (text_id, lang_id) values ('.
			$this->q($_GET['text_id']).
			$this->q($_GET['lang_id'], false).
			')';
		$this->exec($SQL);
	}

	private function getTranslation() {
		$SQL='select t.translation, c.code '.
			'from zn_text_translations t, zn_text_codes c '.
			'where t.text_id='.$_GET['text_id'].' and t.text_id=c.text_id '.
			'and t.lang_id='.$_GET['lang_id'];
		
		if (!$this->hasData($SQL)) {
			$this->createTranslation();
		}

		$row=$this->getRow($SQL);
		$trans=$this->decode($row['translation']);
		echo '<h3>'.$row['code'].'</h3>';
		echo '<textarea id="translation" style="width:500px;height:250px;">'.$trans.'</textarea>';
		echo '<br>';
		echo '<input type="button" value="Gem / opdater" onclick="EditTrans.saveTranslation();"/>';
		echo '&nbsp;<span id="edit-translation-msg"></span>';
	}

	private function updateTranslation() {
		//echo urldecode($_GET['translation']);
		//echo $_GET['translation'];
		$trans = $this->encode($_GET['translation']);

		$SQL='update zn_text_translations set translation='.$this->q($trans, false).' '.
			'where text_id='.$_GET['text_id'].' and lang_id='.$_GET['lang_id'];
		$this->exec($SQL);
	}

	private function br() {
		echo '<br>';
	}
	
	private function getTranslationFile() {
		$SQL='select name from zn_languages where lang_id='.$_GET['lang_id'];
		$row=$this->getRow($SQL);
		$language=$row['name'];
		echo '&lt;?';
		echo '<br>';		
		echo "// SNM CMS translation file<br>";	
		echo "// ".$language."<br><br>";

		$SQL='select t.translation, c.code from zn_text_translations t, zn_text_codes c '.
			'where t.lang_id='.$_GET['lang_id'].' and t.text_id=c.text_id order by c.code ';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_array($result)) {
?>
define("<? echo $row['code'];?>", "<? echo htmlentities($row['translation']);?>");
<br>
<?
		}
		echo '?&gt;';
	}
}

if (isset($_GET['action'])) {
	$textcode = new EditTranslations();
}

?>
