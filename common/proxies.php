<?

class Proxy {

	/*
		converts an associated array (typically a db-result) into two arrays in [] javascript format
		["key1", "key2", ..] and [value1, value2 ..]
		returned in referenced parameters &$js and &$js2
	*/
	public static function assocToJS($assoc, &$js1, &$js2, $valueIsNumber=true) {
		$n='';
		$s='';
		foreach($assoc as $key=>$value) {
			if ($n!='') {
				$n.=',';
				$s.=',';
			}
			$n.='"'.$key.'"';
			if ($valueIsNumber) {
				$s.=$value;
			} else {
				$s.='"'.$value.'"';
			}
		}
		$js1='['.$n.']';
		$js2='['.$s.']';
	}

	/*
		trim string for " " and control characters
	*/
	public static function trimLine($string) {
		//  Character      Decimal      Use
		//  "\0"            0           Null Character
		//  "\t"            9           Tab
		//  "\n"           10           New line
		//  "\x0B"         11           Vertical Tab
		//  "\r"           13           New Line in Mac
		//  " "            32           Space
       
		$replace=array("\0","\t","\n","\x0B","\r");
		return str_replace($replace, '', $string);
	}

	/*
		convert a mysql dataset to JSON
		$result = mysql_query $result
		$field = mysql result field
		$name = JSON name, eg { "Name" : { [ field, field ..] }
	*/
	public static function resultToJSON($result, $field, $name) {
		$JSON='';
		while ($row = mysql_fetch_assoc($result)) {
			if ($JSON!='') $JSON.=',';
			$value=$row[$field];
			$value=str_replace('"','',$value);
			$value=Proxy::trimLine($value);
			$JSON.='"'.$value.'"';
		}
		$JSON='{ "'.$name.'": ['.$JSON.'] }';
		return $JSON;
	}

	/* 
		formats d m y to danish day/month/year
		considering days often can be empty in older datasets and so
		if $echo is true, the result will be echoed and not returned 
	*/
	public static function formatDateDK($day, $month, $year, $echo=false) {
		$day = (trim($day)!='') ? $day : '??';
		$month = (trim($month)!='') ? $month : '??';
		$year = (trim($year)!='') ? $year : '????';

		if (strlen($day)==1) $day='0'.$day;
		if (strlen($month)==1) $month='0'.$month;

		$result = $day.'/'.$month.'/'.$year;

		if ($echo) {
			echo $result;
		} else {
			return $result;
		}
	}

	/* 
		format lat lngs to lat / lng or ? / ?
	*/
	public static function formatLatLng($lat, $lng) {
		$lat = $lat!='' ? $lat : '?';
		$lng = $lng!='' ? $lng : '?';
		return $lat.' / '.$lng;
	}
		
		
}

?>
