<?

define('ARROW_DOWN', '&#9660;');
define('ARROW_UP', '&#9650;');
define('ARROW_LEFT', '&#9668;');
define('ARROW_RIGHT', '&#9658;');
define('DEGREE', '&#176;');
define('SPACE', '&nbsp;');


class HTML {
	
	static public function h1($caption) {
		echo '<h1>'.$caption.'</h1>'."\n";
	}

	static public function h2($caption) {
		echo '<h2>'.$caption.'</h2>'."\n";
	}

	static public function h3($caption) {
		echo '<h3>'.$caption.'</h3>'."\n";
	}

	static public function p($text) {
		echo '<p>'.$text.'</p>'."\n";
	}

	static public function nb($count=1) {
		for ($i=1;$i<$count;$i++) {
			echo '&nbsp;';
		}
	}

	static public function br($count=1) {
		for ($i=1;$i<$count;$i++) {
			echo '<br>';
		}
	}

	static public function span($content, $class='') {
		echo '<span class="'.$class.'">'.$content.'</span>'."\n";
	}

	static public function hr($class='' ) {
		echo ($class!='') ? '<hr class="'.$class.'">' : '<hr>';
	}

	static public function divider($size) {
		echo '<div class="divider" style="height:'.$size.'px;"></div>';
	}

	static public function rightText($text) {
		echo '<span style="float:right;">'.$text.'</span>';
	}

	static public function leftText($text) {
		echo '<span style="float:left;">'.$text.'</span>';
	}
	
	static public function getOption($val, $caption, $value) {
		$sel = ($val==$value) ? ' selected="selected"' : '';
		return '<option value="'.$val.'"'.$sel.'>'.$caption.'</option>';
	}

	static public function selectYesNo($name, $value) {
		echo '<select name="'.$name.'" id="'.$name.'" style="width:80px;">';
		echo self::getOption(1, 'Ja', $value);
		echo self::getOption(0, 'Nej', $value);
		echo '</select>';
	}

	static public function selectWeight($value) {
			echo '<select id="weight" name="weight" style="width:80px;">';
			for ($i=-10;$i<=10;$i++) {
				if ($i==$value) {
					echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
				} else {
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
			}
			echo '</select>';
	}

	//this is hardcoded, no need to make defined constants here
	static public function selectStatus($selected='') {
		switch ($_SESSION[LANGUAGE]) {
			case 2 : 
				$a=array(''=>'Select category','Ex'=>'Extinct',"E"=>'Endangered',"V"=>'Vulnerable',"R"=>'Rare');
				break;
			default : 
				$a=array(''=>'V&aelig;lg kategori','Ex'=>'Udd&oslash;d',"E"=>'Truet',"V"=>'S&aring;rbar',"R"=>'Sj&aelig;lden');
				break;
		}
		echo '<select id="status" name="status">';
		foreach($a as $key => $value) {
			echo self::getOption($key, $value, $selected);
		}
		echo '</select>';
	}
	
	static public function countrySelect($name, $id, $style='') {
	?>		
	<select size="1" style="<? echo $style;?>" name="<? echo $name;?>" id="<? echo $id;?>">
                <option value="Denmark" selected="selected">Denmark</option>
                <option value="*">Alle lande/All countries </option>
                <option value="Afghanistan">Afghanistan</option>
                <option value="Andorra">Andorra</option>
                <option value="Argentina">Argentina</option>
                <option value="Armenia">Armenia</option>
                <option value="Australia">Australia</option>
                <option value="Austria">Austria</option>
                <option value="Azores">Azores</option>
                <option value="Belgium">Belgium</option>
                <option value="Bhutan">Bhutan</option>
                <option value="Bosnia Herzegovina">Bosnia Herzegovina</option>
                <option value="Brazil">Brazil</option>
                <option value="Bulgaria">Bulgaria</option>
                <option value="Cameroon">Cameroon</option>
                <option value="Canada">Canada</option>
                <option value="Chile">Chile</option>
                <option value="China">China</option>
                <option value="Croatia">Croatia</option>
                <option value="Cuba">Cuba</option>
                <option value="Cyprus">Cyprus</option>
                <option value="Czech Rep.">Czech Rep.</option>
                <option value="Dominican Rep. ">Dominican Rep. </option>
                <option value="Ecuador">Ecuador</option>
                <option value="Estonia">Estonia</option>
                <option value="Ethiopia">Ethiopia</option>
                <option value="Faroe Islands">Faroe Islands</option>
                <option value="Fiji">Fiji</option>
                <option value="Finland">Finland</option>
                <option value="France">France</option>
                <option value="Georgia">Georgia</option>
                <option value="Germany">Germany</option>
                <option value="Greece">Greece</option>
                <option value="Greenland">Greenland</option>
                <option value="Grenada">Grenada</option>
                <option value="Hungary">Hungary</option>
                <option value="Iceland">Iceland</option>
                <option value="India">India</option>
                <option value="Indonesia">Indonesia</option>
                <option value="Israel">Israel</option>
                <option value="Italy">Italy</option>
                <option value="Japan">Japan</option>
                <option value="Kazakhstan">Kazakhstan</option>
                <option value="Kenya">Kenya</option>
                <option value="Kirghizia">Kirghizia</option>
                <option value="Latvia">Latvia</option>
                <option value="Lithaunia">Lithaunia</option>
                <option value="Luxembourg">Luxembourg</option>
                <option value="Madagascar">Madagascar</option>
                <option value="Malaysia">Malaysia</option>
                <option value="Mexico">Mexico</option>
                <option value="Morocco">Morocco</option>
                <option value="Netherlands">Netherlands</option>
                <option value="New Zealand">New Zealand</option>
                <option value="Nicaragua">Nicaragua</option>
                <option value="Norway">Norway</option>
                <option value="Papua New Guinea">Papua New Guinea</option>
                <option value="Paraguay">Paraguay</option>
                <option value="Peru">Peru</option>
                <option value="Philippines">Philippines</option>
                <option value="Poland">Poland</option>
                <option value="Portugal">Portugal</option>
                <option value="Romania">Romania</option>
                <option value="Russia">Russia</option>
                <option value="Saudi Arabia">Saudi Arabia</option>
                <option value="Slovakia">Slovakia</option>
                <option value="Slovenia">Slovenia</option>
                <option value="Spain">Spain</option>
                <option value="Sri Lanka">Sri Lanka</option>
                <option value="St. Lucia">St. Lucia</option>
                <option value="Svalbard">Svalbard</option>
                <option value="Sweden">Sweden</option>
                <option value="Switzerland">Switzerland</option>
                <option value="Tanzania">Tanzania</option>
                <option value="Thailand">Thailand</option>
                <option value="Czech Rep.">Tjekkia/Czech Rep.</option>
                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                <option value="Ukraine">Ukraine</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="U.S.A">United States</option>
                <option value="Venezuela">Venezuela</option>
                <option value="Zimbabwe">Zimbabwe</option>
              </select>
	<?
	}
}

?>
