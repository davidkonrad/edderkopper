<?
/**
 * 
 * Template with one page (body)
 *
**/
class TemplateSimple extends TemplateBase {

	public function draw() {
		$this->pageHead();
		$this->drawWrapper();
		$this->drawBody();
		$this->drawBeforeFooter();
		$this->drawFooter();
	}

	protected function drawWrapper() {
		$index = ($this->isSNM()=='true') ? 'index.php' : 'http://snm.ku.dk/';
?>
<div id="top">
  <div id="brand-KU" title="<? echo trans(LAB_BRAND_TITLE);?>"></div>
</div>
<div id="MainFrame">
  <div id="KUlogo">
    <a href="<? echo $index;?>" title="<? echo trans(ZN_GOTO_FRONTPAGE);?>">
      <img src="img/ku.gif" alt="<? echo trans(ZN_GOTO_FRONTPAGE);?>">
    </a>
   </div>
   <div id="BMlogo"><? echo trans(ZN_PAGE_HEADER); ?></div>
   <div id="snm-top"></div><div id="redbar"></div>
   <div id="page-body">
<!-- page content begin -->
<?
	}

	//from Base.php
	//return language specific URL's for page_id
	protected function getLangLinks($page_id) {
		$links=array();
		$SQL='select lang_id, semantic_name from zn_page_content where page_id='.$page_id.' order by lang_id';
		$result=$this->query($SQL);
		while ($row = mysql_fetch_assoc($result)) {
			//echo $row['semantic_name'];
			$href=$row['semantic_name'];
			$href=str_replace(' ', '%20', $href);
			$links[$row['lang_id']]=$href;
		}
		return $links;
	}	

	protected function drawRelatedContent() {
		if (isset($_SESSION[CURRENT_PAGE_ID])) {
			/*
			$SQL='select category_id from zn_page where page_id='.$_SESSION[CURRENT_PAGE_ID];
			$res=$this->getRow($SQL);
			$cat=$res['category_id'];

			$SQL='select p.page_id, p.kolofon, c.anchor_caption, c.anchor_title, c.semantic_name '.
				'from zn_page p, zn_page_content c '.
				'where c.lang_id='.$_SESSION[LANGUAGE].' and c.page_id=p.page_id and p.page_id<>'.$_SESSION[CURRENT_PAGE_ID].' '.
			 	'and p.category_id='.$cat.' and visible=1 order by weight';

			mysql_set_charset('Latin1');
			$res=$this->query($SQL);
			if ($res) {
				echo '<script type="text/javascript" src="js/fold.js"></script>'."\n";
			}
			while ($row=mysql_fetch_array($res)) {
			*/
			$content=$this->getRelatedContent();
			foreach ($content as $row) {
				$processed=false;
				HTML::divider(20);

				//static page
				$SQL='select page_html from zn_page_static where page_id='.$row['page_id'].' and lang_id='.$_SESSION[LANGUAGE];
				$result=$this->query($SQL);
				if (($result) && (mysql_num_rows($result)>0)) {
					$data=mysql_fetch_array($result);
					$processed=true;
					$class=($row['kolofon']==1) ? 'fold-box kolofon' : 'fold-box';
					echo '<fieldset id="f'.$row['page_id'].'" class="'.$class.'" style="">';
					echo '<legend>'.$row['anchor_caption'].'</legend>';
					echo '<a href="#" page_id="'.$row['page_id'].'" id="arr'.$row['page_id'].'" class="fold-arrow">&#9660;</a>';
					echo '<div style="display:none;" id="cnt'.$row['page_id'].'">';

					echo stripslashes($data['page_html']); //due to CKEditor slashes
	
					echo '</div>';
					echo '</fieldset>';
				} 

				//link
				$SQL='select l.blank, l.url, t.name, t.icon '.
					'from zn_page_link l, zn_page_link_types t '.
					'where l.page_id='.$row['page_id'].' and (l.linktype_id=t.linktype_id)';
				mysql_set_charset('Latin1');
				$result=$this->query($SQL);
				if (($result) && (mysql_num_rows($result)>0)) {
					$processed=true;
					$data=mysql_fetch_array($result);
					//$target=($data['blank']==1) ? ' target=_blank' : '';
					$target=($data['blank']==1) ? ' class="blank"' : '';

					echo '<fieldset id="f'.$row['page_id'].'" class="fold-box">';
					echo '<legend>'.$row['anchor_caption'].'</legend>';
					echo '<a href="#" id="arr'.$row['page_id'].'" page_id="'.$row['page_id'].'" class="fold-arrow">&#9660;</a>';
					echo '<div style="display:none;" id="cnt'.$row['page_id'].'">';

					echo '<a href="'.$data['url'].'" title="'.$row['anchor_title'].'"'.$target.'>'.trans($row['anchor_caption']).'</a>';
					echo '&nbsp;<img src="'.$data['icon'].'" alt="'.$data['name'].'" style="z-index:67;position:absolute;"/><br/><br/>';
					
					//link_desc
					$SQL='select link_desc from zn_page_link_desc '.
						'where page_id='.$row['page_id'].' and lang_id='.$_SESSION[LANGUAGE];
					mysql_set_charset('Latin1');
					$result=$this->query($SQL);
					if (($result) && (mysql_num_rows($result)>0)) {
						$data=mysql_fetch_array($result);
						echo $data['link_desc'].'<br/><br/>';
					}

					echo '</div>';
					echo '</fieldset>';
				}

				//assume class if not processed
				if (!$processed) {
					$SQL='select class_name from zn_page_class where page_id='.$row['page_id'];
					$data=$this->getRow($SQL);
					if ($data) {
						if (get_parent_class($data['class_name'])!='TemplateSimple') {
							//create dummy fieldset
							echo '<fieldset id="df'.$row['page_id'].'" class="fold-box" style="display:block;height:30px;">';
							echo '<legend>'.$row['anchor_caption'].'</legend>';
							echo '<a href="#" id="arr'.$row['page_id'].'" page_id="'.$row['page_id'].'" class="fold-arrow" search="yes">&#9660;</a>';
							echo '</fieldset>';
							echo '<div id="search'.$row['page_id'].'" style="display:none;clear:both;">';
							$class_=new $data['class_name'];
							//
							$class_->extraHead();
							//
							$class_->drawBody();
							$class_->drawBeforeFooter();
							echo '</div>';
?>
<script type="text/javascript">
$(document).ready(function() {	
	$("#<? echo 'search'.$row['page_id'];?>").resize(function(e){
		if ($("#search<? echo $row['page_id'];?>").is(':visible')) {
			var h=$("#search<? echo $row['page_id'];?>").height();
			if ($.browser.webkit) {h=h+2;}
			h='-'+(parseInt(h)-parseInt(25))+'px';
			$("#arr<? echo $row['page_id'];?>").css('top',h);
			$("#arr<? echo $row['page_id'];?>").css('left','-14px');
		}
	});
});
</script>
<?

						}
					}
				}
			}
		}
	}

