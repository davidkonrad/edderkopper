<?

//shuld not be hardcoded
define('LINK_TEKST', 'Link tekst er linkets "anchor" eller tekst.');
define('HOVER_TEKST', 'Hover tekst er popup-beskrivelsen når musen føres henover linket.');
define('TITEL_TEKST', 'Titel er titlen i browseren og den overskrift man vil se når linket er blevet indekseret af søgemaskiner.');
define('SEMANTISK', 'Giver mulighed for en "semantisk URL". Må ikke indeholde blanktegn eller specialtegn. Brug underscore, bindestreg eller plus til at dele ord.');
define('META', 'Meta description er en tekst på op til 160 karakterer, som søgemaskiner indekserer og (oftest) er den tekst man ser under et link i søgemaskinerne.');
define('KOLOFON', 'Hvis en side er <i>kolofon</i> vises den øverst som aller første boks (uanset vægt) og altid åben / klappet ud.');
define('SYNLIGHED', '<b>Never visible</b> : Siden vises aldrig<br><b>Always visible</b> : Siden vises altid<br><b>When logged in</b> : Siden vises hvis man er logget ind <u>og</u> har rettigheder til den kategori siden tilhører.');
define('STANDALONE_HELP', 'Som standard vises sider i kasser der kan åbnes op og lukkes i, sammen med evt øvrige sider hidrørende samme kategori. Hvis en side defineres som "standalone" vises siden uden rammer, og uden andre sider fra den pågældende kategori.');
define('ALTERNATIV_TEMPLATE', 'Alternativ template. Navnet på den PHP-klasse der indeholder den templaten. PageLoaderen vil så loade siden med den angivne template.');

class EditPage extends TemplateSimple {
	private $page_id;
	private $row;
	private $lang_id;
	private $page_type; //by defined constant

	public function __construct($page_id, $lang_id) {
		parent::__construct();
		$this->page_id = $page_id;
		$this->lang_id = $lang_id;
		$this->loadData();
	}

	protected function buttonBar() {
		echo '<span style="float:left;text-align:left;">';
		echo '<input type="button" onclick="EditPage.index();" value="&lt; Forside"/>';
		echo '<input type="button" onclick="EditPage.save();" value="Gem"/>';
		echo '<input type="button" onclick="EditPage.reload();" value="Genindl&aelig;s"/>';
		echo '<input type="button" onclick="EditPage.preview();" value="Preview"/>';
		echo '</span>';
		$this->langSelect();
		echo '<hr style="clear:both;">';
	}
		
	private function langSelect() {
		echo '<span style="float:right;clear:right;">';
		echo '<select id="lang_id" onchange="EditPage.changeLang();" style="width:100px;">';
		echo HTML::getOption(1, 'Dansk', $this->lang_id);
		echo HTML::getOption(2, 'English', $this->lang_id);
		echo '</select>';
		echo '</span>';
	}

