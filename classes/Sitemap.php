<?

//id's of special category content
define('CATEGORY_CONTENT', 7);
define('CATEGORY_ADMINISTRATION', 4);
define('CATEGORY_TYPER_MAINTENANCE', 5);
define('CATEGORY_PICTUREUPLOAD_MAINTENANCE', 8);
define('CATEGORY_MAINTENANCE', 15);

//visible stattes
define('VISIBLE_NEVER', 0);
define('VISIBLE_ALWAYS', 1);
define('VISIBLE_LOGGED_IN', 2);

class Sitemap extends TemplateRightCol {
	private $user;

	public function __construct() {
		parent::__construct();
		//set DEFAULT_USER if not logged in
		$this->user=$this->loggedIn() ? $_SESSION[LOGIN] : 1;
	}

	private function isLink($page_id) {
		$SQL='select l.blank, l.url, t.icon '.
			'from zn_page_link l, zn_page_link_types t '.
			'where l.page_id='.$page_id.' and (l.linktype_id=t.linktype_id)';
		return $this->getRow($SQL);
	}

	private function drawLink($row) {
		//show only VSIBLE_NEVER for administrator
		if ($row['visible']==VISIBLE_NEVER) {
			if ($this->loggedIn() && !$this->isAdmin()) return;
		}

		echo '<span class="sitemap-link">';
		if ($this->loggedIn() && $this->isAdmin()) {
			echo '<a href="'.EDIT_PAGE.'?page_id='.$row['page_id'].'" title="Rediger side"><img src="ico/page_edit.png"></a>&nbsp;';
			$hidden=(in_array($row['visible'], array(VISIBLE_NEVER, VISIBLE_LOGGED_IN))) ? ' style="color:gray;"' : '';
		} else $hidden='';

		$islink=$this->isLink($row['page_id']);
		if (!$islink) {
			$link = ($row['semantic_name']!='') ? ''.$row['semantic_name'] : '?id='.$row['page_id'];
			echo '<a href="'.$link.'" title="'.$row['anchor_title'].'"'.$hidden.'>'.trans($row['anchor_caption']).'</a><br/>';
		} else {
			$target=($islink['blank']==1) ? ' class="blank"' : '';
			echo '<a href="'.$islink['url'].'" title="'.$row['anchor_title'].'"'.$target.$hidden.'>'.trans($row['anchor_caption']).'</a>';
			echo '&nbsp;<img src="'.$islink['icon'].'" style="z-index:67;position:absolute;" alt=""/><br/>';
		}
		echo '</span>'."\n";
	}

	private function drawCategoryContentLinks() {
		echo '<span class="sitemap-link">';
		echo '<a href="common/CreatePage.php?type=static">';
		echo '<img src="ico/page_add.png" alt=""/>&nbsp;';
		echo trans(CONTENT_CREATE_STATIC);
		echo '</a></span><br>';

		echo '<span class="sitemap-link">';
		echo '<a href="common/CreatePage.php?type=link">';
		echo '<img src="ico/page_add.png" alt=""/>&nbsp;';
		echo trans(CONTENT_CREATE_LINK);
		echo '</a></span><br>';

		echo '<span class="sitemap-link">';
		echo '<a href="common/CreatePage.php?type=class">';
		echo '<img src="ico/page_add.png" alt=""/>&nbsp;';
		echo trans(CONTENT_CREATE_DYNAMIC);
		echo '</a></span><br>';
	}

	private function drawCategories($categories) {
		while ($cat = mysql_fetch_assoc($categories)) {
			$hidden=($cat['visible']=='0') ? ' style="color:gray;"' : '';

			$href=($cat['semantic_name']!='') ? $cat['semantic_name'] : 'index.php?category='.$cat['category_id'];
			echo '<h3><a href="'.$href.'"'.$hidden.'>'.trans($cat['caption']).'</a></h3>'."\n";

			if ($cat['category_id']==CATEGORY_CONTENT) {
				$this->drawCategoryContentLinks();
				echo '<br>';
			} else {
				if ($this->loggedIn()) {
					$SQL='select p.page_id, p.weight, p.visible, c.anchor_caption, c.anchor_title, c.semantic_name '.
						'from zn_page p, zn_page_content c '.
						'where p.category_id='.$cat['category_id'].' '.
						'and (c.lang_id='.$_SESSION[LANGUAGE].') and (p.page_id=c.page_id)  '.	
						'order by p.weight';
				} else {
					$SQL='select p.page_id, p.weight, p.visible, '.
						'c.anchor_caption, c.anchor_title, c.semantic_name '.
						'from zn_page p, zn_page_content c '.
						'where p.category_id='.$cat['category_id'].' '.
						'and (c.lang_id='.$_SESSION[LANGUAGE].') and (p.page_id=c.page_id) and p.visible=1 '.
						'and (c.lang_visibility=1) '.
						'order by p.weight';

				}
				$result=$this->query($SQL);
				while ($row = mysql_fetch_array($result)) {
					$this->drawLink($row);
				}
				echo '<br>';
			}
		}
		if ($this->loggedIn()) {
			$SQL='select * from zn_pages where category_id is null';
		} else {
			$SQL='select * from zn_pages where category_id is null and visible=1';
		}
		if ($this->hasData($SQL)) {
			echo '<h3>Andet</h3>';
			$result=$this->query($SQL);
			while ($row = mysql_fetch_array($result)) {
				$this->drawLink($row);
			}
		}
	}

	protected function drawBody() {
		$SQL='select c.category_id, c.weight, c.visible, d.caption, d.semantic_name '.
			'from zn_category c, zn_user_rights r, zn_category_desc d '.
			'where r.user_id='.$this->user.' and r.category_id=c.category_id '.
			'and d.category_id=c.category_id and d.lang_id='.$_SESSION[LANGUAGE].' '.
			'and c.category_id not in ('.CATEGORY_CONTENT.','.
										CATEGORY_ADMINISTRATION.','.
										CATEGORY_TYPER_MAINTENANCE.','.
										CATEGORY_MAINTENANCE.','.
										CATEGORY_PICTUREUPLOAD_MAINTENANCE.
										') ';

		//if not logged in, dont show categories which are not visible
		if (!$this->loggedIn()) {
			$SQL.='and c.visible=1 ';
		}
		
		$SQL.='order by weight, d.caption ';
		$categories=$this->query($SQL);
		$this->drawCategories($categories);
	}

	protected function drawRightCol() {
		echo '<div style="clear:both;">';
		Lang::flagMenu();
		Login::drawLoginBox();
		echo '</div>';
		echo '<div style="clear:both;float:left;">';
		$SQL='select c.category_id, c.weight, c.visible, d.caption, d.semantic_name '.
			'from zn_category c, zn_user_rights r, zn_category_desc d '.
			'where r.user_id='.$this->user.' and r.category_id=c.category_id '.
			'and d.category_id=c.category_id and d.lang_id='.$_SESSION[LANGUAGE].' '.
			'and c.category_id in ('.CATEGORY_CONTENT.','.
									CATEGORY_ADMINISTRATION.','.
									CATEGORY_TYPER_MAINTENANCE.','.
									CATEGORY_PICTUREUPLOAD_MAINTENANCE.','.
									CATEGORY_MAINTENANCE.
									') ';
			'order by c.weight ';

		$categories=$this->query($SQL);
		$this->drawCategories($categories);
		echo '</div>';
	}
}

?>
