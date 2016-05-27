<?php

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '0');
error_reporting(0); //0 E_ALL


class Db {
	private $database;
	private $hostname;
	private $username;
	private $password;
	private $link;
	private $host;
  
	public static function getInstance(){
		static $db = null;
		if ( $db == null ) $db = new Db();
		return $db;
	}

	public function __construct() { 
		$this->host = $_SERVER["SERVER_ADDR"]; 
		if (($this->host=='127.0.0.1') || ($this->host=='::1')) {
			/*
			$this->database = 'samlingerne';
			$this->hostname = 'localhost';
			$this->username = 'root';
			$this->password = 'zoo';
			*/
			$this->database = 'edderkopper';
			$this->hostname = 'localhost';
			$this->username = 'root';
			$this->password = 'dadk';

		} else {
			$this->database = 'danmarks_edderk';
			$this->hostname = 'localhost';
			$this->username = 'danmarks_edderk';
			$this->password = 'PwH5nbki';
		}

		try {
			$this->link=mysql_connect($this->hostname,
						  $this->username,
						  $this->password);
			if (!$this->link) {
				die('Could not connect: ' . mysql_error());
			} else {
				mysql_select_db ($this->database);
			}

		} catch (Exception $e){
			throw new Exception('Could not connect to database.');
			exit;
 		}
	}

	public function setUTF8() {
		mysql_set_charset('UTF8',$this->link);
	}

	public function setLatin1() {
		mysql_set_charset('Latin1',$this->link);
	}

	public function setASCII() {
		mysql_set_charset('ASCII',$this->link);
	}

	public function exec($SQL) {
		mysql_query($SQL);
	}

	public function query($SQL) {
		$result=mysql_query($SQL);
		return $result;
	}

	public function getRow($SQL) {
		try {
			$result=mysql_query($SQL);
			$result=mysql_fetch_array($result);
			return $result;
		} catch (Exception $e) {
		    echo 'Database error : ',  $e->getMessage(), "\n".$SQL;
		}
	}

	public function hasData($SQL) {
		$result=mysql_query($SQL);
		return is_array(@mysql_fetch_array($result));
	}

	public function getRecCount($table) {
		$SQL='select count(*) from '.$table;
		$count=$this->getRow($SQL);
		return $count[0];
	}		

	public function q($string, $comma = true) {
		$string=mysql_real_escape_string($string);
		return $comma ? '"'.$string.'",' : '"'.$string.'"';
	}
	
	public function lastInsertId() {
		return mysql_insert_id();
	}

	//	
	//common database functions
	//not nessecarily related to Db, but implemented here so we always have access to them in child classes
	//
	public function getFields($table) {
		$SQL='show columns from '.$table;
		$result=$this->query($SQL);
		$return = array();
		while ($row = mysql_fetch_assoc($result)) {
			$return[]=$row;
		}
		return $return;		
	}

	public function removeLastChar($s) {
		return substr_replace($s ,"", -1);
	}

	public function getLanguages() {
		$SQL='select lang_id, name from zn_languages order by lang_id';
		$result=$this->query($SQL);
		$a=array();
		while ($row=mysql_fetch_array($result)) {
			$a[]=$row;
		}
		return $a;
	}

	//url, current page info
	public function currentDomain() {
		return $_SERVER['HTTP_HOST'];
	}

	public function getIndexPage() {
		$host=$_SERVER["SERVER_ADDR"]; 
		if (($host=='127.0.0.1') || ($host=='::1')) {
			return 'http://localhost/samlinger';
		} else {
			return 'http://daim.snm.ku.dk';
		}
	}

	public function currentURL() {
		$domain = $this->currentDomain(); 
		$url = "http://" . $domain . $_SERVER['REQUEST_URI'];
		//change & to &amp;
		$url=str_replace('&','&amp;',$url);
		return $url;
	}

	//$semanticOnly = cut all after ?
	public function currentSemanticName($semanticOnly = false) {
		$url=$this->currentURL();
		$file=explode("/", $url);
		$file=$file[sizeof($file)-1];
		if ($semanticOnly) {
			$file=explode('?', $file)[0];
		}
		return $file;
	}

	public function currentPageId() {
		$semantic_name = $this->currentSemanticName();
		$SQL= 'select p.page_id from zn_page p, zn_page_content c '.
			'where c.semantic_name="'.$semantic_name.'" and p.page_id=c.page_id';
		$row = $this->getRow($SQL);
		return isset($row[0]) ? $row[0] : false;
	}
		
	//params, the GET-array is
	//[0] => param1
	//[1] => value1
	//[2] => param2
	//[3] => value2
	//...
	protected function hasParam($param) {
		return in_array($param, $_SESSION[GET]);
	}

	protected function getParam($param, $default=false) {
		if ($this->hasParam($param)) {
			$index=array_search($param, $_SESSION[GET]);
			return $_SESSION[GET][$index+1];
		} else {
			return $default;
		}
	}

	protected function getParams() {
		$params='';
		for ($i=0;$i<count($_SESSION[GET]);$i++) {
			if ($i%2==0) { 
				if ($params=='') {
					$params.='?';
				} else {
					$params.='&';
				}
				$params.=$_SESSION[GET][$i];
				$params.='='.$_SESSION[GET][$i+1];
			}
		}
		return $params;
	}

	//get page type according to id or semantic name
	//reduced version of PageLoader->getPageType
	public function getPageType($semantic, $id) {
		$where = ($id!='') ? 'z.page_id='.$id : 'z.semantic_name="'.$semantic.'"';
		$where = ' where '.$where;
		$where.= ' and (c.page_id=z.page_id) limit 1';

		$SQL='select c.page_id from zn_page_class c, zn_page_content z '.$where;
		if ($this->hasData($SQL)) return PAGE_CLASS;

		$SQL='select c.page_id from zn_page_static c, zn_page_content z '.$where;
		if ($this->hasData($SQL)) return PAGE_STATIC;

		$SQL='select page_id from zn_page_link where page_id='.$id.' limit 1';
		if ($this->hasData($SQL)) return PAGE_LINK;

		return PAGE_UNDEFINED;
	}

	//debug
	public function debug($data) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	protected function fileDebug($text) {
		$file = "debug.txt";
		$fh = fopen($file, 'a') or die("");
		fwrite($fh, $text."\n");
		fclose($fh);
	}


}

?>
