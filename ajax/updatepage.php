<?

include('../common/Db.php');

class UpdatePage extends Db {

	public function __construct() {
		parent::__construct();

		$kolofon=(isset($_POST['kolofon'])) ? '1' : '0';
		//update zn_page	
		$SQL='update zn_page set '.
			'category_id='.$this->q($_POST['category_id']).
			'weight='.$this->q($_POST['weight']).
			'standalone='.$this->q($_POST['standalone']).
			'kolofon='.$this->q($kolofon).
			'visible='.$this->q($_POST['visible']).
			'alternative_template='.$this->q($_POST['alternative_template'], false).' '.
			'where page_id='.$_POST['page_id'];
		$this->exec($SQL);
		
		//update zn_page_content
		$SQL='update zn_page_content set '.
			'anchor_caption='.$this->q($_POST['anchor_caption']).
			'anchor_title='.$this->q($_POST['anchor_title']).
			'semantic_name='.$this->q($_POST['semantic_name']).
			'meta_desc='.$this->q($_POST['meta_desc']).
			'lang_visibility='.$this->q($_POST['lang_visibility']).
			'title='.$this->q($_POST['title'], false).' '.
			'where page_id='.$_POST['page_id'].' and lang_id='.$_POST['lang_id'];

		$this->fileDebug($SQL);
		$this->exec($SQL);

		//update if class
		if (isset($_POST['class_name'])) {
			$SQL='update zn_page_class '.
				'set class_name='.$this->q($_POST['class_name'], false).' '.
				'where page_id='.$_POST['page_id'];
			$this->exec($SQL);
		}

		//update if static
		if (isset($_POST['html'])) {
			$SQL='update zn_page_static '.
				//CKEditor adds extra \ in img quotes
				//'set page_html='.stripslashes($this->q($_POST['html'], false)).' '.
				'set page_html='.$this->q($_POST['html'], false).' '.
				'where page_id='.$_POST['page_id'].' and lang_id='.$_POST['lang_id'];
			$this->exec($SQL);
		}

		//update if link
		if (isset($_POST['linktype_id'])) {
			$SQL='update zn_page_link set '.
				'linktype_id='.$this->q($_POST['linktype_id']).
				'blank='.$this->q($_POST['blank']).
				'url='.$this->q($_POST['url'], false).' '.
				'where page_id='.$_POST['page_id'];
			$this->exec($SQL);
			
			//must check link_desc exists, since link_desc is a new feature
			$SQL='select * from zn_page_link_desc where page_id='.$_POST['page_id'].' and lang_id='.$_POST['lang_id'];
			//$test=$this->query($SQL);
			if ($this->hasData($SQL)) {
				$SQL='update zn_page_link_desc '.
					'set link_desc='.$this->q($_POST['link_desc'], false).' '.
					'where page_id='.$_POST['page_id'].' and lang_id='.$_POST['lang_id'];
				echo $SQL;
				$this->exec($SQL);
			} else {
				$SQL='insert into zn_page_link_desc (page_id, lang_id, link_desc) values('.
					$this->q($_POST['page_id']).
					$this->q($_POST['lang_id']).
					$this->q($_POST['link_desc'], false).')';
				echo $SQL;
				$this->exec($SQL);
			}
					
		}

	}

}

$update = new UpdatePage();

?>
