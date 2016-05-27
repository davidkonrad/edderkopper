<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<!--
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
-->
</head>
<body>

<?php

//debug

error_reporting(E_ALL);
ini_set('display_errors', '1');

include('convertBase.php');

class BilleKatalogToMySQL extends ConvertBase {

	public function run() {
		/*
		//array_filter($array_with_nulls, 'strlen');
		$test=array('a'=>null, 'b'=>'');
		$this->debug($test);
		//if (empty($test)) {
		
		$test=array();
		$test[]=Array('client_id' => '3680', 'firstname' => 'Brian', 'surname' => 'May', 'company' => '');
		$test[]=Array('client_id' => '1111', 'firstname' => 'Brianxxx', 'surname' => 'Jun', 'company' => '');
		$test[]=Array('client_id' => '6666', 'firstname' => 'Brianyyy', 'yesname' => 'Jul', 'company' => '');

		
		/*
		if (count(array_filter($test['company'], 'strlen'))==0) {
		//$this->debug(array_intersect_key($test, array(array('client_id'=>''))));
		//if (array_intersect_key($test, array('company'=>''))) {
			echo 'empty';
		} else {
			echo 'not empty';
		}
		*/
		ini_set("auto_detect_line_endings", true);
		$this->delimiter=$this->detectDelimiter();
		$this->loadCSV();

		foreach ($this->records as $record) {
			$this->debug($record);
		}

		$this->insertData();
		echo count($this->records);
	}

	protected function getDMY($date) {
		if (strlen($date)!=8) return '';
		$ret=',';
		$ret.='dato_day='.$this->q(substr($date,0,2));
		$ret.='dato_month='.$this->q(substr($date,2,2));
		$ret.='dato_year='.$this->q(substr($date,4,4), false);
		return $ret;
	}
		
	private function getTaxonId($taxon_name) {
		$SQL='select taxon_id from billekatalog_taxon where taxon_name="'.$taxon_name.'"';
		$result=$this->query($SQL);
		echo $SQL;
		if (mysql_num_rows($result)>0) {
			echo '....OK! <br>';
			$row=mysql_fetch_array($result);
			return $row['taxon_id'];
		}
		return false;
	}

	protected function insertData() {
		$SQL='delete from billekatalog';
		$this->exec($SQL);

		$SQL='delete from billekatalog_taxon';
		$this->exec($SQL);

		mysql_set_charset('Latin1');

		$this->debug(iconv_get_encoding('all'));

		foreach($this->records as $record) {
			//get or create taxon_id
			$taxon_id=$this->getTaxonId($record['Taxon']);
			if ($taxon_id==false) {
				$SQL='insert into billekatalog_taxon (taxon_name) values ('.$this->q($record['Taxon'], false).')';
				echo $SQL.'<br>';
				$this->exec($SQL);
				$taxon_id=mysql_insert_id();
			}
			/*
			//$record[$key]=iconv('utf8', 'latin1', $value);
			foreach($record as $key=>$value) {
				echo $value.' ';
				//echo iconv('cp850', 'latin1', $value).' ';
				//echo iconv('LATIN1//TRANSLIT//IGNORE', 'utf8', $value).' ';
				//echo iconv('ISO-8859-1', 'latin1', $value).' ';
				//echo iconv('ascii', 'latin1', $value).' ';
				//echo iconv('utf8', 'latin1', $value).' ';
				//echo iconv('ISO-8859-1', 'UTF-8', $value).' ';
				//echo iconv('windows-1252', 'latin1', $value).' ';
				//echo utf8_decode($value).' ';
				//echo iconv('ISO-8859-1', 'utf-8', $value).' ';
				//echo iconv('latin1', 'utf8', $value).' ';
				//echo iconv('us-ascii', 'latin1', $value).' ';
				//echo mb_detect_encoding($value).' ';
			}
			echo '<br>';
			break;
			*/

			//insert record
			$SQL='insert into billekatalog set '.
				'taxon_id='.$this->q($taxon_id).
				'distrikt='.$this->q($record['Distrikt']).
				'lokalitet='.$this->q($record['Lokalitet']).
				'dato='.$this->q($record['Dato']).
				'datoerr='.$this->q($this->arrVal($record, 'DatoErr')).
				'datocorr='.$this->q($this->arrVal($record, 'DatoCorr')).
				'leg='.$this->q($record['Leg']).
				'Det='.$this->q($record['Det']).
				'coll='.$this->q($record['Coll']).
				'note='.$this->q($record['Note']).
				'error='.$this->q($record['Error']).
				'kommentar='.$this->q($record['Kommentar'], false);
			$SQL.=$this->getDMY($record['Dato']);
			$this->exec($SQL);
			$this->debug($SQL);
		}
	}
			
		
}

//$biller = new BilleKatalogToMySQL('billekatalog/billekatalog5.mer');
//$biller = new BilleKatalogToMySQL('billekatalog/BillekatalogComplete2.mer');
$biller = new BilleKatalogToMySQL('billekatalog/BillekatalogComplete3.mer');
$biller->run();

?>
