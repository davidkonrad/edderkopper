<?

include('../../common/Db.php');

class OffendingChars extends Db {
	private $table;

	public function __construct() {
		parent::__construct();
		$this->table=(isset($_GET['table'])) ? $_GET['table'] : false;
		if ($this->table) {
			$this->run();
		} else {
			echo 'Ingen tabel angivet';
		}
	}

	private function run() {
		$SQL='show columns from '.$this->table;
		$result=$this->query($SQL);
		$html='<table>';
		$html.='<thead><tr><th>Field</th><th></th><th>Kommentar</th></tr></thead><tbody>';
		while ($row = mysql_fetch_assoc($result)) {
			$this->fileDebug('processing '.$row['Field']);
			$html.='<tr>';
			$html.='<td>'.$row['Field'].'</td>';
			$html.='<td>&nbsp;</td>';
			
			if ((strpos($row['Type'], 'varchar') !== false) ||
				(strpos($row['Type'], 'text') !== false)) {
				$count=$this->processField($row['Field']);
			} else {
					$count=-1;
			}
			
			switch ($count) {
				case -1 :
					$html.='<td>Ej tekstfelt</td>';
					break;
				case 0 : 
					$html.='<td>Ingen kontroltegn</td>';
					break;
				default : 
					$html.='<td>'.$count.' kontroltegn fjernet</td>';
					break;
			}

			$html.='</tr>';
		}
		$html.='<tbody></table>';
		echo $html;
	}

	private function processField($field) {
		$count=0;

		$SQL='update '.$this->table.' set `'.$field.'` = replace(`'.$field.'`, "\n", "")';
		$this->exec($SQL);
		$count=$count+(Integer)mysql_affected_rows();

		$SQL='update '.$this->table.' set `'.$field.'` = replace(`'.$field.'`, "\r", "")';
		$this->exec($SQL);
		$count=$count+mysql_affected_rows();

		$SQL='update '.$this->table.' set `'.$field.'` = replace(`'.$field.'`, "\t", " ")';
		$this->exec($SQL);
		$count=$count+mysql_affected_rows();

		$SQL='update '.$this->table.' set `'.$field.'` = replace(`'.$field.'`, CHAR(11), CHAR(32))';
		$this->exec($SQL);
		$count=$count+mysql_affected_rows();

		//23.01.2014
		$SQL='update '.$this->table.' set `'.$field.'` = replace(`'.$field.'`, CHAR(21), CHAR(32))';
		$this->exec($SQL);
		$count=$count+mysql_affected_rows();

		return $count;
	}

		
}

$oc = new OffendingChars();

?>
