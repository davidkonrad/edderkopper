<?

include('../common/Db.php');

class GeoCoding extends Db {
	protected $country_or_region;
	protected $lat;
	protected $lng;

	public function __construct() {
		parent::__construct();
		$action = isset($_GET['action']) ? $_GET['action'] : false;
		$this->country_or_region = (isset($_GET['cor'])) ? $_GET['cor'] : false;
		$this->lat = (isset($_GET['lat'])) ? $_GET['lat'] : false;
		$this->lng = (isset($_GET['lng'])) ? $_GET['lng'] : false;

		header('Content-type: application/json; charset=utf-8');

		switch($action) {
			case 'get' :
				$this->get();
				break;
			case 'put' :
				$this->put();
				break;
			default :
				echo $this->errJSON('no input');
				break;
		}
	}

	protected function errJSON($msg) {
		return '{ "error" : "'.$msg.'" }';
	}

	protected function get() {
		if (!$this->country_or_region) return;
		$SQL='select * from geocoding where country_or_region='.$this->q($this->country_or_region, false);
		$this->fileDebug($SQL);
		//mysql_set_charset('utf8_general_ci');
		mysql_set_charset('utf8');
		$result = $this->query($SQL);
		$row = @mysql_fetch_assoc($result);
		$this->fileDebug($row);
		//$result = json_encode($row, true);
		//json_encode returns "false" as string
		//$this->fileDebug($result);
		if (isset($row['lat'])) {
			$result = '{ "lat" : "'.$row['lat'].'", "lng" : "'.$row['lng'].'" }';
		} else {
			$result = $this->errJSON('notfound');
		}
		$this->fileDebug($result);
		echo $result;
		//echo ($result!='false') ? $result : 
	}

	protected function put() {
		$this->fileDebug('put : '.$this->country_or_region.' '.$this->lat.' '.$this->lng);

		if (!$this->country_or_region || !$this->lat ||	!$this->lng) return;
		$SQL='insert into geocoding (country_or_region, lat, lng) values ('.
			$this->q($this->country_or_region).
			$this->q($this->lat).
			$this->q($this->lng, false).
		')';
		$this->fileDebug($SQL);
		mysql_set_charset('utf8');
		$this->exec($SQL);
		echo $this->errJSON('ok');
	}
}

$geocoding = new GeoCoding();

?>
		




