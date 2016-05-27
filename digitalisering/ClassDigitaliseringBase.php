<?

class ClassDigitaliseringBase extends ClassBase {

	protected function getProjects($id="project") {
		$select='<select id="'.$id.'" name="'.$id.'">';
		$SQL='select * from webcam_projects';
		mysql_set_charset('utf8');
		$result=$this->query($SQL);
		while ($row = mysql_fetch_array($result)) {
			$select.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
		}
		$select.='</select>';
		return $select;
	}

}

?>
