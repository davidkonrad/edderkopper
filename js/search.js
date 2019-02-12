
function SearchItem(form) {
	this.caption = '';	//title of the searchclass, ex "Our bad ass collection"
	this.caption_results = '';	//search result description, ex "results"
	this.form_id = form;  //id of the form, ex #search-form
	this.form_cnt = '';	//id of the form container, if any, ex #search-cnt
	this.result_id = '#'; //id of search result container, ex #search-result
	this.headline_id = '#'; //id of search result legend, if any, ex #result-headline
	this.submit_load = 'ajax/';
	this.result_table = '#result-table'; //only change if a different table is used
	this.mandatory_criterias = true;
	this.markers = null; //polygon on a google map, array of Marker
	//autocompletes
	this.lookupFields = [];
	this.lookupValues = [];
	//date/year
	this.date_limit_from = '';
	this.date_limit_to = new Date().getFullYear();
}

var Search = {
	items : [],
	current_search : null, //SearchItem object

	init : function (item) {
		this.current_search=item;
		$(item.form_id+' input').on('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if (code == 13) { 
				Search.submit(item.form_id);
			}
		});

		//date evaluation
		if ($(item.form_id).find('.datoInterval').length>0) {
			DateEval.limit_from=item.date_limit_from;
			DateEval.limit_to=item.date_limit_to;

			$('.datoInterval').bind('keydown', function(e) {
				DateEval.keydown(e);
			});

			$('.datoInterval').blur(function() {
				DateEval.enableIntervals();
			});

			DateEval.enableIntervals();
		}

		//mandatory fields
		if (item.mandatory_criterias) {
			$(item.form_id+' input').on('propertychange keyup input paste change', function(e) {
				Search.mandatory(item.form_id);
			});

			$(item.form_id+' select').on('change', function(e) {
				Search.mandatory(item.form_id);
			});

			Search.mandatory(item.form_id);
		}

		this.setCaption();

		//focus first input
		$(item.form_id+" :input:visible:enabled:first").focus();
	},

	reset : function() {
		window.location.reload(false);
		return;
		//
		$(Search.form_id+' input[type=text]').val('');
		$(Search.form_id+' select').attr('selectedIndex',0);
		$(Search.form_id+" .chzn-select").val('').trigger("liszt:updated");
	},

	addItem : function(searchItem) {
		this.items[searchItem.form_id]=searchItem;
	},

	setAjaxWheel : function() {
		$(this.current_search.result_id).append('<img src="img/ajax-loader.gif"/>');
	},

	clearResult : function() {
		$(this.current_search.result_id).find('div').remove();
		$(this.current_search.result_id).find('img').remove();
		$(this.current_search.result_id).find('table').remove();
		$(this.current_search.result_id).find('input').remove();
		$(this.current_search.result_id).find('style').remove();
		this.setCaption();
	},

	wait : function(mode) {
		if (mode) {
			$('html, body').css("cursor", "wait");
		} else {
			$('html, body').css("cursor", "auto");
		}
	},

	setLookupValue : function(field, value) {
		this.current_search.lookupValues[field]=value;
	},

	getLookupValues : function() {
		var str='', field;
		for (var i=0;i<this.current_search.lookupFields.length;i++) {
			field=this.current_search.lookupFields[i];
			if (str!='') str+='&';
			value=(this.current_search.lookupValues[field]!=undefined) ? this.current_search.lookupValues[field] : '';
			str+=field+'='+value;
		}
		return str;
	},
	
	getPolygonParams : function(item) {
		var params='';
		if (item.markers!=null) {
			for (var i=0;i<item.markers.length;i++) {
				var test=item.markers[i].position;
				var LL=item.markers[i].getPosition().toUrlValue(4);
				params+='&LL'+i.toString()+'='+LL.toString();
			}
		}
		return params;
	},

	getParams : function(item) {
		//form fields
		var params='';
		$(item.form_id+ ' input[type=text], input[type=hidden]').each(function(i) {
			if (params!='') params+='&';
			params+=$(this).attr('name');
			params+='='+encodeURIComponent($(this).val());
		});

		//field values (autocompletes)
		for (var i=0;i<this.current_search.lookupFields.length;i++) {
			field=this.current_search.lookupFields[i];
			if (params!='') params+='&';
			value=(this.current_search.lookupValues[field]!=undefined) ? this.current_search.lookupValues[field] : '';
			params+=field+'='+value;
		}

		//selects
		$(item.form_id+' select').each(function(i) {
			if ($(this).attr('name')) {
				var sel=$(this).val();
				if (sel!='') {
					if (params!='') params+='&';
					params+=$(this).attr('name')+'='+sel;
				}
			}
		});

		//checkboxes
		$(item.form_id+ ' input[type=checkbox]').each(function() {
			if ($(this).is(':checked')) {
				if (params!='') params+='&';
				params+=$(this).attr('name');
				params+='='+encodeURIComponent($(this).val());
			}
		});

		return params;
	},

	prepareSubmit : function(item) {
		this.current_search=item;

		if (!$(item.result_id).is(':visible')) { 
			$(item.result_id).show();
		}
		this.setAjaxWheel();
		Search.wait(true);

		$(item.form_cnt).hide();
		$(item.form_id).hide();
		$(item.headline_id).html(item.caption_result);
	},

	performSearch : function(url, item) {
		$.ajax({
			url: url,
			cache: true,
			async: true,
			timeout : 5000,
			success: function(html) {
				Search.clearResult();
				$(item.result_id).append(html);
				Search.wait(false);
				var len=$(item.result_table+" tr:gt(0)").length;
				if (len==1) {
					if ($(item.result_table+" tr:gt(0)").find('td').hasClass('dataTables_empty')) {
						len=0;
					}
				}
				var caption=item.caption+' - '+len+' '+item.caption_results;
				$(item.headline_id).html(caption);
				System.adjustPageHeight();

				//
				$("html, body").animate({ scrollTop: 0 }, "fast");
			}
		});
	},

	submit : function(form) {
		var item=this.items[form];
		this.prepareSubmit(item);
		var params = Search.getParams(item);

		//hack, will be refactorized when search.js os
		if ($("#hidden-kommune").val()=='') {
			params+=Search.getPolygonParams(item);
		}
	
		var url=item.submit_load+'?'+params;
		this.performSearch(url, item);
	},

	autoSubmit : function(form, fields, values) {
		var item=this.items[form];
		this.prepareSubmit(item);
		var params = '';
		for (var i=0;i<fields.length;i++) {
			if (params!='') params+='&';
			params+=fields[i]+'='+values[i];
		}
		var url=item.submit_load+'?auto=yes&'+params;
		this.performSearch(url, item);
	},

	setCaption : function() {
		$(this.current_search.headline_id).html(this.current_search.caption);
	},

	back : function() {
		$(this.current_search.form_cnt).show();
		$(this.current_search.form_id).show();
		$(this.current_search.result_id).hide();
		Search.clearResult();
		Search.setCaption();
		System.adjustPageHeight();
	},

	mandatory : function(form_id) {
		var data=false;
		$.each($(form_id+' input:text'), function (index, element) {
			if ($(element).val()!='') { data=true; }
		});
		$.each($(form_id+' select'), function (index, element) {
			if ($(element).prop('selectedIndex')>0) { data=true; }
		});

		if (data) {
			$(form_id+" #search-button").removeAttr("disabled");
			$(form_id+" #search-button").removeClass('ui-state-disabled');
		} else {
			$(form_id+" #search-button").attr("disabled", "disabled");
			$(form_id+" #search-button").addClass('ui-state-disabled');
		}
	}
};


