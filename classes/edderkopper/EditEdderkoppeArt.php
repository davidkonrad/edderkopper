<?

//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');


class EditEdderkoppeArt extends EditBase {
	private $species;

	private $speciesName = '';
	private $genusName = '';
	
	private $species_data;
	private $genus_data;
	private $family_data;

	private $speciesID;
	private $genus_id;
	private $family_id;

	public $template = 'TemplateEdderkopper';

	public function __construct() {
		parent::__construct();
		$this->speciesID=($this->hasParam('speciesID')) ? $this->getParam('speciesID') : false;
		$this->getData();
	}

	public function extraHead() {
?>
<link rel="stylesheet" href="plugins/tabber/zmuc.css" type="text/css" media="screen" />
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<style type="text/css">
.save {
	float: right;
	font-size: 20px;
	padding: 3px;
	margin-right: 20px;
}
td.caption {
	font-weight: bold;
}
select {
	background-color: white;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$('.editor').each(function() {
		var name=$(this).attr('id');
		CKEDITOR.replace(name, { width:"750px", height:"250px", toolbar:'edderkopper' }); //, toolbar:'Basic'
	});
});
function editorData(name) {
	for(var i in CKEDITOR.instances) {
		CKEDITOR.instances[i].updateElement();
	}
}
function updateEditors() {
	for(var i in CKEDITOR.instances) {
		CKEDITOR.instances[i].updateElement();
	}
}
function save(pre, table, where) {
	updateEditors();
	var params='table='+table;
	var field, value;
	params+='&where='+where;
	$('[name^="'+pre+'"]').each(function() {
		field=$(this).attr('name');
		value=$(this).val();
		value=encodeURIComponent(value);
		field=field.replace(pre+'_','');
		params+='&'+field+'='+value;
	});

	var url='ajax/edderkopper_update_table.php';
	$.ajax({
		type: 'POST', 
		data: params,
		url: url,
		cache: false,
		async: true,
		timeout : 5000,
		success: function(html) {
			var text= (html=='') ? 'Ændringer gemt' : html;
			$("#message").show();
			$("#message").html(text);
			setTimeout(function() {
				$("#message").fadeOut('slow');
			}, 3000);

			//update fund/edderkopper genus og species names
			var params='&oldspecies='+encodeURIComponent(speciesName)+
					'&oldgenus='+encodeURIComponent(oldGenusName)+
					'&newspecies='+encodeURIComponent($("#species_Species").val())+
					'&newgenus='+encodeURIComponent($("#species_GenusID option:selected").text());

			$.ajax({
				url : 'ajax/edderkopper/actions.php?action=fundupdatespeciesname'+params,
				success : function(msg) {
					//update fund/edderkopper Name
					$.ajax({
						url : 'ajax/edderkopper/actions.php?action=fundupdatenameall',
						success : function(msg) {
							//alert(msg+'ok');
						}
					});
				}
			});
			
		}
	});
}
//add legend change options to species and genus
$(document).ready(function() {
	$("#species_Species").on('keyup', function() {
		speciesName = $("#species_Species").val();
		$("#legend").text(genusName+' '+speciesName);
	});
	$("#species_GenusID").on('change', function() {
		var genusID = $("#species_GenusID").val();
		genusName = $("#species_GenusID option[value='"+genusID+"']").text();
		$("#legend").text(genusName+' '+speciesName);
	});
});
</script>
<?
	}

	private function genusSelect($id, $genusID) {
		$SQL='select GenusID, Genus from edderkopper_genus order by Genus ';
		$result=$this->query($SQL);
		$options='';
		while ($row = mysql_fetch_array($result)) {
			$selected=($row['GenusID']==$genusID) ? ' selected="selected"' : '';
			$options.='<option value="'.$row['GenusID'].'"'.$selected.'>'.$row['Genus'].'</option>';
		}
		return '<select name="'.$id.'" id="'.$id.'" style="width:300px;">'.$options.'</select>';
	}

	private function getData() {
		$SQL='select * from edderkopper_species where speciesID='.$this->speciesID;
		mysql_set_charset('utf8');
		$result=$this->query($SQL); 
		$this->species_data=mysql_fetch_assoc($result);

		$this->speciesName = $this->species_data['Species'];

		//genusID can be NULL if newly inserted, check for this
		if ($this->species_data['GenusID']!='') {
			$SQL='select Genus from edderkopper_genus where GenusID='.$this->species_data['GenusID'];
			$row = $this->getRow($SQL);
			$this->genusName = $row['Genus'];
		} else {
			$this->genusName = '';
		}
			
	}

	private function getLength($type)  {
		//from http://stackoverflow.com/questions/6278296/extract-numbers-from-a-string
		preg_match_all('!\d+!', $type, $matches);
		if (isset($matches[0][0])) {
			return $matches[0][0];
		}
		return false;
	}	

	private function getType($type) {
		$pos=strpos($type,'(');
		if ($pos===false) return $type;
		return substr($type,0,$pos);
	}

	private function processTable($table, $record, $pre, $where) {
		$params='&quot;'.$pre.'&quot,&quot;'.$table.'&quot;,&quot;'.$where.'&quot;';
		/*
		echo '<button xclass="save" onclick="save('.$params.');">Gem ændringer</button>';
		HTML::hr('search');
		echo '<div id="message" style="float:right;margin-right:20px;padding-top:10px;font-size:20px;font-family:courier;"></div>';
		*/
		echo '<button id="save" style="font-size:16px;" onclick="save('.$params.');">Gem &aelig;ndringer</button>';
		echo '<span id="message" style="margin-left:10px;"></span>';
		HTML::hr('search');


		$info = $this->getFields($table);
		echo '<table>';
		foreach ($info as $field) {
			$name=$field['Field'];
			$id=$pre.'_'.$name;
			echo '<tr>';
			echo '<td valign="top" class="caption">';
			echo '<label for="'.$id.'">'.$field['Field'].'</label>';
			echo '</td>';
			echo '<td>';
			$type=$this->getType($field['Type']);;
			$length=$this->getLength($field['Type']);
			if ($length>95) $length=95;

			switch ($type) {

				case 'text' : 
						echo '<textarea id="'.$id.'" name="'.$id.'" class="editor">'.$record[$name].'</textarea>';
						break;

				case 'varchar' : 
						echo '<input type="text" id="'.$id.'" size="'.$length.'" name="'.$id.'" value="'.$record[$name].'">';
 						break;

				case 'int' : 
						$readonly=(in_array($name, array('SpeciesID', 'GenusID', 'FamilyID'))) ? ' readonly="readonly" style="background-color:#ebebeb;"' : '';
						if ($name=='GenusID' && $table!='edderkopper_genus') {
							echo $this->genusSelect($id, $record[$name]);
						} else if ($name=='FamilyID' && $table!='edderkopper_family') {
							echo $this->familySelect($id, $record[$name]);
						} else
 {							echo '<input type="text" id="'.$id.'" size="5" name="'.$id.'" value="'.$record[$name].'"'.$readonly.'>';
						}
						break;						

				default : echo '<input type="text" id="'.$id.'" name="'.$id.'" value="'.$record[$name].'">';
						break;
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	public function drawBody() {
		echo '<h2 class"inline" id="legend">'.$this->genusName.' '.$this->speciesName.'</h2>';
		HTML::hr('search');
		$this->processTable('edderkopper_species', $this->species_data, 'species', 'SpeciesID='.$this->speciesID);
?>
<!--
<fieldset>
<legend id="legend"><? echo $this->genusName.' '.$this->speciesName;?></legend>
<? $this->processTable('edderkopper_species', $this->species_data, 'species', 'SpeciesID='.$this->speciesID); ?>
</fieldset>
-->

<script>
var speciesName = "<? echo $this->speciesName;?>";
var genusName = "<? echo $this->genusName;?>";
var oldGenusName = "<? echo $this->genusName;?>";
</script>

<?

	}


}

?>
