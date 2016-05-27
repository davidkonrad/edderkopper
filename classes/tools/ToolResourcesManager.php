<?

class ToolResourcesManager extends ClassBase {
	private $path = 'resources';
	private $fileList = array();

	public function drawBody() {
		parent::drawBody();
		HTML::h2('<code>/resources</code>');
		HTML::br(2);
		$this->drawSessLang();
		$this->loadFiles();
		$this->drawUpload();
		$this->drawGallery();
	}

	public function drawBeforeFooter() {
	}

	protected function drawUpload() {
?>
<fieldset>
<legend><? trans(LAB_FILE_UPLOAD, true);?></legend>
<form id="upload-form" method="post" enctype="multipart/form-data" action="ajax/fileupload.php">
<input type="hidden" name="return-to" id="return-to" value="<? echo $this->currentSemanticName();?>">
<input type="file" id="upload-file" name="upload-file">
<hr class="search">
<input type="submit" id="upload-submit" value="Upload">
</form>
</fieldset>
<?
	}

	protected function drawGallery() {
		echo '<br><br>';
		echo '<fieldset><legend>Gallery</legend>';
		foreach ($this->fileList as $file) {
			echo '<div class="thumb">';

			$path=$this->path.'/'.$file;
			$test = @getimagesize($path);
			$exists = is_array($test);
			if ($exists) {
				echo '<img src="'.$path.'">';
			} else {
				echo '<img src="ico/unknown.png" style="width:110px;">';
			}
			echo '<br><span class="caption">'.$file.'</span>';
			$alt=trans(LAB_FILE_DELETE);
			echo '&nbsp;<img src="ico/remove.png" file="'.$file.'" class="remove-btn" style="height:12px;cursor:pointer;" title="'.$alt.'">';
			echo '</div>';
		}
		echo '</fieldset>';
	}

	protected function loadFiles() {
		if ($handle = opendir($this->path)) {

		    /* 
				This is the correct way to loop over the directory. 
				http://php.net/manual/en/function.readdir.php
			*/
		    while (false !== ($entry = readdir($handle))) {
				if (!in_array($entry, array('.', '..'))) {
					$this->fileList[]=$entry;
				}
    		}
		    closedir($handle);
		}
	}

	public function extraHead() {
?>
<style>
.thumb {
	height: 150px;
	border:1px solid #dadada;
	padding: 5px;
	float: left;
	margin-right: 10px;
	margin-bottom: 5px;
	background-color: #ebebeb;
	text-align: center;
}
.thumb img {
	height : 135px;
}
span.caption {
	width: 100px;
	white-space: nowrap
	text-overflow:ellipsis;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	var url='ajax/fileupload.php';
	var msg=($("input[name=sess_lang]").val()==1) ? 'Slet ' : 'Delete ';
	$('#upload-submit').attr('disabled', 'disabled');
	$('#upload-file').change(function(){
		if ($(this).val()){
			$('#upload-submit').removeAttr('disabled'); 
		} else {
			$('#upload-submit').attr('disabled',true);
		}
	});
	$(".remove-btn").click(function() {
		var filename=$(this).attr('file');
		if (confirm(msg+filename+'?')) {
			var href=url+'?delete=yes&file='+filename+'&return-to='+$("#return-to").val();
			window.location.href=href;
		}
	});
});
</script>
<?
	}
}

?>
