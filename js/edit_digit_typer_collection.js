
var Collection = {
	url : 'ajax/digit_typer_collection.php',

	ajaxError : function(jqXHR, textStatus, errorThrown) {
		console.log('error :'+jqXHR.responseText+' '+textStatus+' '+errorThrown);
		alert('error :'+jqXHR.responseText+' '+textStatus+' '+errorThrown);
	},

	redirect : function() {
		var url='';
		switch (System.getLang()) {
			case 2 : url = 'edit-virtual-type-collection'; break;
			default : url = 'rediger-virtuel-type-samling'; break;
		}
		window.location.href=url;
	},

	create : function(newCollection) {
		$.ajax({
			url : Collection.url+'?action=create&collection='+newCollection,
			success : function(html) {
				Collection.redirect();
			},
			error : Collection.ajaxError
		});
	},

	deleteCollection : function(collection) {
		$.ajax({
			url : Collection.url+'?action=delete_collection&collection='+collection,
			success : function(html) {
				Collection.redirect();
			},
			error : Collection.ajaxError
		});
	},

	rename : function(collection) {
		var newname=prompt('Omdøb '+collection+' til', '');
		if (newname=='' || newname==null) return;
		$.ajax({
			url : Collection.url+'?action=rename&collection='+collection+'&new='+newname,
			success : function(html) {
				Collection.getList(newname);
				for (var i=0;i<$("#collection option").length;i++) {
					if ($("#collection option")[i].value==collection) {
						$("#collection option")[i].value=newname;
						$("#collection option")[i].text=newname;
					}
				}
			},
			error : Collection.ajaxError
		});
	},

	addType : function(collection, catalognumber) {
		$.ajax({
			url : Collection.url+'?action=add&collection='+collection+'&catalognumber='+catalognumber,
			success : function(html) {
				Collection.getList(collection);
			},
			error : Collection.ajaxError
		});
	},

	deleteType : function(collection, catalognumber) {
		$.ajax({
			url : Collection.url+'?action=delete&collection='+collection+'&catalognumber='+catalognumber,
			success : function(html) {
				Collection.getList(collection);
			},
			error : Collection.ajaxError
		});
	},
		
	getList : function(collection) {
		$.ajax({
			url : Collection.url+'?action=list&collection='+collection,
			success : function(html) {
				//alert(html);
				$("#collection-cnt").html(html);
				$("#collection-add").click(function() {
					var collection=$("#collection-add").attr('collection');
					var catalognumber=prompt('Tilføj type / catalognumber til "'+collection+'"','');
					if (catalognumber!='' && catalognumber!=null) {
						Collection.addType(collection, catalognumber);
					}
				});
				$(".img-delete-catalognumber").click(function() {
					var catalognumber=$(this).attr('catalognumber');
					if (confirm('Slet '+catalognumber+'?')) {
						Collection.deleteType(collection, catalognumber);
					}
				});
				$("#collection-rename").click(function() {
					Collection.rename(collection);	
				});
				$("#collection-delete").click(function() {
					if (confirm('Slet '+collection+'?')) {
						Collection.deleteCollection(collection);
					}
				});

			},
			error : Collection.ajaxError
		});
	},

		
};
