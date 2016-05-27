<?php

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('convertBase.php');

//convert svampedata.mer =>data
class FloraDanicaCSVToMySQL extends ConvertBase {

	public function run() {
		$this->loadCSV();

		//$this->createTable();
		$this->insertData();
	}

	protected function createTable() {
		$this->debug($this->getCreateTableSQL('floradanica'));
		foreach($this->records as $record) {
			$this->debug($record);
		}
		/*blev :
create table floradanica(Fascicle_code varchar(20),
Volume varchar(20),
Fascicle_number varchar(20),
Plate_number varchar(20),
Plate_Roman varchar(20),
Plate_Arabic varchar(20),
Fasc_editor varchar(20),
Fasc_year varchar(20),
OriginalName varchar(20),
original_author varchar(20),
DanishNameFD varchar(20),
CurrName varchar(20),
new_author varchar(20),
CurrFullName varchar(20),
CurrDanishName varchar(20),
AllDKNames varchar(20),
AllNames varchar(20),
Drawer varchar(20),
Editor varchar(20),
`group` varchar(20),
groups_danish varchar(20),
LangeName varchar(20),
LangeFullInfo varchar(20),
Taxon varchar(20),
Comments_danish varchar(20),
Comments_english varchar(20),
PicName varchar(20),
URLsmalPic varchar(20),
URLstorPic varchar(20))
*/
	}

	protected function getDMY($date) {
		$split = explode('-',$date);
		$result='dato_day='.$split[0].','.
			'dato_month='.$split[1].','.
			'dato_year='.$split[2];
		return $result;
	}
		
	private function imgPath($path) {
		$path=str_replace('http://130.225.211.33:1591/FloraDanicaWeb/','',$path);
		$path='FloraDanica/'.$path;
		return $path;
	}

	protected function insertData() {
		$SQL='delete from floradanica';
		$this->exec($SQL);

		/*
		$csv = array_map("str_getcsv", file($this->CSVFile, FILE_SKIP_EMPTY_LINES));
		$keys = array_shift($csv);
		foreach ($csv as $i=>$row) {
			 $csv[$i] = array_combine($keys, $row);
		}
		*/
		
		foreach($this->records as $record) {
			$this->debug($record);
			$SQL='insert into floradanica set '.
				'Fascicle_code='.$this->q($record['Fascicle code']).
				//'Fascicle_number='.$this->q($record['Fascicle number']).
				'Volume='.$this->q($record['Fascicle number']).
				'Plate_number='.$this->q($record['Plate number']).
				'Plate_Roman='.$this->q($record['Plate Roman']).
				'Plate_Arabic='.$this->q($record['Plate Arabic']).
				'Fasc_editor='.$this->q($record['Fasc editor']).
				'Fasc_year='.$this->q($record['Fasc year']).
				'OriginalName='.$this->q($record['OriginalName']).
				'original_author='.$this->q($record['original author']).
				'CurrFullName='.$this->q($record['CurrFullName']).
				'AllNames='.$this->q($record['AllNames']).
				//group is reserved word
				'`group`='.$this->q($record['group']).
				'LangeName='.$this->q($record['LangeName']).
				'LangeFullInfo='.$this->q($record['LangeFullInfo']).

				'URLsmalPic='.$this->q($this->imgPath($record['URLsmalPic'])).
				'URLstorPic='.$this->q($this->imgPath($record['URLstorPic']), false);

				echo $SQL.'<br>';
				$this->exec($SQL);
		}

		foreach($this->records as $record) {
			//$this->debug($record);
		
			/*			
			$SQL='insert into floradanica set '.
				'Fascicle_code='.$this->q($record['
				
			foreach($this->fieldNames as $field) {
				$SQL.=$field.'='.$this->q($record[$field]);
			}
			if ($record['dato']!='') {
				$SQL.=$this->getDMY($record['dato']);
			} else {
				$SQL=$this->removeLastChar($SQL);
			}
			echo $SQL.'<br>';
			$this->exec($SQL);
			*/
		}
	}
			
		
}

$faroe = new FloraDanicaCSVToMySQL('FloraDanicaData.tab');
$faroe->run();

?>
