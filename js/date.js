
var DateEval = {
	show_from : 1800,
	show_to : new Date().getFullYear(),
	limit_from : 1800,
	limit_to : new Date().getFullYear(),
	current_value : null,

	keydown : function(e) {
		//tab
		if (e.keyCode == 9) {
			DateEval.enableIntervals();
			return;
		}
		//backspace, delete, escape, and enter
		if (e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 27 || e.keyCode == 13 || 
			//ctrl+A
			(e.keyCode == 65 && e.ctrlKey === true) || 
			//home, end, left, right
			(e.keyCode >= 35 && e.keyCode <= 39)) {
				//let it happen, don't do anything
				//DateEval.enableIntervals();
				return;
		} else {
			//ensure that it is a number and stop the keypress
			if (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105 )) {
				e.preventDefault(); 
				return;
			}
		}
		var id='#'+e.currentTarget.id;
		var val=$(id).val();
		if (val.length>=4) {
			e.preventDefault(); 
		}
	},

	enableIntervals : function() {
		this.enableYear("year");
		this.enableMonth("month");
		this.enableDay("day");
		//this.enable("month");
		//this.enable("day");
	},

	enableYear : function(from) {
		//alert('ok');
		var val=parseInt($("#"+from).val());
		if ((val<this.limit_from) || (val>this.limit_to)) {
			alert('ok');
			//it is legal to leave the field blank
			if ($("#"+from).val()!='') {
				$("#"+from).val(this.limit_from);
				return;
			}
		}

		if ($("#to-"+from).val()>this.limit_to) {
			$("#to-"+from).val(this.limit_to);
			return;
		}

		if (parseInt($("#"+from).attr('value'))>=this.limit_from) {
			$("#to-"+from).removeAttr('disabled');
		} else {
			$("#to-"+from).attr('disabled','disabled');
		}
	},	

	enableMonth : function(from) {
		var val=$("#"+from).val();
		if ((val<1) || (val>12)) {
			//it is legal to leave the field blank
			if ($("#"+from).val()!='') {
				$("#"+from).val('1');
				return;
			}
		}

		if ($("#to-"+from).val()>12) {
			$("#to-"+from).val('12');
			return;
		}

		if ($("#"+from).attr('value')>=1) {
			$("#to-"+from).removeAttr('disabled');
		} else {
			$("#to-"+from).attr('disabled','disabled');
		}
	},	

	enableDay : function(from) {
		var val=$("#"+from).val();
		if ((val<1) || (val>31)) {
			//it is legal to leave the field blank
			if ($("#"+from).val()!='') {
				$("#"+from).val('1');
				return;
			}
		}

		if ($("#to-"+from).val()>31) {
			$("#to-"+from).val('31');
			return;
		}

		if ($("#"+from).attr('value')>=1) {
			$("#to-"+from).removeAttr('disabled');
		} else {
			$("#to-"+from).attr('disabled','disabled');
		}
	},	

	enable : function(from) {
		if ($("#"+from).attr('value')>0) {
			$("#to-"+from).removeAttr('disabled');
		} else {
			$("#to-"+from).attr('disabled','disabled');
		}
	},

	getCurrentDate : function(separator) {
		if (!separator) separator='/';
		var date=new Date();
		var month=date.getMonth()+1;
		date=date.getDay()+separator+month+separator+date.getFullYear();
		return date;
	},

	getCurrentTime : function() {
		var date = new Date();
		var hours = date.getHours();
		if (hours.toString().length==1) hours='0'+hours;
		var minutes = date.getMinutes();
		if (minutes.toString().length==1) minutes='0'+minutes;
		var seconds = date.getSeconds();
		if (seconds.toString().length==1) seconds='0'+seconds;
		//return time in 10:30:23 format
		return hours + ':' + minutes + ':' + seconds;
	}

};
