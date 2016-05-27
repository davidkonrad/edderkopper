<?

class ToolDigitTyperImgNoType extends ClassBase {

	public function drawBody() {
		HTML::h2('<code>Typer</code> - billeder med reference til ikke-eksisterende typer');
		HTML::hr('search');
		echo '<button id="generate">Generér liste</button>';
		echo '<div id="result"></div>';
	}

	public function drawBeforeFooter() {
?>
<style type="text/css">
.DTTT_button {
	width: 60px;
}
</style>
<script type="text/javascript">
function initialize() {
	$("#result-table").dataTable({
		bJQueryUI: true,
        bPaginate: false,  
        bInfo: false,  
		bLengthChange: false,
        bFilter: false,
        bAutoWidth: false,
		asStripClasses:[],
		bSortClasses: false,
		sDom: 'T<"clear">lfrtip',
		oTableTools: {
    	    sSwfPath: "DataTables-1.9.1/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
		}
	});
	/*
	$(".dataTables_filter").hide();
	$(".fg-toolbar").hide();
	$(".DTTT_container").hide();
	*/
}
$(document).ready(function() {
	$("#generate").click(function() {
		System.wait(true);
		$.ajax({
			url: 'ajax/digit_typer_bad_images.php',
			success : function(html) {
				$("#result").html(html);
				System.wait(false);
				setTimeout(
					initialize(),
					100
				);
			},
			error : System.ajaxError
			
		});
	});
});
</script>
<?
	}

}

?>

