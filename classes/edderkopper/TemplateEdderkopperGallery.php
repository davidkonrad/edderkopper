<?

class TemplateEdderkopperGallery extends TemplateEdderkopper { //TemplateBase
	private $class_;
	private $info;
	private $page_id;

	//hack : $class can be class OR a page_id, so we can handle static pages
	public function __construct($class) {
		parent::__construct($class);

		if (is_object($class)) {
			$this->class_=$class;
			$this->info=$class->getInfo();
		} else {
			$this->page_id=$class;
		}
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
		$border=($_SESSION['LANG']==$id) ? ' style="border:1px dotted #33613d;"' : ''; 
		echo '<a href="'.$href.'"><img src="'.$icon.'" width="20" alt="'.$alt.'"'.$border.'/></a>';
	}

	private function getSpeciesFullName($speciesID) {
		$SQL='select s.Species, g.Genus '.
			'from edderkopper_species s, edderkopper_genus g '.
			'where s.SpeciesID='.$speciesID.' '.
			'and s.GenusID=g.GenusID';
		$row = $this->getRow($SQL);
		return $row['Genus'].' '.$row['Species'];
	}
		
	protected function drawStaticPage($page_id) {
		$SQL='select c.title, c.anchor_caption, c.meta_desc, s.page_html '.
			'from zn_page_content c, zn_page_static s '.
			'where (c.page_id='.$page_id.' and c.lang_id='.$_SESSION[LANGUAGE].') '.
			'and (s.page_id=c.page_id) and s.lang_id='.$_SESSION[LANGUAGE];
		$this->row=$this->getRow($SQL);

		$links = $this->getLangLinks($page_id);
		Lang::flagMenu($links);

		echo '<fieldset id="static'.$this->page_id.'">';
		echo '<legend>'.$this->row['anchor_caption'].'</legend>';

		$subject = $_SESSION[LANGUAGE]==1 ? 'SubjectDK' : 'SubjectUK';
		$SQL='select p.SpeciesID, p.Filename, p.'.$subject.' as subject '.
				'from edderkopper_photo p, edderkopper_species s '.
				'where p.SpeciesID=s.SpeciesID '.
				//now the speices table holds confimed danish species only
				//'and s.VH_Distribution like "%Denmark%" '.
				'order by rand() limit 10';

		$result=$this->query($SQL);
		echo '<div style="float:right;margin-left:10px;margin-bottom:10px;background-color:black;width:380px;height:295px;">';
?>		
<div 
<div id="gallery" class="carousel slide">
  <div class="carousel-inner">
<?
	$first = true;

	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		if ($first) {
			echo '<div class="active item">';
			$first=false;
		} else {
			echo '<div class="item">';
		}
		$fullName=$this->getSpeciesFullName($row['SpeciesID']);

		switch ($_SESSION['LANG']) {
			case 1 : 
				$href='artsbeskrivelse?taxon='.urlencode($fullName);
				break;
			default :
				$href='species-description?taxon='.urlencode($fullName);
				break;
		}
		
		$photo=stripslashes($row['Filename']); //filename has leading \

		echo '<a href="'.$href.'">';
		echo '<img src="lissner/'.$photo.'">';
		echo '<div style="clear:both;padding:10px;background-color:black;color:white;font-weight:bold;">';
		echo '<em>'.$fullName.'</em> - '.$row['subject'];
		echo '</div>';
		echo '</a>';
		echo '</div>';
	}
?>
  </div>
</div>
<?
		echo '</div>';
		echo '<p>';
		echo stripslashes($this->row['page_html']); //due to $this->q() when inserted
		echo '</p>';
		echo '</fieldset>';
	}

	protected function drawBody() {
		if (is_object($this->class_)) {
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

	protected function drawFooter() {
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#gallery').carousel({
		interval : 4000,
	});
	$('[data-fancybox="images"]').fancybox({
		protect: true,
		fullScreen: {
			autoStart: false
		}
	});
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
<?
//show only login when lang is danish and page is redaktionskomite
if ($this->currentPageId()==39) {
	if ($_SESSION[LANGUAGE]==1) {
		echo '<span style="float:right;padding:30px;">';
		Login::drawLoginBoxInline();
		echo '</span>';
	}
}
?>
</div>
<?
$this->drawLexLink();
?>
<script>
$(document).ready(function()  {
	System.adjustFlagMenu();
});
</script>
</body>
</html>
<?
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
<link rel="shortcut icon" href='img/favicon_nat.ico' />
<link rel="stylesheet" href="css/edderkopper-template.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="plugins/bootstrap/js/bootstrap.js"></script>
<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css" />
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.js"></script>
<script type="text/javascript" src="js/system.js"></script>
<meta name="google-site-verification" content="KHDDqSOxRL2dc9nV-5cthqyxTSmfdAd0ENFZB56m5zU" />
<style>
a {
	color: #33613D;
}
a:hover {
	color: green;
	text-decoration: none;
}
ol.carousel-indicators {
	display: none;
}
/* override bootstrap settings */
fieldset {
	margin: 0px;
	padding: 16px;
}
legend {
	margin-bottom: 0px;
}
.carousel {
	margin-bottom: 5px; 
}
.fancybox-is-open .fancybox-bg {
	opacity: .6;
}
</style>
<? $this->extraHead(); ?>
</head>
<body>	
<?
	}
}

?>
