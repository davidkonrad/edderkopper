<?

class ClassEdderkopperChecklist extends ClassBase {
	public $template = 'TemplateEdderkopper';

	public function drawBody() {
		parent::drawBody();
?>
<fieldset id="search-fieldset">
<legend id="checklist-headline"><? echo $this->info['anchor_caption'];?></legend>
<input type="button" value="<? trans(LAB_CHECKLIST_PDF, true);?>" id="btn-save-pdf" style="margin-left:10px;float:left;clear:right;"/>
<br><br><br>
<? 
HTML::divider(13);
$this->drawSessLang();?>
<div id="checklist-result" style="float:left;text-align:left;"></div>
<script>
//$("#checklist-result").load('edderkopper-upload/checklist/checklist.html');
$("#checklist-result").load('ajax/edderkopper_checklist.php?action=get');
</script>
</fieldset>
<?
	}

	public function extraHead() {
?>
<!--
<style type="text/css">
tr.odd {
	background-color: white;
}
tr.even {
	background-color: white;
}
</style>
-->
<?
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
/*
$("#btn-checklist").click(function() {
	Search.wait(true);
	$.ajax({
		url : 'ajax/edderkopper_checklist.php',
		success : function(html) {
			$("#checklist-result").html(html);
			$("#btn-pdf").show();
			$("#btn-pdf").button();
			Search.wait(false);
		}
	});
});
*/
$("#btn-save-pdf").click(function() {
	//var url='ajax/edderkopper_checklist.php?pdf=yes'
	System.wait(true);
	var url='ajax/edderkopper_checklist.php?action=pdf'
	window.location=url;
	System.wait(false);
/*
	//window.open(url,'Edderkopper checkliste');
	Search.wait(true);
	$.ajax({
		url : 'ajax/edderkopper_checklist.php?pdf=yes',
		success : function(url) {
			//$("#checklist-result").html(html);
			//var url='ajax/edderkopper_checklist.php?pdf=yes'
			window.location=url;
			Search.wait(false);
		}
	});
*/
});
/*
$("#btn-pdf").click(function() {
	Search.wait(true);
	resultTable=$("#table-checkliste").dataTable({
		bJQueryUI: true,
		bSortClasses: false,
		bPaginate: false,
		aaSorting: [],
		sStripeEven : 'snm-none',
		sStripeOdd : 'snm-none',
		//sDom: 'TC<"clear">R<"H"lpfr>t<"F"i>',
		sDom: 'T<"clear">lfrtip',
		oTableTools: {
			sSwfPath: "DataTables-1.9.1/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
			aButtons: [
				{
				  "sExtends": "pdf",
				  'mColumns':'visible',
				  'sButtonText' : 'Gem som PDF-fil',
				  'sToolTip': 'Gem søgeresultater som PDF fil',
				  //"sPdfOrientation": "landscape",
				  "fnClick": function( nButton, oConfig, flash ) {
						flash.setFileName('checkliste.pdf');
						this.fnSetText( flash,
							"title:"+ this.fnGetTitle(oConfig) +"\n"+
							"message:"+ oConfig.sPdfMessage +"\n"+
							"colWidth:"+ this.fnCalcColRatios(oConfig) +"\n"+
							"orientation:"+ oConfig.sPdfOrientation +"\n"+
							"size:"+ oConfig.sPdfSize +"\n"+
							"--/TableToolsOpts--\n" +
							this.fnGetTableData(oConfig)
						);
					    }  
				}				
			]
		}
	});
	$(".dataTables_filter").hide();
	$("#table-checkliste_info").hide();
	$("#ToolTables_table-checkliste_0").button();
	Search.wait(false);
});
*/

$(document).ready(function() {
	//we doesnt need a real form, just an unique id 
/*
	var searchItem = new SearchItem('#search-fieldset');

	searchItem.headline_id="#checklist-headline";
	searchItem.caption="<? echo $this->info['anchor_caption'];?>";
	searchItem.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';

	Search.addItem(searchItem);
	Search.init(searchItem);
*/
});
</script>
<?
	}

}
?>
