
var EditPage = {

	save : function() {
		var mode = ($('#lang_visibility').val()=='1');
		EditPage.enableForm(true);
		var params = $('#edit-page').serialize();
		try {
			var page_html=CKEDITOR.instances.page_html.getData()
			params=params+'&html='+encodeURIComponent(page_html);
		} catch (e) {
			//alert('err');
		}
		var url='ajax/updatepage.php';
		$.ajax({
			type: 'POST', //very important due to large requests (page_html)
			data: params,
			url: url,
			cache: false,
			async: true,
			timeout : 5000,
			success: function(html) {
				EditPage.enableForm(mode);
			}
		});
	},

	changeLang : function() {
		var url='editpage?page_id='+$("#page_id").val()+'&lang_id='+$('#lang_id').find(':selected').val();
		window.location=url;
	},	

	enableForm : function(mode) {
		var elements = $("#edit-page input,select,textarea,.chzn-done");
		for (var i=0;i<elements.length;i++) {
			var id=$(elements[i]).attr('id');
			if (id!='lang_visibility' && id!='lang_id') {
				if (mode) {
					$(elements[i]).removeAttr('disabled');
				} else {
					$(elements[i]).attr('disabled','disabled');
				}
				//disable chosen selects 
				if ($(elements[i]).prop("tagName")=='SELECT') {
					$(elements[i]).trigger("liszt:updated");
				}
			}
		}
	},

	reload : function(id) {
	},

	preview : function(id) {
	},

	getText : function() {
		var html=CKEDITOR.instances.page_html.getSnapshot();
		var dom=document.createElement("DIV");
		dom.innerHTML = html;
		var plain_text = (dom.textContent || dom.innerText);
		//alert(plain_text);
/*
//following function will return plain text
function GetPlainText(FCKEditorID) {
var FEditor = FCKeditorAPI.GetInstance(FCKEditorID);
var Result = "";
if (FEditor.EditorDocument.body.textContent || FEditor.EditorDocument.body.textContent=="" ) {
//Firefox compitable browser
Result = FEditor.EditorDocument.body.textContent.trim(); 
}
else { 
//IE compitable browser 
Result = FEditor.EditorDocument.body.innerText.trim(); 
}
return Result; 
*/
	},

	convertSpecialChars : function(s) {
		s=s.replace('æ','&aelig;');
		return s;
	},

	index: function() {
		window.location='index.php';
	}

};

