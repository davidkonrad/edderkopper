<?php

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('convertBase.php');

class EdderkoppeTabsToMySQL extends ConvertBase {

	public function run() {
		$this->delimiter = ';'; //??
		$this->loadCSV();

		/*
		$SQL=$this->getCreateTableSQL('edderkopper');
		$this->exec($SQL);
		echo $SQL;
		*/
	
		$this->insertData();
	}

	protected function getDMY($date) {
		$split = explode('-',$date);
		$result='dato_day='.$split[0].','.
			'dato_month='.$split[1].','.
			'dato_year='.$split[2];
		return $result;
	}
	
/* old	
	protected function correctLat($lat) {
		$lat=str_replace(',','',$lat);
		$lat=preg_replace("/^(.{2})/", "$1.", $lat); 
		return $lat;
	}

	protected function correctLong($long) {
		$long=str_replace('.','',$long);
		if ($long[0]=='8' || $long[0]=='9') {
			$long=preg_replace("/^(.{1})/", "$1.", $long); 
		} else {
			$long=preg_replace("/^(.{2})/", "$1.", $long); 
		}
		return $long;
	}
*/
	
	protected function correctLatLong($ll) {
		return str_replace(',','.',$ll);
	}

	protected function normalizeDates() {
		for ($i=0;$i<count($this->records);$i++) {
			//empty date_first
			if ($this->records[$i]['Date_first']=='') {
				$this->records[$i]['Date_first']=$this->records[$i]['Date_last'];
			}
			//empty date_last
			if ($this->records[$i]['Date_last']=='') {
				$this->records[$i]['Date_last']=$this->records[$i]['Date_first'];
			}
			//empty month_first
			if ($this->records[$i]['Month_first']=='') {
				$this->records[$i]['Month_first']=$this->records[$i]['Month_last'];
			}
			//empty month_last
			if ($this->records[$i]['Month_last']=='') {
				$this->records[$i]['Month_last']=$this->records[$i]['Month_first'];
			}
			//empty yeear_first
			if ($this->records[$i]['Year_first']=='') {
				$this->records[$i]['Year_first']=$this->records[$i]['Year_last'];
			}
			//empty year_last
			if ($this->records[$i]['Year_last']=='') {
				$this->records[$i]['Year_last']=$this->records[$i]['Year_first'];
			}
		}
	}
		
	protected function insertData() {
		$this->normalizeDates();
		//setlocale(LC_ALL, 'da_DK');
		mysql_set_charset('utf8'); //important!!
		//mysql_set_charset('Latin1'); //important!!
		//mysql_set_charset('utf8_danish_ci');
		foreach($this->records as $record) {
			$SQL='insert into edderkopper set ';
			foreach($this->fieldNames as $field) {
				if ($field=='LatPrec') {
					$SQL.=$field.'='.$this->q($this->correctLatLong($record[$field]));// $this->correctLat($record[$field]));
				} elseif ($field=='LongPrec') {
					$SQL.=$field.'='.$this->q($this->correctLatLong($record[$field]));//q($this->correctLong($record[$field]));
				} else {
					if ($field!='LNR') {
						//$SQL.=$field.'='.$this->q(utf8_encode($record[$field]));
						if ($field=='Locality') {
							$test=$record[$field];
							//$test=iconv('ISO-8859-1', "UTF-8//TRANSLIT", $test);
							//$test=utf8_encode($test);
							//if (in_array($test[1], array('æ','Æ','ø','Ø','å','Å'))) echo '<br>ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZXXXXXXX<br>';
							//ISO-8859-14
							//$test=iconv("ISO-8859-1", "UTF-8//TRANSLIT", $test);
							//$enc=mb_detect_encoding($test);
							//$test=iconv($enc, "UTF-8", $test);
							//$test=iconv("Windows-1252", "UTF-8", $test);
							//$test=iconv("CP1250", "UTF-8//TRANSLIT", $test);
							//echo utf8_encode($test).'<br>';
							echo $test.'<br>';
							//$SQL.=$field.'='.$this->q(utf8_encode($test));
							$SQL.=$field.'='.$this->q($test);
						} else {
							$SQL.=$field.'='.$this->q($record[$field]);
						}
					}
				}
			}
			//echo $record['Locality'].'<br>';
			//echo utf8_decode($record['Locality']).'<br>';
			//echo iconv("UTF-8", "Latin1//TRANSLIT", $record['Locality']).'<br>';
			//$SQL=$this->removeLastChar($SQL);
			$SQL.='Name="'.$record['Genus'].' '.$record['Species'].'"';
			$this->debug($SQL);
			$this->exec($SQL);
		}
	}
}

//kør denne SQL for at opdate Genus på species
//update edderkopper_species set genus = ( select Henus from edderkopper_genus where eddderkopper_genus.genusID = edderkopper_species.GenusID)

//$tbu = new EdderkoppeTabsToMySQL('edderkopper/Edderkopper.tab');
//$tbu = new EdderkoppeTabsToMySQL('edderkopper/edderkopper2012.csv');
//$tbu = new EdderkoppeTabsToMySQL('edderkopper/edderkopper12-2012.csv');
//$tbu = new EdderkoppeTabsToMySQL('edderkopper/SpidermapAllRecNS28122012CSV.csv');
//$edder = new EdderkoppeTabsToMySQL('edderkopper/Copy of SpidermapAllRecNS28122012.csv');
//$edder->run();

?>
