<?
/**
 * 
 * Template for detail pages
 *
**/
class TemplateDetail extends TemplateSimple {

	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
function adjustDetails() {
	if ((".detail-left").length>0) {
		var padding=14; //fieldset inner padding
		var h=$(".detail-right img").outerHeight();
		if ($(".detail-left").height()<h) {
			$(".detail-left").height(h+padding); 
		}
		h=$(".detail-left").height();
		if ($(".detail-right").height()<h) {
			$(".detail-right").height(h);
		}
	}
}
$(document).ready(function() {
	setTimeout("adjustDetails()", 300);
});
</script>
<?
	}

	protected function extraHead() {
?>
<style type="text/css">
td.caption {
	vertical-align: top;
	font-weight: bold;
	white-space: nowrap;
	min-width:120px;
}
td.info {
	vertical-align: top;
}
</style>
<?
	}

	protected function drawRow($caption, $value, $class='') {
		echo '<tr>';
		echo '<td class="caption">'.$caption.'&nbsp;&nbsp;</td>';
		echo '<td class="info">'.$value.'</td>';
		echo '</tr>'."\n";
	}

	protected function drawCloseLink() {
		echo '<span style="float:right;clear:none;margin-top:5px;margin-right:20px;">';
		echo '<a style="color:#33613d;font-size:14px;" href="#" onclick="window.close();">'.trans(ZN_CLOSE_WINDOW_BACK_TO_SEARCH).'</a>';
		echo '</span>';
	}

}

?>
