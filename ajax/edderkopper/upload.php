<?
	ini_set('display_errors', '1');

	$destination_path = '../../edderkopper-upload/';
	$return_to = $_POST['return-to'];

	$result = 'error';

	$target_path = $destination_path . basename( $_FILES['file']['name']);
 
   	if (@move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
		$result='y';//$_FILES['file']['name'].' er blevet uploadet!';
		chmod($target_path, 0777);
		/*
		if (chmod($target_path, 0777)) {
			$result.='<br>Permission->0777 lykkedes!';
		} else {
			$result.='<br>Reset af permission mislykkedes ...';
		}
		/*
		$zip = new ZipArchive();
		$x = $zip->open($target_path);
		if ($x === true) {
			$zip->extractTo($destination_path);
			$zip->close();
			$result.='<br>Udpakning af zip lykkedes!';
		} else {
			$result.='<br>Zip-udpakning mislykkedes ...';
		}
		*/
	} else {
		$result='n'; //Upload af '.$_FILES['file']['name'].' mislykkedes ...';
	}

	sleep(1);
	header('location: '.$return_to.'?f='.$_FILES['file']['name'].'&m='.$result);
?>
