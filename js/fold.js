(function($){
    $.fn.moveTo = function(selector){
        return this.each(function(){
            var cl = $(this).clone();
            $(cl).appendTo(selector);
            $(this).remove();
        });
    }
})(jQuery);

function fold(e) {
	e.preventDefault(); 
	e.stopPropagation()
	var id=$(this).attr('page_id');
	if ($(this).attr('search')=='yes') {
		if ($("#search"+id).is(":visible")) {
			$("#df"+id).toggle();
			$("#search"+id).toggle();
			$("#arr"+id).html('&#9660;');
			$("#arr"+id).moveTo("#df"+id);
			$("#arr"+id).bind('click', fold);
			$("#arr"+id).css('top','-9px');
			$("#arr"+id).css('left','-31px');
			//
			$('#print'+id).remove();
		} else {
			$("#search"+id).ready(function() {	
				var caption=$("#df"+id).find('legend').text();
				$("#search"+id).find('legend').text(caption);

				var h=$("#search"+id).height();
				h='-'+parseInt(h-3)+'px';
				$("#arr"+id).css('top',h);
				$("#arr"+id).css('left','-12px');
				$("#arr"+id).bind('click', fold);
				//
				var f="search"+id;
				//wait to it has opened
				setTimeout(function() {
					addPrint("#"+f, id, true);
				}, 300);
			});
			$("#df"+id).toggle();
			$("#search"+id).toggle();
			$("#arr"+id).html('&#9650;');
			$("#arr"+id).moveTo("#search"+id);
			$("#arr"+id).bind('click', fold);
		}
	} else {
		if ($("#cnt"+id).is(":visible")) {
			$("#cnt"+id).hide();
			$("#arr"+id).html('&#9660;');
			//
			$('#print'+id).remove();
		} else {
			$("#cnt"+id).show();
			$("#arr"+id).html('&#9650;');
			//
			addPrint("#f"+id, id, false);
		}
	}
	System.adjustPageHeight();
}

function getPrintTop(elem) {
	var h;
	if ($.browser.mozilla) {
		h=$(elem).height()-31;
	} else if ($.browser.webkit) {
		h=$(elem).height()-30;
	} else if ($.browser.opera) {
		h=$(elem).height()-31;
	} else if ($.browser.msie)
		h=$(elem).height()-30;
	else {
		h=$(elem).height()+4;
	}
	return h;
}

function addPrint(elem, id, issearch) {
	var pid='print'+id;
	if (!issearch) {
		var h=$(elem).height()-8;
		var l=$(elem).width()-11;
		var print=$('<span class="print-box" id="'+pid+'" title="Udskriv"><img src="ico/printer.png" alt="print" class="print-box-image" onclick="doPrint(&quot;'+elem+'&quot;);"></span>').appendTo(elem);
	} else {
		var l;
		if ($.browser.mozilla) {
			l=$(elem).width()-41;			
		} else if ($.browser.webkit) {
			l=$(elem).width()-41;			
		} else if ($.browser.opera) {
			l=$(elem).width()-41;			
		} else {
			l=$(elem).width()-41;
		}
		var h=getPrintTop(elem);
		var print=$('<span class="print-box" id="'+pid+'"><img src="ico/printer.png" alt="print" class="print-box-image" onclick="doPrint(&quot;'+elem+'&quot;);"></span>').appendTo(elem);

		$(elem).resize(function(e) {
			var h=getPrintTop(elem);
			$('#'+pid).css('top',-parseInt(h)+'px');
		});
	}
	$('#'+pid).css('top',-parseInt(h)+'px');
	$('#'+pid).css('left',l+'px');
}	

function doPrint(elem) {
	var w=open('','print');
	var html=$(elem).clone();
	$(html).find('input').remove();
	$(html).find('select').remove();
	$(html).find('.print-box').remove();
	$(html).find('.fold-arrow').remove();
	w.document.body.innerHTML="";
	w.document.write('<style>body{width:670px;}header{width:100%;height:30px;background-color:#ebebeb;font-weight:bold;}</style>');
	w.document.write($(html).html());
	if (window.print) {
		w.print();
	}
}

$(document).ready(function() {	
	$('.fold-arrow').bind('click', fold);
});
