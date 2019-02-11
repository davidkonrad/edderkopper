<?
session_start();

//debug
ini_set('display_errors', '1'); //
error_reporting(1);

include_once('common/Db.php');

//language db connection
$dbl = (isset($dbl)) ? $dbl : new Db();
$debug = true;
function trans($text, $print=false) {
	global $dbl, $debug;
	if (!$print) {
		if (defined($text)) {
			return constant($text);
		} else return $text;
	} else {
		if (defined($text)) {
			echo constant($text);
		} else echo $text;
	}
}

include('common/proxies.php');
include('common/Login.php');
include('common/Lang.php');
include('common/HTML.php');
include('common/PageLoader.php');
include('classes/404.php');


//include('classes/StaticPage.php');
//include('classes/CategoryPage.php');

//frontpage
//include('classes/Sitemap.php');

function __autoload($class) {
	//class directories
	$class_dir = array(
		'classes/', 
		'classes/edderkopper/',
		'classes/template/',
		'classes/tools/'
	);

	if (!class_exists($class, false)) {
		foreach ($class_dir as $dir) {
			$filename=$dir.$class.'.php';
			if (file_exists($filename)) {
				include($filename);
				return;
			}
		} 
	}
}

//consts
define('EDIT_PAGE','editpage');
define('EDIT_CATEGORY','admin-categories');
define('SHOW_CATEGORY','category');
define('CURRENT_PAGE_ID','current_page_id');
define('STANDALONE','standalone');
define('LANGUAGE','LANG');

//array of meta-params, eg semantic_name?param1=xx&param2=yy =>[param2]=xx,=>[param2]=yy
define('GET','_GET'); 

//flags
define('PAGE_UNDEFINED', -1);
define('PAGE_CLASS', 1);
define('PAGE_STATIC', 2);
define('PAGE_LINK', 3);
define('PAGE_CATEGORY', 4);

//for any reason, set flag that instructs PageLoader to ignore automatics
//for now, only when user change language

$LOADER_IGNORE = false;
if (isset($_GET['lang'])) {
	$_SESSION[LANGUAGE]=$_GET['lang'];
	$LOADER_IGNORE=true;
}

//set default language
if (!isset($_SESSION[LANGUAGE])) {
	$_SESSION[LANGUAGE]=1;
}

//load content	
$pageloader = new PageLoader($LOADER_IGNORE);

?>
