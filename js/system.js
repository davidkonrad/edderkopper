
//avoid internet explorer console.log error
if (!window.console) console = { log: function(){} };

var System = {

		init : function() {
			System.alertIE();
			System.styleElements();
			System.adjustFlagMenu();
			System.adjustKolofon();
		},

		alertIE : function() {
			//using support evaluates IE7/8 correctly
			//http://stackoverflow.com/questions/8890460/how-to-detect-ie7-and-ie8-using-jquery-support
			if (!$.support.leadingWhitespace) {
				var html='<div class="alert alert-danger">';
				html+='<img src="ico/information.png">';
			
				switch(System.getLang()) {
					case '1' : html+='&nbsp;&nbsp;Det ser ud til at de benytter <em><b>Internet Explorer 8</b></em> eller lavere som browser. ';
							html+='Denne side benytter moderne teknikker og metoder som ældre browsere ikke understøtter tilstrækkeligt. ';
							html+='Det er derfor nødvendigt at benytte som minimum <b>Internet Explorer 9</b>, ';
							html+='eller <b>FireFox</b>, <b>Chrome</b>, <b>Safari</b> eller <b>Opera</b> som browser';
							break;
					default : break;
				}
				$(html).prependTo("#page-body");
			}
		},

		ajaxError : function(jqXHR, textStatus, errorThrown) {
			console.log('error :'+jqXHR.responseText+' '+textStatus+' '+errorThrown);
			alert('error :'+jqXHR.responseText+' '+textStatus+' '+errorThrown);
		},

		ajaxWheel : function(element) {
			$(element).html('<img src="img/ajax-loader.gif">');
			System.wait(true);
		},

		wait : function(mode) {
			if (mode) {
				$('html, body').css("cursor", "wait");
			} else {
				$('html, body').css("cursor", "auto");
			}
		},

		styleElements : function() {
			/*
			$.each($('select').not('.no-auto-select'), function () {
				$(this).chosen();
			})
			*/
			$.each($('a.blank'), function () {
				$(this).attr('target','_blank');
			})
		},

		getLang : function() {
			var lang = $("input[name=sess_lang]").val();
			if (lang==undefined) lang=1;
			return lang;
		},

		adjustFlagMenu : function() {
			$flagMenu = $("#flag-menu");
			$logo = $("#KUlogo");
 			if ($flagMenu.length == 0 || $logo.length == 0) return;
			$flagMenu.css('left', $logo.width()-45);
			$flagMenu.css('top', $logo.offset().top-50);

			//remove additional flag-menu's added by sub pages
			var test=$('.flag-menu-cnt');
			if (test.length>1) for (var i=test.length;i>0;i--) {
				$(test[i]).remove();
			}
		},

		adjustKolofon : function() {
			var kolofon = $('.kolofon');
			if (kolofon.length<=0) return;
			kolofon.css('margin-bottom', '20px');
			kolofon.prependTo(kolofon.parent());
			kolofon.find('div').css('display','block');
			kolofon.find('.fold-arrow').remove();
		},

		adjustPageHeight : function() {
			var fieldsets = $('fieldset');
			var height = 120;
			$.each(fieldsets, function(index, fieldset) {
				$fieldset = $(fieldset);
				if ($fieldset.is(':visible')) {
					if (!$fieldset.hasClass('detail-right') &&
						!$fieldset.hasClass('no-system-height')) {
							height+=$(fieldset).height();
					}
				}
			});
			$("#page-body").css('height', height);
		}
	

}

$(document).ready(function() {
	System.init();
});

//enhance $('button') with a disable function
jQuery.fn.extend({
    disable: function(state) {
        return this.each(function() {
            this.disabled = state;
        });
    }
});
