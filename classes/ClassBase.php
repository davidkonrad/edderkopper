<?

class ClassBase extends Base {
	//public $template = 'TemplateClass';

	public function __construct() {
		parent::__construct();
		$this->info=$this->getInfo();
	}

	protected function drawSessLang() {
		echo '<input type="hidden" name="sess_lang" value="'.$_SESSION[LANGUAGE].'"/>'."\n";
	}

	protected function drawLoggedIn() {
		if (isset($_SESSION[LOGIN])) {
			echo '<input type="hidden" id="loggedin" name="loggedin" value="yes"/>'."\n";
		}
	}

	//so we have the same buttons with same id's on every search form
	protected function formButtons($form='', $exact=false) {
		echo '<tr>';
		if ($exact) {
			echo '<td><input type="checkbox" name="exact" id="exact">'.trans(LAB_SEARCH_EXACT).'</td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
?>
		<td><input type="button" id="search-button" value="<? trans(LAB_SEARCH, true);?>" onclick="Search.submit(&quot;<? echo $form;?>&quot;);"/>&nbsp;&nbsp;
		<input type="button" id="reset-button" value="<? trans(LAB_RESET, true);?>" onclick="Search.reset();"/>
		</td>
	</tr>
<?
	}

	//implement dato interval search (full) with auto filtering in search.js
	protected function formDatoInterval() {
?>
	<tr>
		<td><label for="year"><? trans(LAB_DATE, true);?></label></td>
		<td>
			<small class="datoInterval" style="margin-left:0px;"><? trans(LAB_DATE_YEAR, true);?></small>
				<input type="text" class="datoInterval" name="year" style="width:40px;" id="year"/>
			<small class="datoInterval"><? trans(LAB_DATE_MONTH, true);?></small>
				<input type="text" class="datoInterval" name="month" style="width:40px;" id="month"/>
			<small class="datoInterval"><? trans(LAB_DATE_DAY, true);?></small>
				<input type="text" class="datoInterval" name="day" style="width:40px;" id="day"/>
		</td>
	</tr>
	<tr>		
		<td><label for="to-year"><? trans(LAB_DATE_TO, true);?></label></td>
		<td>
			<small class="datoInterval" style="margin-left:0px;"><? trans(LAB_DATE_YEAR, true);?></small>
				<input type="text" class="datoInterval" name="to-year" style="width:40px;" id="to-year"/>
			<small class="datoInterval"><? trans(LAB_DATE_MONTH, true);?></small>
				<input type="text" class="datoInterval" name="to-month" style="width:40px;" id="to-month"/>
			<small class="datoInterval"><? trans(LAB_DATE_DAY, true);?></small>
				<input type="text" class="datoInterval" name="to-day" style="width:40px;" id="to-day"/>
		</td>
	</tr>
<?
	}

}

?>
