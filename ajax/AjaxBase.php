<?

//include('../common/Core.php');
include('../common/Db.php');

if (isset($_GET['lang'])) {
	switch($_GET ['lang']) {
		case 2 : include('../lang/english.php'); break;
		default : include('../lang/dansk.php'); break;
	}
}

//translation, dup from Template.php
function trans($text, $print=false) {
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


class AjaxBase extends Db {

	protected function styleInputScript($id='') {
		if ($id!='') $id.=' ';
?>
<script type="text/javascript">
$(document).ready(function() {
	$.each($("<? echo $id;?>select"), function () {
		$(this).selectmenu({ style:"dropdown", 
			     maxHeight:"400", 
			     menuWidth: "300", 
			     width : '230'
			   });
	});
	$("<? echo $id;?>input:button").button();
	$("<? echo $id;?>input:submit").button();
});
</script>
<?
	}
}

?>
