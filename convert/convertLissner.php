<?php

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('convertBase.php');

class convertLissner extends ConvertBase {
	private $table;

	public function __construct($csv, $table) {
		parent::__construct($csv);
		$this->table=$table;
		$this->delimiter=',';
		$this->loadCSV();
		//echo $this->getCreateTableSQL($table);
		//$this->insertPhoto();
		$this->insertData();
	}

	private function insertPhoto() {
		$this->records = array_map(function($record) {
			return array(
				'PhotoId' => $record['PhotoId'],
				'RefId' => $record['RefId'],
				'SpeciesId' => $record['SpeciesId'],
				'LocID' => $record['LocID'],
				'SubjectDK' => $record['SubjectDK'],
				'SubjectUK' => $record['SubjectUK'],
				'Filename' => str_replace('Pictures','', $record['Filename']),
				'IsFO' => $record['IsFO'],
				'IsGL' => $record['IsGL'],
				'IsGR' => $record['IsGR'],
				'IsAZ' => $record['IsAZ'],
				'IsPS' => $record['IsPS'],
				'TStamp' => $record['TimeStamp']
			);
		}, $this->records);
		$this->fieldNames[12]='TStamp';
		$this->insertData();
	}			

	protected function insertData() {
		mysql_set_charset('utf8');
		foreach($this->records as $record) {
			$SQL='insert into '.$this->table.' set ';
			foreach($this->fieldNames as $field) {
				$SQL.=$field.'='.$this->q($record[$field]);
			}
			$SQL=$this->removeLastChar($SQL);
			$this->debug($SQL);
			$this->exec($SQL);
		}
	}

}
	
//$lissner = new convertLissner('edderkopper/tblPhoto.csv', 'edderkopper_photo');
//$lissner = new convertLissner('edderkopper/tblSpecies.csv', 'edderkopper_species');
//$lissner = new convertLissner('edderkopper/tblGenus.csv', 'edderkopper_genus');
$lissner = new convertLissner('edderkopper/tblFamily.csv', 'edderkopper_family');
?>

