<?
ini_set('display_errors', '1');

class EditEdderkopperFund extends ClassBase {
	public $template = 'TemplateEdderkopper';
	private $LNR;
	private $headline = 'Rediger fund';
	private $row;

	public function __construct() {
		parent::__construct();
		$this->LNR=($this->hasParam('lnr')) ? $this->getParam('lnr') : false;
		
		if (!$this->LNR) {
			$this->createNewFund();
			$this->LNR=$this->lastInsertId();
			header('Location:edderkopper-rediger-fund?lnr='.$this->LNR);
			//$this->headline='Rediger nyt fund';
		} 

		mysql_set_charset('utf8');
		$SQL='select * from edderkopper where LNR="'.$this->LNR.'"';
		$this->row = $this->getRow($SQL);

		/*
			name bør opdateres automatisk 
			AuthorYear bør opdateres automatisk
		*/
	}

	private function createNewFund() {
		mysql_set_charset('utf8');
		$SQL='insert into edderkopper (Family, Genus, Species, AuthorYear, Leg, Locality, Date_last, Month_last, Year_last) values('.
			$this->q('<not set>').
			$this->q('<not set>').
			$this->q('<not set>').
			$this->q('<not set>').
			$this->q('<not set>').
			$this->q('<not set>').
			$this->q('0').
			$this->q('0').
			$this->q('0', false).
		')';
		$this->exec($SQL);
	}

	public function extraHead() {
?>
<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
<link rel="stylesheet" href="css/bootstrap-typeahead.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper-adm.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/edderkopper.js"></script>
<script type="text/javascript" src="js/edderkopper_adm.js"></script>
<style>
.changed {
	background-color: green;
	color: white;
}
form label {
	width: auto;
	clear: none;
	margin-left: 5px;
	padding-right: 3px;
	font-size: 12px;
	padding-top: 2px;
}
</style>
<?
	}

	public function drawBody() {
		echo '<script> var lnr='.$this->LNR.';</script>';
		echo '<h2 class"inline">'.$this->headline.' #'.$this->LNR.'</h2>';
		HTML::hr('search');
		echo '<button id="save" style="font-size:16px;">Gem &aelig;ndringer</button>';
		echo '<button id="delete" style="font-size:16px;float:right;">Slet fund</button>';
		echo '<span id="save-msg" style="margin-left:10px;"></span>';
		HTML::hr('search');
?>
<form id="editform" method="get">
    <table>
        <tbody>
            <tr>
                <td><b>LNR</b>
                </td>
                <td>
                    <input readonly="readonly" type="text" name="LNR" id="LNR" value="<? echo $this->row['LNR'];?>" style="width:50px;">
                </td>
            </tr>
            <tr>
                <td><b>Family</b>
                </td>
                <td>
                    <input type="text" name="Family" id="Family" value="<? echo $this->row['Family'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>Genus</b>
                </td>
                <td>
                    <input type="text" name="Genus" id="Genus" value="<? echo $this->row['Genus'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>Species</b>
                </td>
                <td>
                    <input type="text" name="Species" id="Species" value="<? echo $this->row['Species'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>AuthorYear</b>
                </td>
                <td>
                    <input type="text" name="AuthorYear" id="AuthorYear" value="<? echo $this->row['AuthorYear'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>Date d/m/y</b>
                </td>
                <td>
                    <input type="text" name="Date_first" id="Date_first" class="number-only" value="<? echo $this->row['Date_first'];?>" style="width:30px;">
					<label>/</label>
                    <input type="text" name="Month_first" id="Month_first" class="number-only"  value="<? echo $this->row['Month_first'];?>" style="width:30px;">
					<label>/</label>
                    <input type="text" name="Year_first" id="Year_first" class="number-only" value="<? echo $this->row['Year_first'];?>" style="width:40px;">
					<label>&nbsp;<b>to</b>&nbsp;</label>
                    <input type="text" name="Date_last" id="Date_last" class="number-only" value="<? echo $this->row['Date_last'];?>" style="width:30px;">
					<label>/</label>
                    <input type="text" name="Month_last" id="Month_last" class="number-only"  value="<? echo $this->row['Month_last'];?>" style="width:30px;">
					<label>/</label>
                    <input type="text" name="Year_last" id="Year_last" class="number-only" value="<? echo $this->row['Year_last'];?>" style="width:40px;">

                </td>
            </tr>

            <tr>
                <td><b>No. collected</b>
                </td>
                <td>
					<label>Males</label>
                    <input type="text" name="MaleCount" id="MaleCount"  class="number-only" value="<? echo $this->row['MaleCount'];?>" style="width:40px;">
					<label>Females</label>
                    <input type="text" name="FemaleCount" id="FemaleCount"  class="number-only" value="<? echo $this->row['FemaleCount'];?>" style="width:40px;">
					<label>Juveniles</label>
                    <input type="text" name="JuvenileCount" id="JuvenileCount"  class="number-only" value="<? echo $this->row['JuvenileCount'];?>" style="width:40px;">
                </td>
            </tr>

            <tr>
                <td><b>Leg</b>
                </td>
                <td>
                    <input type="text" name="Leg" id="Leg" value="<? echo $this->row['Leg'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>Det</b>
                </td>
                <td>
                    <input type="text" name="Det" id="Det" value="<? echo $this->row['Det'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>Collection</b>
                </td>
                <td>
                    <input type="text" name="Collection" id="Collection" value="<? echo $this->row['Collection'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>KatalogNrPers</b>
                </td>
                <td>
                    <input type="text" name="KatalogNrPers" id="KatalogNrPers" value="<? echo $this->row['KatalogNrPers'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>Locality</b>
                </td>
                <td>
                    <input type="text" name="Locality" id="Locality" value="<? echo $this->row['Locality'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>Lat / Long</b>
                </td>
                <td>
                    <input type="text" name="LatPrec" id="LatPrec" value="<? echo $this->row['LatPrec'];?>" style="width:70px;">
					<label>&nbsp;/&nbsp;</label>
                    <input type="text" name="LongPrec" id="LongPrec" value="<? echo $this->row['LongPrec'];?>" style="width:70px;">
                </td>
            </tr>

            <tr>
                <td><b>RadiusNew</b>
                </td>
                <td>
                    <input type="text" name="RadiusNew" id="RadiusNew" value="<? echo $this->row['RadiusNew'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>Region</b>
                </td>
                <td>
                    <input type="text" name="Region" id="Region" value="<? echo $this->row['Region'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>NotesF</b>
                </td>
                <td>
                    <input type="text" name="NotesF" id="NotesF" value="<? echo $this->row['NotesF'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>NotesonLoc</b>
                </td>
                <td>
                    <input type="text" name="NotesonLoc" id="NotesonLoc" value="<? echo $this->row['NotesonLoc'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>NotesonSpecimen</b>
                </td>
                <td>
                    <input type="text" name="NotesonSpecimen" id="NotesonSpecimen" value="<? echo $this->row['NotesonSpecimen'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>NotesonTube</b>
                </td>
                <td>
                    <input type="text" name="NotesonTube" id="NotesonTube" value="<? echo $this->row['NotesonTube'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>SpecimenBased</b>
                </td>
                <td>
                    <input type="text" name="SpecimenBased" id="SpecimenBased" value="<? echo $this->row['SpecimenBased'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>Specimens</b>
                </td>
                <td>
                    <input type="text" name="Specimens" id="Specimens" value="<? echo $this->row['Specimens'];?>" style="width:400px;">
                </td>
            </tr>
            <tr>
                <td><b>UTM10</b>
                </td>
                <td>
                    <input type="text" name="UTM10" id="UTM10" value="<? echo $this->row['UTM10'];?>" style="width:400px;">
                </td>
            </tr>

            <tr>
                <td><b>PrecMonth</b>
                </td>
                <td>
                    <input type="text" name="PrecMonth" id="PrecMonth" value="<? echo $this->row['PrecMonth'];?>" style="width:400px;">
                </td>
            </tr>

        </tbody>
    </table>
    <br>
	<hr class="search">
	<br>
</form>
<?
	}
	
