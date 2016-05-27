/* 
Note :

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load('visualization', '1.0', {'packages':['corechart']});</script>

must be included on page!
*/

var Chart = {

	colChart : function(elem_id, title, keyTitle, valueTitle, keys, values, options) {
		var data = new google.visualization.DataTable();
		data.addColumn('string', keyTitle);
		data.addColumn('number', valueTitle);

		for (var i = 0; i < keys.length; i++) {
			data.addRow([keys[i], values[i]]);            
		}
		
		options = options || {
			'title': title,
			'titlePosition' : 'out',
			'colors' : ['#A52A2A', '#A52A2A'],
			'vAxis' : { 
				'textPosition' : 'out', 
				'gridlines' : { 'count' : 4 }, 
				"viewWindow" : { "min": 0 }, 
				"format" :'#'
			},
			'hAxis' : { 
				'slantedTextAngle' : 90, 
				'maxAlternation' : 1, 
				"viewWindow" : { "min": 0 },
				"format" :'#'
			},
			'width': "100%",
			'height': "100%",
			'legend' : { 'position' : 'none'} ,
			'backgroundColor' : 'transparent' ,
			'chartArea' : { left: 40, top: 30, width: "100%", height: "65%"}
		};

		var chart = new google.visualization.ColumnChart(document.getElementById(elem_id));
		chart.draw(data, options);
	}

};



