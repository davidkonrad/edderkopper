<script type="text/javascript" src="js/search.js"></script>
<script type="text/javascript" language="javascript" src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script type="text/javascript">
$(document).ready(function() {
	var url='ajax/pageload.php?s=<? echo $row['semantic_name'];?>';
	$.ajax({
		url: url,
		cache: false,
		async: true,
		timeout : 5000,
		success: function(html) {
			var page=$(html).find('fieldset').first().contents();
			//src stackoverflow.com/questions/2699320/jquery-script-tags-in-the-html-are-parsed-out-by-jquery-and-not-executed
			var dom = $(html);
		        dom.filter('script').each(function(){
				$.globalEval(this.text || this.textContent || this.innerHTML || '');
		        });
			$("#<? echo 'class'.$row['page_id'];?>").html(dom.find('#something').html());

			$("#<? echo 'class'.$row['page_id'];?>").append(page);
			//$("#<? echo 'class'.$row['page_id'];?>").append(script);
			$("#<? echo 'class'.$row['page_id'];?>").find('legend').remove();
			$("input:button").button();
			
			/*$("#search-btn").removeAttr('onclick');
			$("#search-btn").click(function(event) {
				var param=$(page).find('form').serialize();
				alert(param);
				event.preventDefault();
				//alert('ok');
			});
			*/
		}
	});
});
</script>
<?
				}

