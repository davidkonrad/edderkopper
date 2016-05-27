<?

include('../common/Db.php');

class EditTextCode extends Db {

	public function __construct() {
		parent::__construct();
		switch ($_GET['action']) {
			case 'add' : $this->addTextCode(); break;
			case 'update' : $this->updateTextCode(); break;
			case 'delete' : $this->deleteTextCode(); break;
			default : break;
		}
	}

	private function addTextCode() {
		$SQL='insert into zn_text_codes (code) values ('.$this->q($_GET['code'], false).')';
		$this->exec($SQL);
	}

	private function deleteTextCode() {
		$SQL='delete from zn_text_codes where text_id='.$_GET['text_id'];
		$this->exec($SQL);
		//delete translations
		$SQL='delete from zn_text_translations where text_id='.$_GET['text_id'];
		$this->exec($SQL);
	}

	private function updateTextCode() {
		$SQL='update zn_text_codes set code='.$this->q($_GET['code'], false).' where text_id='.$_GET['text_id'];
		$this->exec($SQL);
	}

}

if (isset($_GET['action'])) {
	$textcode = new EditTextCode();
}

?>
