<?php

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('convertBase.php');

//convert svampedata.mer =>data
class TBUEMerToMySQL extends ConvertBase {

	public function run() {
		$this->loadCSV();
		$this->insertData();
	}

	protected function getDMY($date) {
		$split = explode('-',$date);
		$result='dato_day='.$split[0].','.
			'dato_month='.$split[1].','.
			'dato_year='.$split[2];
		return $result;
	}
		
	protected function insertData() {
		$SQL='delete from tbu_kartoteket';
		$this->exec($SQL);

		foreach($this->records as $record) {
			$SQL='insert into tbu_kartoteket set ';
			foreach($this->fieldNames as $field) {
				if ($field!="LayerCalc") {
					$SQL.=$field.'='.$this->q($record[$field]);
				}
			}
			/*
			if ($record['dato']!='') {
				$SQL.=$this->getDMY($record['dato']);
			} else {
				$SQL=$this->removeLastChar($SQL);
			}
			*/
			$SQL=$this->removeLastChar($SQL);
			$this->debug($SQL);
			$this->exec($SQL);
		}
	}
			
		
}

$tbu = new TBUEMerToMySQL('exports/tbue_ny.mer');
$tbu->run();

?>
