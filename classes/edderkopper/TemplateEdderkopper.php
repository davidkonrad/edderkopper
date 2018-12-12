<?

error_reporting(E_ALL);


class TemplateEdderkopper extends TemplateBase {
	private $class_;
	private $info;
	private $page_id;

	//hack : 
	//$class can be class OR a page_id, so we can handle static pages
	public function __construct($class) {
		parent::__construct($class);

		if (is_object($class)) {
			$this->class_=$class;
			$this->info=$class->getInfo();
		} else {
			$this->page_id=$class;
			$this->info=$this->getInfo();
		}
	}

	public function getInfo() {
		$class_name=get_class($this);
		$SQL='select c.page_id, c.lang_id, c.anchor_caption, c.anchor_title, c.semantic_name, c.title, c.meta_desc '.
			'from zn_page_content c '.
			'where c.lang_id='.$_SESSION[LANGUAGE].' and c.page_id='.$this->page_id;
		return $this->getRow($SQL);
	}

	public function draw() {
		$this->pageHead();
		$this->drawWrapper();
		$this->drawBody();
		$this->drawBeforeFooter();
		$this->drawFooter();
	}

	private function drawLangIcon($id, $icon, $alt) {
		$href=$this->currentSemanticName();

		$a=array('lang=1','lang=2','&lang=1','&lang=2');
		$href=str_ireplace($a, '', $href,$count);

		if (strpos($href,'?')>0) {
			$href.='&lang='.$id;
		} else {
			$href.='?lang='.$id;
		}
		$border=($_SESSION['LANG']==$id) ? ' style="border:1px dotted #33613d;"' : ''; //' style="border:2px outset gray;"';
		echo '<a href="'.$href.'"><img src="'.$icon.'" width="20" alt="'.$alt.'"'.$border.'/></a>';
	}

	protected function drawWrapper() {
?>
<div id="top">
<?
$links=$this->getRelatedContent(true);
$loop=false;
$current=$this->currentSemanticName();
foreach ($links as $link) {
	if ($loop) echo '<span class="separator">&#9830</span>';
	if ($link['semantic_name']!=$current) {
		echo '<a href="'.$link['semantic_name'].'">';
		echo $link['anchor_caption'];
		echo '</a>';
	} else {
		echo '<span class="active">'.$link['anchor_caption'].'</span>';
	}
	$loop=true;
}
?>
</div>
<div id="MainFrame">
<? echo ($_SESSION[LANGUAGE]==1) 
	? '<div id="top-cnt" style="background-image : url(img/edderkopper/edderkop_js_dansk_II.jpg);background-size:980px;height:135px;">'
	: '<div id="top-cnt" style="background-image : url(img/edderkopper/edderkop_js_engelsk.jpg);background-size:980px;height:135px;">'
?>
		<div id="sitename-cnt">
		</div>
</div>
	<div id="page-body">
<!-- page content begin -->
<?
	}

	protected function drawStaticPage($page_id) {
		$SQL='select c.title, c.anchor_caption, c.meta_desc, s.page_html '.
			'from zn_page_content c, zn_page_static s '.
			'where (c.page_id='.$page_id.' and c.lang_id='.$_SESSION[LANGUAGE].') '.
			'and (s.page_id=c.page_id) and s.lang_id='.$_SESSION[LANGUAGE];
		$this->row=$this->getRow($SQL);

		$links = $this->getLangLinks($page_id);
		LANG::flagMenu($links);

		echo '<fieldset id="static'.$this->page_id.'">';
		echo '<legend>'.$this->row['anchor_caption'].'</legend>';
		echo stripslashes($this->row['page_html']); //due to $this->q() when inserted
		echo '</fieldset>';
	}

	protected function drawBody() {
		if (is_object($this->class_)) {
			//$this->debug($this->class_);
			$this->class_->drawBody();
		} else {
			$this->drawStaticPage($this->page_id);
		}
	}

	// override to insert additional lines in the <head>..</head> section
	protected function extraHead() {
		if (is_object($this->class_)) {
			$this->class_->extraHead();
		}
	}

