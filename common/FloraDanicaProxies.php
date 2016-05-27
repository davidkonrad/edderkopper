<?
//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

class FloraDanicaProxies {

	public static function imagePath($fascicle, $arabic) {
		$image='http://digit.snm.ku.dk/bm/openup/floradanica/';
		$image.='m'.$fascicle.'/';
		$image.=str_pad($arabic, 4, '0', STR_PAD_LEFT).'-1200.jpg';
		return $image;
	}

	public static function textPath($fascicle, $roman) {
		return 'FloraDanica/TEXT/'.$fascicle.'/'.strtoupper($roman).'.jpg';
	}

	public static function detailsPath($auto_id=false) {
		//session vars is set (scope is inside the CMS)
		if (defined('LANGUAGE')) {
			switch ($_SESSION[LANGUAGE]) {
				case 2 : $details='Flora-Danica-details-en'; break;
				default : $details='Flora-Danica-details-dk'; break;
			}
		} else {
		//it is an AJAX-call
			switch ($_GET['sess_lang']) {
				case 2 : $details='Flora-Danica-details-en'; break;
				default : $details='Flora-Danica-details-dk'; break;
			} 
		}

		if ($auto_id) {
			$details.='?auto_id='.$auto_id;
		}
		return $details;
	}	

}

?>
