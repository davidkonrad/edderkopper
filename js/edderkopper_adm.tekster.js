$(document).ready(function() {

	var page_id = 22; //introduktion
	var lang = 1; //dansk 
	$('#tekst-page-id').val(page_id);
	$('#tekst-lang-id').val(lang);

	$('body').on('click', '.tekst-lang', function() {
		$('.tekst-lang').attr('style', '');
		$(this).css('border', '1px solid green');
		lang = $(this).attr('lang');
		$('#tekst-lang-id').val(lang);
		loadPage();
	});

	$('body').on('change', '#tekst-page', function() {
		page_id = $(this).find('option:selected').val();
		$('#tekst-page-id').val(page_id);
		loadPage();
	});

	$("#tekst-save").on('click', function() {
		CKEDITOR.instances['tekst-editor'].updateElement();
		var params=$('#tekst-form').serialize();
		params+='&action=savePage';
		$.ajax({
			url : 'ajax/edderkopper/actions.php?'+params,
			success : function(response) {
				$('#tekst-messages').text(response).show().fadeOut(5000)
			}
		});
	})

	if (CKEDITOR.instances['tekst-editor'] == undefined) {
		CKEDITOR.replace('tekst-editor', { width:"750px", height:"200px", toolbar:'edderkopper' });
	}

	function loadPage() {
		$.ajax({
			url : 'ajax/edderkopper/actions.php?action=getPage',
			data : {
				page_id: page_id,
				lang: lang
			},
			success: function(response) {
				response = JSON.parse(response);
				$('[name="tekst-title"]').val(response.title);
				$('[name="tekst-meta"]').val(response.meta_desc);
				$('[name="tekst-caption"]').val(response.anchor_caption);
				CKEDITOR.instances['tekst-editor'].setData(response.page_html);
			}
		})
	}
	loadPage()



});
	
