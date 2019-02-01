<?

class Test {

	function debug($o) {
		echo '<pre>';
		print_r($o);
		echo '</pre>';
	}

	function createKommunePolygons($knr) {
		$json = file_get_contents('json/kommuner_wgs84.json');
		$data = json_decode($json);

		$this->kommune_polygons = array();
		$this->kommune_polygons_points = array();

		foreach ($data->kommuner_WGS84 as $kommune) {

			if ($kommune->knr == $knr) {
				$border = $kommune->border;
				break;
			}
		}

		if (!isset($border)) return;

		//min max
		$latmin=90;
		$latmax=-90;
		$longmin=90;
		$longmax=-90;

		$kommune_points=array();

		foreach($border as $coords) {
			$this->debug($coords);
			$points = explode('/', $coords->coords);
			//$count=0;
			foreach($points as $point) {
				$point = explode(',', $point);
				$kommune_points[]=new Point($point[1], $point[0]);
				//check min max
				if ($latmin>$point[1]) $latmin=$point[1];
				if ($latmax<$point[1]) $latmax=$point[1];
				if ($longmin>$point[0]) $longmin=$point[0];
				if ($longmax<$point[0]) $longmax=$point[0];
			}
			$this->kommune_polygons_points[]=$kommune_points;
		}

		$this->calcCenter($latmin, $latmax, $longmin, $longmax);

		//return min max SQL
		return '('.
			'(CAST('.$this->lat_field.' as DECIMAL(6,4))>'.$latmin.') and '.
			'(CAST('.$this->lat_field.' as DECIMAL(6,4))<'.$latmax.') and '.
			'(CAST('.$this->long_field.' as DECIMAL(6,4))>'.$longmin.') and '.
			'(CAST('.$this->long_field.' as DECIMAL(6,4))<'.$longmax.') '.
		')';
	}		
}

$t = new Test();
$t->createKommunePolygons('0153');

?>

