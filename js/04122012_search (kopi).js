
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
}
	

var Search = {
	/*
	caption : '', 	//title of the searchclass, ex "Our bad ass collection"
	caption_results : '',//search result description, ex "results"
	form_id : '#form', //id of the form, ex #search-form
	form_cnt : '',	//id of the form container, if any, ex #search-cnt
	result_id : '#', //id of search result container, ex #search-result
	headline_id : '#', //id of search result legend, if any, ex #result-headline
	submit_load : 'ajax/',
	result_table : '#result-table', //only change if a different table is used
	mandatory_criterias : true,
	markers: null, //polygon on a google map, array of Marker
	*/
	items : [],
	init : function () {
		$(this.form_id+' input').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if (code == 13) { 
				Search.submit();
			}
		});
		$('.datoInterval').bind('keydown', function(e) {
			//Search.datoKeydown(e);
			SNMDate.keydown(e);
		});
		$('.datoInterval').blur(function() {
			//Search.enableDatoIntervals();
			SNMDate.enableIntervals();
		});
		//Search.enableDatoIntervals();
		SNMDate.enableIntervals();
		$(Search.form_id+' input').bind('propertychange keyup input paste', Search.mandatory);
		$(Search.form_id+' select').bind('change', Search.mandatory);
		this.mandatory();
		this.setCaption();
		//focus first input
		$(this.form_id+" :input:visible:enabled:first").focus();
	},
	reset : function() {
		window.location.reload(false);
		return;
		//
		$(Search.form_id+' input[type=text]').val('');
		$(Search.form_id+' select').attr('selectedIndex',0);
		$(Search.form_id+" .chzn-select").val('').trigger("liszt:updated");
	},
	addItem(searchItem) {
		this.items[searchItem.form_id]=searchItem;
	},
	setAjaxWheel : function() {
		$(this.result_id).append('<img src="img/ajax-loader.gif"/>');
	},
	clearResult : function() {
		$(this.result_id).find('div').remove();
		$(this.result_id).find('img').remove();
		$(this.result_id).find('table').remove();
		$(this.result_id).find('input').remove();
		$(this.result_id).find('style').remove();
		this.setCaption();
	},
	wait : function(mode) {
		if (mode) {
			$('html, body').css("cursor", "wait");
		} else {
			$('html, body').css("cursor", "auto");
		}
	},
	getPolygonParams : function() {
		var params='';
		if (Search.markers!=null) {
			for (var i=0;i<Search.markers.length;i++) {
				var test=Search.markers[i].position;
				var LL=Search.markers[i].getPosition().toUrlValue(4);
				params+='&LL'+i.toString()+'='+LL.toString();
			}
		}
		return params;
	},
	getParams : function() {
		var params='';
		$(Search.form_id+ ' input[type=text]').each(function (i) {
			if (params!='') params+='&';
			params+=$(this).attr('name');
			params+='='+encodeURIComponent($(this).val());
		});
		return params;
	},
	submit : function() {
		if (!$(this.result_id).is(':visible')) { 
			$(this.result_id).show();
		}
		this.setAjaxWheel();
		Search.wait(true);

		var params = $(Search.form_id).serialize();

		//hack, will be refactorated when search.js os
		if ($("#hidden-kommune").val()=='') {
			params+=Search.getPolygonParams();
		}
		//alert(params);
		//return;
	
		$(this.form_cnt).hide();
		$(this.form_id).hide();
		$(this.headline_id).html(this.caption_result);
		var url=this.submit_load+'?'+params;
		//alert(url);
		$.ajax({
			url: url,
			cache: true,
			async: true,
			timeout : 5000,
			success: function(html) {
				Search.clearResult();
				$(Search.result_id).append(html);
				$("input:button").button();
				Search.wait(false);
				//update legend / caption, assumes (!) table is #result-table
				var caption=Search.caption+' - '+($(Search.result_table+" tr:gt(0)").length-1)+' '+Search.caption_results;
				$(Search.headline_id).html(caption);
			}
		});
	},
	setCaption : function() {
		$(this.headline_id).html(this.caption);
	},
	back : function() {
		$(Search.form_cnt).show();
		$(Search.form_id).show();
		$(Search.result_id).hide();
		Search.clearResult();
		Search.setCaption();
	},
	/*
	enableDatoInterval : function(from) {
		if ($("#"+from).attr('value')>0) {
			$("#to-"+from).removeAttr('disabled');
		} else {
			$("#to-"+from).attr('disabled','disabled');
		}
	},
	*/
	mandatory : function() {
		if (!Search.mandatory_criterias) {
			$("#search-button").removeAttr("disabled");
			//fix jquery UI bug
			$("#search-button").removeClass('ui-state-disabled');
			//alert('ok');
			return false;
		}
		var data=false;
		$.each($(Search.form_id+' input:text'), function (index, element) {
			if ($(element).val()!='') { data=true; }
		});
		$.each($(Search.form_id+' select'), function (index, element) {
			if ($(element).prop('selectedIndex')>0) { data=true; }
		});

		if (data) {
			$("#search-button").removeAttr("disabled");
			$("#search-button").removeClass('ui-state-disabled');
		} else {
			$("#search-button").attr("disabled", "disabled");
			$("#search-button").addClass('ui-state-disabled');
		}
	}
	/*,
	enableDatoIntervals : function() {
		Search.enableDatoInterval("year");
		Search.enableDatoInterval("month");
		Search.enableDatoInterval("day");
	}
	*/
	/*,
	datoKeydown : function(e) {
		//from http://stackoverflow.com/questions/995183/how-to-allow-only-numeric-0-9-in-html-inputbox-using-jquery
		//backspace, delete, tab, escape, and enter
		if (e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 27 || e.keyCode == 13 || 
		//ctrl+A
		(e.keyCode == 65 && e.ctrlKey === true) || 
		//home, end, left, right
		(e.keyCode >= 35 && e.keyCode <= 39)) {
			//let it happen, don't do anything
			Search.enableDatoIntervals();
			return;
		} else {
			//ensure that it is a number and stop the keypress
			if (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105 )) {
		                e.preventDefault(); 
			}   
		}
	}
	*/
};

$(document).ready(function() {
	Search.init();
});

