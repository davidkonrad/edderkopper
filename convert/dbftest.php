<?

$db = dbase_open('/exportsWEBFUNGI.DBF', 0);

$rec = dbase_get_record($db, $recno);
$nf  = dbase_numfields($db);
for ($i = 0; $i < $nf; $i++) {
  echo $rec[$i], "\n";
}

?>
