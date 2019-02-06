<?

class Point {
    var $x;
    var $y;

    function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
}

class PolygonSearch {
	public $polygon = null;
	public $polygon_points = null;
	
	public $kommune_polygons = null;
	public $kommune_polygons_points = null;
	public $kommune_lookup = array();
	//
	public $lat_field;
	public $long_field;
	//
	public $center = null; //should be type of Point

	public function __construct($lat, $long) {
		$this->lat_field=$lat;
		$this->long_field=$long;
	}

	public function calcCenter($lat1, $lat2, $long1, $long2) {
		$lat=($lat1+$lat2)/2;
		$long=($long1+$long2)/2;
		$this->center=new Point($lat, $long);
	}

	public function hasPolygon() {
		return is_array($this->polygon);
	}

	public function getSQL($vars) {
		if (isset($vars['LL0'])) {
			$this->polygon=array();
			$val=0;
			while (isset($vars['LL'.$val])) {
				//is in form &LL0=(56.12573618568972, 9.751806640624977)
				$raw=$vars['LL'.$val];
				$raw=str_replace(array('(',')',' '),'', $raw);
				$poly=explode(',',$raw);
				$this->polygon[]=$poly;
				$val++;
			}
			//min max
			$latmin=90;
			$latmax=-90;
			$longmin=90;
			$longmax=-90;
			foreach ($this->polygon as $poly) {
				if ($latmin>$poly[0]) $latmin=$poly[0];
				if ($latmax<$poly[0]) $latmax=$poly[0];
				if ($longmin>$poly[1]) $longmin=$poly[1];
				if ($longmax<$poly[1]) $longmax=$poly[1];
			}
			$SQL=' ('.
			'(CAST('.$this->lat_field.' as DECIMAL(6,4))>'.$latmin.') and '.
			'(CAST('.$this->lat_field.' as DECIMAL(6,4))<'.$latmax.') and '.
			'(CAST('.$this->long_field.' as DECIMAL(6,4))>'.$longmin.') and '.
			'(CAST('.$this->long_field.' as DECIMAL(6,4))<'.$longmax.') '.
			')';

			$this->calcCenter($latmin, $latmax, $longmin, $longmax);

			return $SQL;			
		}

		if ($vars['hidden-kommune']!='') {
			return $this->createKommunePolygons($vars['hidden-kommune']);
		}

		if ($vars['hidden-habitat']!='') {
			return $this->createHabitatPolygons($vars['hidden-habitat']);
		}

		return '';
	}

	private function createPolygonPoints() {
		$this->polygon_points=array();
		foreach ($this->polygon as $p) {
			$this->polygon_points[]=new Point((float)$p[0], (float)$p[1]);
		}
		if (count($this->polygon_points)%2!==0) {
			$this->polygon_point[]=$this->polygon_points[0];
		}
	}

	private function pointInside($p,&$points) {
		//set_time_limit(60);
		$c = 0;
		$p1 = $points[0];
		$n = count($points);

		for ($i=1; $i<=$n; $i++) {
			$p2 = $points[$i % $n];
			if ($p->y > min($p1->y, $p2->y)
				&& $p->y <= max($p1->y, $p2->y)
				&& $p->x <= max($p1->x, $p2->x)
				&& $p1->y != $p2->y) {
					$xinters = ($p->y - $p1->y) * ($p2->x - $p1->x) / ($p2->y - $p1->y) + $p1->x;
					if ($p1->x == $p2->x || $p->x <= $xinters) {
						$c++;
					}
			}
			$p1 = $p2;
		}
		// if the number of edges we passed through is even, then it's not in the poly.
		return $c%2!=0;
	}

	public function isIncludeable($lat, $long) {
		if (($this->polygon==null) && ($this->kommune_polygons_points==null)) return true;

		//polygon
		if (is_array($this->polygon)) {
			if (!is_array($this->polygon_points)) { 
				$this->createPolygonPoints();
			}
			return $this->pointInside(new Point($lat, $long), $this->polygon_points);
		}

		//kommune
		if (isset($this->kommune_lookup[$lat.$long])) {
			return $this->kommune_lookup[$lat.$long];
		}
		$this->kommune_lookup[]=$lat.$long;
		foreach($this->kommune_polygons_points as $points) {
			if ($this->pointInside(new Point($lat, $long), $points)) {
				$this->kommune_lookup[$lat.$long]=true;
				return true;
			}
		}
		$this->kommune_lookup[$lat.$long]=false;
		return false;
	}

	protected function createKommunePolygons($knr) {
		$json = file_get_contents('../json/kommuner_wgs84.json');
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


/*
	protected function createKommunePolygons($knr) {
		$json = file_get_contents('http://geo.oiorest.dk/kommuner/'.$knr.'/graense.json');
		$data = json_decode($json);

		$this->kommune_polygons = array();
		$this->kommune_polygons_points = array();

		//min max
		$latmin=90;
		$latmax=-90;
		$longmin=90;
		$longmax=-90;

		foreach($data->coordinates as $poly) {
			$kommune_points=array();
			$count=0;
			foreach($poly[0] as $point) {
				if (($count % 10)==0) {
					$kommune_points[]=new Point($point[1], $point[0]);
				}
				//check min max
				if ($latmin>$point[1]) $latmin=$point[1];
				if ($latmax<$point[1]) $latmax=$point[1];
				if ($longmin>$point[0]) $longmin=$point[0];
				if ($longmax<$point[0]) $longmax=$point[0];

				$count++;
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
*/

//---------------------------

	protected function createHabitatPolygons($navn) {
//		echo 'N'.urldecode($navn);

		$json = file_get_contents('../json/habitater_27062013.json');
		//echo $json;
		$data = json_decode($json);
		
		//just reuse kommune_ variables
		$this->kommune_polygons = array();
		$this->kommune_polygons_points = array();

		//min max
		$latmin=90;
		$latmax=-90;
		$longmin=90;
		$longmax=-90;

		foreach($data->habitater as $habitat) {
			//echo $habitat->navn;
			if ($habitat->navn==$navn) {
				//echo 'OK!!';
				$coords=$habitat->coords;
				$coords=explode('/', $coords);
				//$coordsfound=$habiat;
				//$this->debug($found);
				//$this->fileDebug('XXX'.$data->coords);
				break;
			}
		}

		$kommune_points=array();
		$count=0;
		//echo $data->coords;
		foreach($coords as $poly) {
			//$poly=explode(',', $poly);;
			//echo $poly;
			$point=explode(',', $poly);
			//foreach($poly[0] as $point) {
				if (($count % 10)==0) {
					$kommune_points[]=new Point($point[1], $point[0]);
				}
				//check min max
				if ($latmin>$point[1]) $latmin=$point[1];
				if ($latmax<$point[1]) $latmax=$point[1];
				if ($longmin>$point[0]) $longmin=$point[0];
				if ($longmax<$point[0]) $longmax=$point[0];

				$count++;
			//}
		}
		$this->kommune_polygons_points[]=$kommune_points;

		$this->calcCenter($latmin, $latmax, $longmin, $longmax);

		//return min max SQL
		return '('.
			'(CAST('.$this->lat_field.' as DECIMAL(6,4))>'.$latmin.') and '.
			'(CAST('.$this->lat_field.' as DECIMAL(6,4))<'.$latmax.') and '.
			'(CAST('.$this->long_field.' as DECIMAL(6,4))>'.$longmin.') and '.
			'(CAST('.$this->long_field.' as DECIMAL(6,4))<'.$longmax.') '.
		')';
	}		


	function debug($var) {
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}

}

?>
