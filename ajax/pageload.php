<?
$server='http://localhost/glsvampe/'.$_GET['s'];
//echo 'sdfsdfsdfsdf';
//echo $_GET['id'];
//echo 'sdfsdfsdfsdfsdfsdfsdfsdf';
$page=file_get_contents($server);
echo $page;
?>