	public function drawBeforeFooter() {
?>
<script>
function setChanged($element) {
	if (!$element.hasClass('changed')) $element.addClass('changed');
}
$("#delete").on('click', function() {
	adm.fundDelete(lnr);
});
$("#save").attr('disabled', 'disabled');
$("#save").on('click', function() {
	adm.fundSave('#editform', '#save-msg');
	$(this).attr('disabled', 'disabled');
	$("#editform input").removeClass('changed');
})
$("#editform input").on('keyup', function(e) {
	if (e.which>=32) {
		setChanged($(this));	
		$("#save").removeAttr('disabled');
	}
});
var currentSpecies = '';
$("#Species").on('click', function() {
	currentSpecies = $(this).val();
	$(this).val('');
});
$("#Species").on('blur', function() {
	if ($(this).val()=='') $(this).val(currentSpecies);
});

$.ajax({
	url : 'ajax/edderkopper/actions.php?action=taxonomy',
	success : function(json) {
		var species = [];
		for (specie in json) {
			species.push(json[specie].Species+', '+json[specie].Genus);
		}
		//console.log(species);//json);
		$("#Species").typeahead({
			source: species,
			//match only species (beginning of string)
			matcher: function(item) {
				if (item.toLowerCase().indexOf(this.query.toLowerCase()) == 0) {
					return ~item.toLowerCase().indexOf(this.query.toLowerCase());
				} 
			},
			//prevent highlightning of genus
			highlighter: function (item) {
				var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
				return item.replace(new RegExp('(' + query + ')', 'i'), function ($1, match) { 
			        return '<strong>' + match + '</strong>'
				})
			},
			//set correct species, genus amd family
			updater: function(item) {
				//$("#edit-specie").disable(false);
				var tax = item.split(', ');
				for (specie in json) {
					if (json[specie].Species==tax[0] &&	json[specie].Genus==tax[1]) {
						$("#Genus").val(tax[1]);
						setChanged($("#Genus"));
						$("#Family").val(json[specie].Family);
						setChanged($("#Family"));
						return tax[0];
					}
				}
		    }			
        });
    }
});    
</script>
<?
	}

}


?>
