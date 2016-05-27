<?

error_reporting(E_ALL);
ini_set('display_errors', '1');

include('../common/Db.php');

class SaveRecord extends Db {
	private $project_id;
	private $id;
	private $taxon;
	private $label;
	private $creator;
	private $image;
	private $password;

	public function __construct() {
		parent::__construct();

		$this->id = (isset($_GET['id'])) ? $_GET['id'] : '';
		$this->project_id = (isset($_GET['project'])) ? $_GET['project'] : '';
		$this->taxon = (isset($_GET['taxon'])) ? $_GET['taxon'] : '';
		$this->label = (isset($_GET['label'])) ? $_GET['label'] : '';
		$this->creator = (isset($_GET['creator'])) ? $_GET['creator'] : '';
		$this->image = (isset($_GET['image'])) ? $_GET['image'] : '';
		$this->password = (isset($_GET['password'])) ? $_GET['password'] : '';

		$action = (isset($_GET['action'])) ? $_GET['action'] : 'save';
		switch ($action) {
			case 'save': $this->save(); break;
			case 'delete': $this->delete(); break;
			case 'update': $this->update(); break;
			default : break;
		}
	}

	protected function checkPassword() {
		$SQL='select * from webcam_projects where id='.$this->project_id.' and password="'.$this->password.'"';
		$result=$this->query($SQL);
		return ($result!=false);
	}

	protected function save() {
		$SQL='insert into webcam (project_id, creator, label, taxon, jpeg_image) values('.
		$this->q($this->project_id).
		$this->q($this->creator).
		$this->q($this->label).
		$this->q($this->taxon).
		$this->q($this->image, false).
		')';

		$this->exec($SQL);
	}

	protected function update() {
		if ($this->checkPassword()) return;
		$SQL='update webcam set '.
			'label='.$this->q($this->label).
			'taxon='.$this->q($this->taxon, false).
			' where id="'.$this->id.'"';
		echo $SQL;
		$this->exec($SQL);
	}

	protected function delete() {
		if ($this->checkPassword()) return;
		$SQL='delete from webcam where id='.$this->id;
		$this->exec($SQL);
	}

}

$save = new SaveRecord();

?>
