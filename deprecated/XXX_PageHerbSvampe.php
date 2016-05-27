<?

class PageHerbSvampe extends TemplateSimple {

	protected function extraHead() {
?>
<script type="text/javascript" language="javascript" src="DataTables-1.9.1/media/js/jquery.dataTables.js"></script> 
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_table.css" type="text/css" media="screen" />
<link rel="stylesheet" href="DataTables-1.9.1/media/css/demo_page.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/search.js"></script>
<?
	}

	protected function getMetaDesc() {
?>
S&oslash;g i statens naturhistoriske museums svampeherbarie. Svampedatabasen rummer p.t. ca. 57.500 poster fra 27 lande. 
De fleste er nyere indsamlinger efter 1990, og alt nyt materiale fra Danmark l&aelig;gges ind i basen. 
<?
	}

	protected function drawBeforeFooter() {
?>
<script type="text/javascript">
function showAbout() {
	$("#svampedatabase").hide();
	$("#about").show();
}
function hideAbout() {
	$("#svampedatabase").show();
	$("#about").hide();
}
$(document).ready(function() {
	Search.caption='<? trans(DB_LINK_SVAMPEHERBARIUM, true);?>';
	Search.caption_results='<? trans(LAB_SEARCH_RESULTS, true);?>';
	Search.form_id="#svampedatabase";
	Search.submit_load="ajax/herbsvampe.php";
	Search.init();
});
</script>
<?
	}

