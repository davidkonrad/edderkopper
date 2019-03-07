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
$("#checklist-result").load('ajax/edderkopper_checklist.php?action=get');
</script>
</fieldset>
<?
	}

	public function extraHead() {
?>
<?
	}

	public function drawBeforeFooter() {
?>
<script type="text/javascript">
$("#btn-save-pdf").click(function() {
	System.wait(true);
  var a = document.createElement('a')
	a.setAttribute('target', '_blank')
  a.setAttribute('href', 'edderkopper-upload/tjekliste/tjekliste.pdf')
  a.setAttribute('download', 'tjekliste.pdf')
	a.style.display = 'none';
  document.body.appendChild(a);
 	a.click();
  document.body.removeChild(a);
	Search.wait(false);
});

</script>
<?
	}

}
?>