	protected function drawBody() {
		Lang::flagMenu($this->getLangLinks($this->page_id));
		//Lang::drawFlagMenu();
	}

	// override to insert additional lines in the <head>..</head> section
	protected function extraHead() {
	}

	protected function drawFooter() {
		$this->GA();
?>
</div> <!-- mainFrame -->
</div> <!-- page content end -->
<div id="footer">
<div id="footer-col-left"><br/><address>
<a href="http://snm.ku.dk/">Statens Naturhistoriske Museum</a><br>
<a href="http://www.ku.dk/">K&oslash;benhavns Universitet</a><br>
&Oslash;ster Voldgade 5-7<br/>1350 K&oslash;benhavn K<br>EAN nr:5798000418004 
</address></div><div id="footer-col-right"><br>Kontakt:
<address>Statens Naturhistoriske Museum<br/>      
<a href="#" onclick="this.href='mailto:snm@snm.ku.dk';return true;">snm<!-- abc -->&#64;<!-- abc -->snm.ku.dk</a><br/>
Tel. +45 35 32 22 22<br/></address>
</div>
</div>
</body>
</html>
<?
	}

	//content of meta content=description, override for each individual page
	protected function getMetaDesc() {
	}

	//content of <title></title>, override for each individual page
	protected function getPageTitle() {
		return trans(ZN_PAGE_TITLE);
	}

	//anhything that needs to be inserted before the footer, like a scipt
	protected function drawBeforeFooter() {
	}

	//<head>...</head>
	protected function pageHead() {
/*
*/
?>
<!doctype html>
<html> 
<head> 
<meta http-equiv="x-ua-compatible" content="IE=Edge"/>
<meta charset="UTF-8">
<title><? echo $this->getPageTitle(); ?></title>
<meta name="description" content="<? $this->getMetaDesc(); ?>" />
<meta name="google-site-verification" content="HSgAR21NPqTsqtGQpqkIIaPtxpQyxbBgooQaANMEw_Q" />
<link rel="shortcut icon" href='img/favicon_nat.ico' />
<link rel="stylesheet" href="css/zn.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/template.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/style.css?ver=123" type="text/css" media="screen" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="css/ui.css" type="text/css" media="screen" rel="stylesheet" />
<script src="plugins/resize.js"></script>
<script src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script src="DataTables-1.9.1/extras/TableTools/media/js/TableTools.min.js"></script>
<script src="js/search.js"></script>
<script src="js/date.js"></script>
<script src="js/system.js"></script>
<script src="js/fold.js"></script>
<script src="plugins/chosen/chosen.jquery.js"></script>
<link rel="stylesheet" href="plugins/chosen/chosen.css" type="text/css" />
<? $this->extraHead(); ?>
</head>
<body>	
<?
	}
	
}

?>