	protected function drawBody() {
?>
<fieldset>
<legend id="content-headline">S&oslash;g i Svampe-Databasen</legend>
<form name="svampedatabase" id="svampedatabase" method="post" action="">
<? $this->drawSessLang(); ?>
<table>
	<tr>
		<td><label for="DKNavn"><? trans(LAB_DANISH_NAME, true);?></label></td>
		<td><input type="text" name="DKNavn" id="DKNavn"/></td>
		<!--<td><span class="formComment">Danish name</span></td>-->
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="vertical-align:top;"><small>OBS! V&aelig;r opm&aelig;rksom p&aring; brugen af bindestreg</small></td>
		<!--<td>&nbsp;</td>-->
	</tr>
	<tr>
		<td><label for="LatinNavn"><? trans(LAB_SCIENTIFIC_NAME, true);?></label></td>
		<td><input type="text" name="LatinNavn" id="LatinNavn"/></td>
		<!--<td><span class="formComment">Latin name</span></td>-->
	</tr>
	<tr>
		<td><label for="gruppe"><? trans(LAB_TAXON_GROUP, true);?></label></td>
		<td><? $this->taxonSelect('gruppe','gruppe'); ?></td>
		<!--<td><span class="formComment">Group</span></td>-->
	</tr>
	<tr>
		<td><label for="land"><? trans(LAB_COUNTRY, true);?></label></td>
		<td><? HTML::countrySelect('land','land'); ?></td>
		<!--<td><span class="formComment">Country</span></td>-->
	</tr>
	<tr>
		<td><label for="locality"><? trans(LAB_LOCALITY, true);?></label></td>
		<td><input type="text" name="locality" id="locality"/></td>
		<!--<td><span class="formComment">Locality</span></td>-->
	</tr>
	<tr>
		<td><label for="dato"><? trans(LAB_DATE, true);?><small>(DD-MM-&aring;&aring;&aring;&aring;)</small></label></td>
		<td><input type="text" name="dato" id="dato" style="width:8em;"/><small style="line-height:2.1em;">&nbsp;(* = ukendt/wildcard)</small></td>
		<!--<td><span class="formComment">Date <small>(DD-MM-YYYY)</small></span></td>-->
	</tr>

	<tr>		
		<td><label for="dato-year"><? trans(LAB_DATE_YEAR, true);?></label></td>
		<td>
			<small class="datoInterval" style="margin-left:0px;">&aring;r</small></label>
				<input type="text" class="datoInterval" max="4" name="year" style="width:40px;" id="year"/>
			<small class="datoInterval"><? trans(LAB_DATE_MONTH, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="month" style="width:40px;" id="month"/>
			<small class="datoInterval"><? trans(LAB_DATE_DAY, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="day" style="width:40px;" id="day"/>
		</td>
		<!--<td>&nbsp;</td>-->
	</tr>

	<tr>		
		<td><label for="dato-to-year"><? trans(LAB_DATE_TO, true);?></label></td>
		<td>
			<small class="datoInterval" style="margin-left:0px;">&aring;r</small></label>
				<input type="text" class="datoInterval" max="4" name="to-year" style="width:40px;" id="to-year"/>
			<small class="datoInterval"><? trans(LAB_DATE_MONTH, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="to-month" style="width:40px;" id="to-month"/>
			<small class="datoInterval"><? trans(LAB_DATE_DAY, true);?></small></label>
				<input type="text" class="datoInterval" max="2" name="to-day" style="width:40px;" id="to-day"/>
		</td>
		<!--<td>&nbsp;</td>-->
	</tr>

	<tr>
		<td><label for="legit"><? trans(LAB_COLLECTOR, true);?></label></td>
		<td><input type="text" name="legit" id="legit"/></td>
		<!--<td><span class="formComment">Collector</span></td>-->
	</tr>
	<tr>
		<td><label for="ident"><? trans(LAB_DETERMINATOR, true);?></label></td>
		<td><input type="text" name="ident" id="ident"/></td>
		<!--<td><span class="formComment">Identification by</span></td>-->
	</tr>
	<tr>
		<td><label for="habitat"><? trans(LAB_HABITAT, true);?></label></td>
		<td><input type="text" name="habitat" id="habitat"/></td>
		<!--<td><span class="formComment">Habitat</span></td>-->
	</tr>
	<tr>
		<td colspan="4"><small>
		Sl&aelig;gtsnavn, artsnavn og/eller autor kan bruges til s&oslash;gninger. Der kan ogs&aring; s&oslash;ges p&aring; dele af ord. <br/>
		Habitat-feltet er kun begr&aelig;nset anvendeligt til s&oslash;gninger, da ikke alle kollektioner har lige<br/>omfattende oplysninger her.
		</small></td>
	</tr>
	<tr><td colspan="2"><hr class="search"></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="button" value="<? trans(LAB_SEARCH, true);?>" onclick="Search.submit();"/>&nbsp;&nbsp;
			<input type="button" value="<? trans(LAB_RESET, true);?>" onclick="Search.reset();"/>&nbsp;&nbsp;
			<!--<input type="button" value="<? trans(DB_ABOUT_SVAMPEHERBARIUM, true);?>" onclick="showAbout();"/>-->
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
</form>
<div id="search-result" style="float:left;text-align:left;"></div>
<div id="about" style="display:none;" lang="dk">
<input type="button" value="&lt; Tilbage" onclick="hideAbout();"/>
<h2>Om Svampeherbariet / databasen</h2>
<p>Svampedatabasen rummer p.t. ca. 57.500 poster fra svampeherbariet. De fleste er nyere indsamlinger efter 1990, og alt nyt materiale fra Danmark l&aelig;gges ind i basen. I forbindelse med udl&aring;n, forskningsprojekter og lignende form&aring;l er ogs&aring; &aelig;ldre indsamlinger blevet indtastede, men der er ikke ressourcer til en generel indtastning af hele herbariet.</p>
<p>Hver post indeholder svampens danske navn, det videnskabelige (latinske) navn, oprindelsesland, lokalitet, indsamleren og bestemmeren, hvis det ikke er indsamleren. Desuden findes indsamlingsdatoen og evt. korte noter om voksestedet. Hvis man kun er interesseret i en bestemt gruppe svampe, kan man lave en s&oslash;gning efter en grovsortering af svampene i 14 grupper, ligesom man kan s&oslash;ge p&aring; oprindelseslandet eller indsamleren. </p>
<p>Basen opdateres 1-2 gange &aring;rligt, og der rettes l&oslash;bende fejl og nye systematiske resultater f&oslash;res ind, men svampenes systematik &aelig;ndres s&aring; hurtigt i disse &aring;r pga molekyl&aelig;re resultater, at det ikke er muligt at v&aelig;re helt opdateret.</p>   
<p>Basens poster er ogs&aring; s&oslash;gbare i det internationale GBIF-system (Global Biodiversity Information Facility).</p>
<p>Databasen er konstrueret og vedligeholdes af Peer Corfixen. Christian Lange har lavet s&oslash;gesiden og Henning Knudsen er videnskabeligt ansvarlig.</p> 
</div>
</fieldset>
<?
	$this->drawRelatedContent();
	}

	private function taxonSelect($name, $id) {
?>
<select name="<? echo $name;?>" id="<? echo $id;?>" size="1">
	<option value="">Alle grupper / all groups </option>
	<option value="1">Phycomycetes</option>
	<option value="2">Discomycetes</option>
	<option value="3">Pyrenomycetes</option>
	<option value="4">Protascomycetes</option>
	<option value="5">Laboulbeniomycetes</option>
	<option value="6">Aphyllophorales</option>
	<option value="7">"Agaricales" (incl. Polyporus)</option>
	<option value="8">Gastromycetes</option>
	<option value="9">Heterobasidiomycetes</option>
	<option value="10">Rust fungi</option>
	<option value="11">Smut fungi</option>
	<option value="12">Myxomycetes</option>
	<option value="13">Mycelia sterilia-anamorphs</option>
	<option value="14">Other anamorphs</option>
	<option value="15">Bacteriae</option>
</select>
<?
	}

}

?>
