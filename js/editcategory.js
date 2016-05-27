
var EditCategory = {

	/* 15.08.2013 
	   param id added
       if set it overrules this.getId()
	*/
	selectCategory : function(id) {
		if (id==null) id=$("#lang_id").val();
		var url='ajax/editcategory.php';
		url+='?action=select';
		url+='&category_id='+this.getId();
		url+='&lang_id='+id;
		url+='&lang='+$("#lang").val();
		$('#edit-category').load(url);
	},

	index : function() {
		window.location='index.php';
	},

	getId : function() {
		return $('#category_id').find(':selected').val();
	},

	changeLang : function() {
		var url='ajax/editcategory.php?action=select';
		url+='&category_id='+this.getId();
		url+='&lang_id='+$("#lang_id").val();
		url+='&lang='+$("#lang").val();
		$('#edit-category').load(url);
	},

	createCategory : function() {
		var url='ajax/editcategory.php?action=create&lang_id='+$("#lang_id").val();
		$('#edit-category').load(url);
	},

	saveCategory : function() {
		var desc=CKEDITOR.instances.category_desc.getData()
		var url='ajax/editcategory.php?action=update';
		url+='&category_id='+this.getId();
		url+='&lang='+$("#lang").val();
		url+='&template_class='+escape($("#template_class").val());
		url+='&caption='+encodeURIComponent($("#caption").val());
		url+='&weight='+escape($("#weight").val());
		url+='&lang_id='+escape($("#lang_id").val());
		url+='&visible='+escape($("#visible").val());
		url+='&semantic_name='+escape($("#semantic_name").val());
		url+='&category_desc='+encodeURIComponent(desc);
		$('#edit-category').load(url);
	}
};

