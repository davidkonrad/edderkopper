<?

class DetailBase extends Base {

	public function __construct() {
		parent::__construct();
		$this->info=$this->getInfo();
	}

	//http://stackoverflow.com/questions/1993721/how-to-convert-camelcase-to-camel-case
	public function deCamelize($camel,$splitter=" ") {
	    $camel=preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $camel));
	   	//return strtolower($camel);
		return $camel;
	}

	// override to set more descriptive titles for detail pages
	public function getTitle() {
		return $this->info['title'];
	}

	// override to set more detailed meta description for detail pages
	public function getMeta() {
		return $this->info['meta_desc'];
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
//needed due to page-body overflow-y:visible and some large detailpages
function adjustPageHeight() {
	var height=80;
	$("#page-body").children().each(function() {
		if ($(this).prop('tagName')=='FIELDSET') {
			if (!$(this).hasClass('detail-right')) {
				height=height+$(this).height()+40;
			}
		}
	});
	$("#page-body").height(height+'px');
}
function adjustDetails(left, right) {
	if (!left) {
		var left='.detail-left';
		var right='.detail-right';
	}
	if ($(left).length>0) {
		var padding=17; //fieldset inner padding
		var h=$(right+' img').outerHeight();
		if ($(left).height()<h) {
			$(left).height(h+padding); 
		}
		h=$(left).height();
		if ($(right).height()<h) {
			$(right).height(h);
		}
	}
}
$(document).ready(function() {
	setTimeout("adjustDetails()", 300);
	setTimeout("adjustPageHeight()", 600);
});
</script>
<?
	}

	public function extraHead() {
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
		echo '<td class="caption '.$class.'">'.$caption.'&nbsp;&nbsp;</td>';
		echo '<td class="info '.$class.'">'.$value.'</td>';
		echo '</tr>'."\n";
	}

	protected function drawRowLine() {
		echo '<tr>';
		echo '<td colspan="2"><hr></td>';
		echo '</tr>'."\n";
	}

	protected function drawRowHR() {
		echo '<tr>';
		echo '<td colspan="2"><hr class="search"></td>';
		echo '</tr>'."\n";
	}

	protected function drawRowEmpty() {
		echo '<tr>';
		echo '<td colspan="2">'.SPACE.'</td>';
		echo '</tr>'."\n";
	}

	protected function drawCloseLink() {
		echo '<span style="float:right;clear:none;margin-top:5px;margin-right:20px;">';
		echo '<a style="font-size:14px;" href="#" onclick="window.close();">'.trans(ZN_CLOSE_WINDOW_BACK_TO_SEARCH).'</a>';
		echo '</span>';
	}

}

?>