	protected function extraHead() {
?>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/editpage.js"></script>
<style type="text/css">
.help {
	position: absolute;
	z-index: 100;
	display: none;
	border: 1px solid #ebebeb;
	width: 200px;
	padding: 10px;
	background-color: #FFFFCC;
}
.help-ico {
	position:relative;
	top:4px;
	left:3px;
	cursor:pointer;
	vertical-align: top;
}
</style>

<?
	}

	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	if ($("#page_html").length>0) {
		CKEDITOR.replace('page_html', {width:"900px",height:"300px"});
	}
});
$(document).ready(function() {
	$(".help-ico").mouseover(function(e) {
		var help=$(this).parent().find('.help');
		if ($(help).is(':visible')) return false;
		$(help).css('left', e.currentTarget.offsetLeft+5);
		$(help).css('top', e.currentTarget.offsetTop+5);
		$(help).show();
	});
	$(".help-ico").mouseout(function() {
		$(this).parent().find('.help').hide();
	});
	$("#lang_visibility").change(function() {
		if ($(this).val()=='0') {
			EditPage.enableForm(false);
		} else {
			EditPage.enableForm(true);
		}
	});
});
</script>
<?
	}

	private function visibleSelect($value) {
		$items = array(ADM_VISIBLE_NEVER, ADM_VISIBLE_ALWAYS, ADM_VISIBLE_LOGGED_IN);
		echo '<select id="visible" name="visible" style="width:150px;">';
		for ($i=0;$i<count($items);$i++) {
			$selected=($i==$value) ? ' selected=selected' : '';
			echo '<option value="'.$i.'"'.$selected.'>'.trans($items[$i]).'</option>';
		}
		echo '</select>';
	}
		
	protected function categorySelect($category_id) {
		$SQL='select category_id, caption from zn_category_desc where lang_id='.$_SESSION[LANGUAGE];
		$result=$this->query($SQL);
		$return = '<select name="category_id" id="category_id" style="width:300px;">';
		$return.= '<option value="">[v&aelig;lg kategori]</option>';
		while ($row = mysql_fetch_array($result)) {
			$sel = ($row['category_id']==$category_id) ? ' selected="selected"' : '';
			$return.='<option value="'.$row['category_id'].'"'.$sel.'>'.trans($row['caption']).'</option>';
		}
		$return.='</select>';
		return $return;
	}

	protected function linkTypeSelect($linktype_id) {
		$SQL='select linktype_id, name from zn_page_link_types';
		$result=$this->query($SQL);
		$return = '<select name="linktype_id" id="linktype_id" style="width:80px;">';
		while ($row = mysql_fetch_array($result)) {
			$sel = ($row['linktype_id']==$linktype_id) ? ' selected="selected"' : '';
			$return.='<option value="'.$row['linktype_id'].'"'.$sel.'>'.$row['name'].'</option>';
		}
		$return.='</select>';
		return $return;
	}

	protected function helpText($text) {
		echo '<img src="ico/info.gif" class="help-ico">';
		echo '<div class="help">';
		echo $text;
		echo '</div>';
	}
		
	protected function loadData() {
		//load fields common to all page types
		$SQL='select p.category_id, p.weight, p.visible, p.standalone, p.kolofon, p.alternative_template, '.
			'c.anchor_caption, c.anchor_title, c.semantic_name, c.title, c.meta_desc, c.lang_visibility '.
			'from zn_page p, zn_page_content c '.
			'where p.page_id='.$this->page_id.' and (p.page_id=c.page_id) and c.lang_id='.$this->lang_id;

		$this->row=$this->getRow($SQL);
		$this->page_type=$this->getPageType('', $this->page_id);

		switch ($this->page_type) {
			case PAGE_STATIC : $SQL='select page_html from zn_page_static '.
					'where page_id='.$this->page_id.' and lang_id='.$this->lang_id;
					$temp = $this->query($SQL);
					$temp = mysql_fetch_array($temp);
					$this->row['page_html']=stripslashes($temp[0]);
					break;

			case PAGE_CLASS : $SQL='select class_name from zn_page_class '.
					'where page_id='.$this->page_id;
					$temp = $this->query($SQL);
					$temp = mysql_fetch_array($temp);
					$this->row['class_name']=$temp[0];
					break;

			case PAGE_LINK : $SQL='select linktype_id, blank, url from zn_page_link '.
					'where page_id='.$this->page_id;
					$temp = $this->query($SQL);
					$temp = mysql_fetch_array($temp);
					$this->row['linktype_id']=$temp['linktype_id'];
					$this->row['blank']=$temp['blank'];
					$this->row['url']=$temp['url'];
					//get link_desc
					$SQL='select link_desc from zn_page_link_desc '.
						'where page_id='.$this->page_id.' and lang_id='.$this->lang_id;
					$temp = $this->query($SQL);
					$temp = mysql_fetch_array($temp);
					$this->row['link_desc']=$temp['link_desc'];
					break;			
					

			default : echo 'Severe error'; break;
		}
	}

	protected function drawForm() {
?>
<form id="edit-page" method="get" action="">
<input type="hidden" name="page_id" id="page_id" value="<? echo $this->page_id;?>"/>
<input type="hidden" name="lang_id" id="lang_id" value="<? echo $this->lang_id;?>"/>
<table>
<tr>
<td>Aktiv for dette sprog</td>
<td><? HTML::selectYesNo('lang_visibility', $this->row['lang_visibility']);?></td>
</tr>
<tr><td colspan=2><hr class="search"></td></tr>
<tr>
<td>Link tekst</td>
<td><input type="text" name="anchor_caption" style="width:400px;" value="<? echo $this->row['anchor_caption'];?>"/>
<? $this->helpText(LINK_TEKST);?>
</td>
</tr>
<tr>
<td>Hover tekst</td>
<td><input type="text" name="anchor_title" style="width:600px;" value="<? echo $this->row['anchor_title'];?>"/>
<? $this->helpText(HOVER_TEKST);?>
</td>
</tr>
<tr>
<td>Titel</td>
<td><input type="text" name="title" style="width:600px;" value="<? echo $this->row['title'];?>"/>
<? $this->helpText(TITEL_TEKST);?>
</td>
</tr>
<tr>
<td>Semantisk navn</td>
<td><input type="text" name="semantic_name" style="width:600px;" value="<? echo $this->row['semantic_name'];?>"/>
<? $this->helpText(SEMANTISK);?>
</td>
</tr>
<tr>
<td style="vertical-align:top;">Meta desc.</td>
<td style="vertical-align:middle;"><textarea name="meta_desc" style="width:600px;height:30px;"><? echo $this->row['meta_desc'];?></textarea>
<? $this->helpText(META);?>
</td>
</tr>
<tr>
<td>Kategori</td>
<td><? echo $this->categorySelect($this->row['category_id']); ?></td>
</tr>
<tr>
<td>Er kolofon</td>
<? $checked=($this->row['kolofon']==1) ? ' checked="checked"' : '';?>
<td><input type="checkbox" name="kolofon" id="kolofon" <?echo $checked;?>>
<? $this->helpText(KOLOFON);?>
</td>
</tr>
<tr>
<tr>
<td>Synlighed</td>
<td><? $this->visibleSelect($this->row['visible']);?>
<? $this->helpText(SYNLIGHED);?>
</td>
</tr>
<td>Standalone</td>
<td><? HTML::selectYesNo('standalone', $this->row['standalone']);?>
<? $this->helpText(STANDALONE_HELP);?>
</td>
</tr>
<tr>
<td>V&aelig;gt</td>
<td><? HTML::selectWeight($this->row['weight']);?></td>
</tr>

<?
		switch ($this->page_type) {
			case PAGE_STATIC : 
?>
<tr>
<td>Alternativ template</td>
<td><input type="text" name="alternative_template" style="width:600px;" value="<? echo $this->row['alternative_template'];?>"/>
<? $this->helpText(ALTERNATIV_TEMPLATE);?>
</td>
</tr>

<tr>
<td colspan="2"><textarea id="page_html" name="page_html"><? echo $this->row['page_html']; ?></textarea></td>
</tr>
<?
			break;
			case PAGE_CLASS : 
?>
<tr>
<td>PHP klasse</td>
<td><input type="text" name="class_name" style="width:300px;" value="<? echo $this->row['class_name'];?>"/></td>
</tr>
<?
			break;
			case PAGE_LINK :
?>
<tr>
<td>Link type</td>
<td><? echo $this->linkTypeSelect($this->row['linktype_id']);?></td>
</tr>
<tr>
<td>&Aring;bne i ny side</td>
<td><? HTML::selectYesNo('blank', $this->row['blank']);?></td>
</tr>
<tr>
<td>URL / link</td>
<td><input type="text" name="url" style="width:600px;" value="<? echo $this->row['url'];?>"/></td>
</tr>
<tr>
<td style="vertical-align:top;">Beskrivelse</td>
<td><textarea id="link_desc" name="link_desc" style="width:600px;height:50px;"><? echo $this->row['link_desc'];?></textarea></td>
</tr>
<tr>
<?
			break;
			default :
				echo 'Severe error';
				break;
		}
?>	

</table>
</form>
<?
	}


	protected function drawBody() {
		echo HTML::h2('Rediger side #'.$this->page_id);
		HTML::divider(5);
		$this->buttonBar();
		$this->drawForm();
	}
}
?>
