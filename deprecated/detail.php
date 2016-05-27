<?
include('common/Core.php');
include_once('common/Db.php');
include('common/HTML.php');
include('classes/Template.php');

//expects page=class, eg PageSvampeDetail
if (isset($_GET['page'])) {
	$page = new $_GET['page'];
	$page->draw();
}

?>

