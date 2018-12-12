<?
/**
 * Db.php				PDO wrapper for ESPBA
 * @copyright   Copyright (C) 2017 david konrad, davidkonrad at gmail com
 * @license     Licensed under the MIT License; see LICENSE.md
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

/**
 * Fill out your credentuils
 **/	 
include('pw.php');

/**
 * If you want to use an alternative "driver" for ESPBA, extend this abstract class and fill out the blanks
 **/	 
abstract class DbProvider {
	protected $database;
	protected $hostname;
	protected $username;
	protected $password;
	protected $charset = 'utf8';
	abstract protected function isLocalhost();		//localhost or production
	abstract protected function query($SQL);			//perform a query on a fully qualified SQL statement and return the result
	abstract protected function exec($SQL);				//perform a query on a fully qualified SQL statement and do not return the result
	abstract protected function s($s);						//escape a string
	abstract protected function error();					//return errorinfo
	abstract protected function affected();				//return affected rows
	abstract protected function lastInsertId();		//return last insert Id
	abstract protected function queryJSON($SQL);	//return the result of a query() as JSON
}

/**
 * default Db provider using PDO
 **/ 
class Db extends DbProvider {
	private $pdo;
  
	public function __construct() { 
		global $pw_local, $pw_server;

		$this->charset = 'utf8'; //'latin1';

		if ($this->isLocalhost()) {
			$this->database = $pw_local['database']; 
			$this->hostname = $pw_local['hostname'];
			$this->username = $pw_local['username'];
			$this->password = $pw_local['password'];
		} else {
			$this->database = $pw_server['database']; 
			$this->hostname = $pw_server['hostname'];
			$this->username = $pw_server['username'];
			$this->password = $pw_server['password'];
		}

		$dsn = "mysql:host=".$this->hostname.";dbname=".$this->database.";charset=".$this->charset;
		$opt = [
	    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	    PDO::ATTR_EMULATE_PREPARES   => false
		];

		try {
			$this->pdo = new PDO($dsn, $this->username, $this->password, $opt);
		} catch(PDOException $e) {
			echo "Error connecting to database: ". $e->getMessage();
		}
	}

	protected function isLocalhost() {
		$host = $_SERVER["SERVER_ADDR"]; 
		if (($host=='127.0.0.1') || ($host=='::1')) {
			return true;
		} else {
			return false;
		}
	}
			
	protected function query($SQL) {
		try {
			$result = $this->pdo->query($SQL);
			return $result;
		} catch(Exception $e) {
			return array('error' => $e->getMessage());
		}
	}

	protected function exec($SQL) {
		try {
			$this->pdo->query($SQL);
		} catch(Exception $e) {
			return array('error' => $e->getMessage());
		}
	}

/**
	* Any string value is quoted, and inside quotes is escaped. 
	* Along with ATTR_EMULATE_PREPARES == false will this prevent SQL injection
	* ;drop table user;-- as an evil attempt will insert ';drop table user;--' as field value
	* Please report any mistakes with this approach
	*/
	protected function s($s) {
		return $this->pdo->quote($s);
	}

	protected function error() {
		$err = $this->pdo->errorInfo();
		$err = ($err && is_array($err) && $err[0] != '00000') ? implode(';', $err) : false;
		return $err;
	}


/**
	* Does not work with MySQL
	*/
	protected function affected() {
		//return $this->pdo->rowCount();
		return 1;
	}

	protected function lastInsertId() {
		return $this->pdo->lastInsertId();
	}

	protected function queryJSON($SQL) {
		$result = $this->query($SQL);

		if (!$result instanceof PDOStatement) {
			return json_encode($result);
		}

		$return = array();
		while ($row = $result->fetch()) {
			$return[] = $row;
		}

		return json_encode($return);
	}

/** 
	* old edderkopper Db* methods (modified)
	*/
	public function getRecCount($table) {
		$SQL='select count(*) as c from '.$table;
		$count=$this->getRow($SQL);
		return $count['c'];
	}		

	public function getRow($SQL, $assoc = false) {
		try {
			$result = $this->query($SQL);
			if (!$assoc) {
				$result = $result->fetch();
			} else {
				$result = $result->fetch(PDO::FETCH_ASSOC);
			}
			return $result;
		} catch (Exception $e) {
		    echo 'Database error : ',  $e->getMessage(), "\n".$SQL;
		}
	}

	public function hasData($SQL) {
		$result = $this->query($SQL);
		return $result->rowCount() > 0;
	}

	//url, current page info
	public function currentDomain() {
		return $_SERVER['HTTP_HOST'];
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
		return isset($row['page_id']) ? $row['page_id'] : false;
	}

	public function getIndexPage() {
		$host=$_SERVER["SERVER_ADDR"]; 
		if (($host=='127.0.0.1') || ($host=='::1')) {
			//return 'http://localhost/samlinger';
			return 'http://localhost/html/edderkopper/';
		} else {
			//return 'http://daim.snm.ku.dk';
			return 'http://danmarks-edderkopper.dk';
		}
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

	//change charset
	public function setLatin1() {
		$this->pdo->exec('SET NAMES Latin1');
  }
	public function setUtf8() {
		$this->pdo->exec('SET NAMES utf8');
  }

}

?>