	public function drawLexLink() {
		$page_id = 81; //ClassEdderkopperLex has page_id 81
		$SQL = 'select * from zn_page_content where page_id='.$page_id.' and lang_id='.$_SESSION['LANG'];
		$row = $this->getRow($SQL);			

		echo '<div style="width:400px;text-align:center;height:30px;margin-left:auto;margin-right:auto;">';
		echo '<a href="'.$row['semantic_name'].'" title="'.$row['anchor_title'].'">'.$row['anchor_caption'].'</a>';
		echo '<br><br><br><br><br>';
		echo '<div>';
	}

	protected function drawFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	$("input:button").button();$("input:submit").button();
	$.each($('a.blank'), function () {
		$(this).attr('target','_blank');
	})
});
</script>
</div> <!-- mainFrame -->
</div> <!-- page content end -->
<div id="footer">
<div id="footer-col-left" style="margin-left: 30px;">
<?
if ($_SESSION['LANG']==1) {
	echo '<img src="img/ku_co_dk_h.jpg" style="float:left;height:90px;" title="Københavns Universitet">';
} else {
	echo '<img src="img/ku_co_uk_h.jpg" style="float:left;height:90px;" title="Københavns Universitet">';
}
?>
<img src="img/nhm-logo.png" style="margin-left:30px;padding-top:16px;" title="Naturhistorisk Museum Aarhus">
</div>
<div id="footer-col-right"><br>
</div>
</div>
<?
$this->drawLexLink();
?>
<script>
$(document).ready(function()  {
	$flagMenu = $("#flag-menu");
	$logo = $("#top-cnt");
	if ($flagMenu.length == 0 || $logo.length == 0) return;
	$flagMenu.css('left', $logo.width()-80);
	$flagMenu.css('top', $logo.offset().top-50);

	//remove additional flag-menu's added by sub pages
	var test=$('.flag-menu-cnt');
	if (test.length>1) for (var i=test.length;i>0;i--) {
		$(test[i]).remove();
	}
});
</script>
</body>
</html>
<?
	}

	protected function getMetaDesc() {
		if ($this->class_) {
			$desc = $this->class_->info['meta_desc'];
		} else {
			$desc = $this->info['meta_desc'];
		}
		return $desc!='' ? $desc : 'Danmarks Edderkopper er en oversigt over alle kendte edderkoppe-arter i Danmark, inkl en søgbar database over verificerede edderkoppe-fund';
	}

	protected function getPageTitle() {
		if ($this->class_) {
			$title = $this->class_->info['title']!='' ? $this->class_->info['title'] : $title = $this->info['title'];
		} else {
			$title = $this->info['title'].' - Danmarks Edderkopper';
		}
		return $title!='' ? $title : 'Danmarks Edderkopper';
	}

	//anhything that needs to be inserted before the footer, like a scipt
	protected function drawBeforeFooter() {
		$this->GA();
		if (is_object($this->class_)) {
			$this->class_->drawBeforeFooter();
		}
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
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title><? echo $this->getPageTitle(); ?></title>
<meta name="description" content="<? echo $this->getMetaDesc(); ?>" />
<link rel="shortcut icon" href='http://ku.dk/images/favicons/favicon_nat.ico' />
<link rel="stylesheet" href="css/zn.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/edderkopper-template.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/autocomplete.js"></script>
<script type="text/javascript" language="javascript" src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/search.js"></script>
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/system.js"></script>
<meta name="google-site-verification" content="KHDDqSOxRL2dc9nV-5cthqyxTSmfdAd0ENFZB56m5zU" />
<? $this->extraHead(); ?>
<style>
fieldset {
  padding: 0;
  margin: 0;
  border: 0;
}
legend {
  display: block;
  width: 100%;
  padding: 0;
  margin-bottom: 20px;
  font-size: 21px;
  line-height: 40px;
  color: #333333;
  border: 0;
  border-bottom: 1px solid #e5e5e5;
}
fieldset {
	margin: 0px;
	padding: 16px;
}
legend {
	margin-bottom: 0px;
}
body {
	line-height: 20px;
	font-size: 14px;
}
.inline {
	float: none;
	clear: none;
	display: inline;
}
a {
	color: #33613D;
}
</style>
</head>
<body>	
<?
	}
}

?>
