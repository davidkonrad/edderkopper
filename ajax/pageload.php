<?
$server='http://localhost/glsvampe/'.$_GET['s'];
$page=file_get_contents($server);
echo $page;
?>
