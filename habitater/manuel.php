<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<style>
input.small {
	width: 50px;
}
input.large {
	width: 400px;
}
</style>
</head>
<body>
<?

include('../common/Db.php');

class Manuel extends Db {

	public function __construct() {
		parent::__construct();
		if (count($_GET)>0) $this->saveRecord();
		$this->drawForm();
	}

	protected function saveRecord() {
		$natura = $_GET['natura'];
		$navn = $_GET['navn'];
		$hnr = $_GET['hnr'];
		$year1 = $_GET['year1'];
		$fnr = $_GET['fnr'];
		$year2 = $_GET['year2'];
		$rnr = $_GET['rnr'];
		$areal = $_GET['areal'];

		$SQL='insert into Natura2000 set '.
			'natura2000='.$this->q($natura).
			'navn='.$this->q($navn).
			'hnr='.$this->q($hnr).
			'aar_for_aendringer_1='.$this->q($year1).
			'fnr='.$this->q($fnr).
			'aar_for_aendringer_2='.$this->q($year2).
			'rnr='.$this->q($rnr).
			'areal_ha='.$this->q($areal, false);

		echo $SQL;
		$this->exec($SQL);
		//header('location:manuel.php');
	}

	protected function drawForm() {
?>
<form action="manuel.php" method="get">
<table>
<tr>
	<td>Natura 2000 nr</td>
	<td><input type="text" name="natura" class="small"></td>
</tr>
<tr>
	<td>navn</td>
	<td><input type="text" name="navn" class="large"></td>
</tr>
<tr>
	<td>H-nr</td>
	<td><input type="text" name="hnr" class="large"></td>
</tr>
<tr>
	<td>År for ændringer 1</td>
	<td><input type="text" name="year1" class="large"></td>
</tr>
<tr>
	<td>F-nr</td>
	<td><input type="text" name="fnr" class="large"></td>
</tr>
<tr>
	<td>År for ændringer 2</td>
	<td><input type="text" name="year2" class="large"></td>
</tr>
<tr>
	<td>R-nr</td>
	<td><input type="text" name="rnr" class="large"></td>
</tr>
<tr>
	<td>Areal</td>
	<td><input type="text" name="areal" class="large"></td>
</tr>

</table>

<input type="submit" value="Gem">

</form>

<?
	}

}

$man = new Manuel();

?>
