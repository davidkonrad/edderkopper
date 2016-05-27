<?

class ImageDesc {

	static public function desc($filename) {
		
		$regEx = '/^abd-dor|abd-ven|epig-area|eye-fron|fragm|gen|hab-dor-lat-ven|hab-dor-select|hab-fron|hab-lat|hab-lat-left|hab-lat-left-xr|hab-lat-right|hab-lat-right-xr|hab-ven|hab-ven-select|head-dor|head-fron|head-lat|head-ven|label|thorax|thorax-dor|abact-oss|act-oss|adamb-oss|amb-oss|infmarg|or-oss|supmarg|term-oss|carap-dor|cephalon-dor|cephalon-fron|cephalothor-dor|cephalothor-lat|cephalothor-ven|epig-lat|epig-ven|max|palp-dor|palp-lat|palp-mes|palp-ven|pros-dor|pros-ven|soma-dor|soma-ven|spin-ven|ext|hect-arm|int|l-hinge|l-val-ext|l-val-int|r-hinge|r-val-ext|r-val-int|shell-dor|shell-mo|shell-side|shell-top|shell-ven|spec-arm|t-club|api|can-buc|can-lin|cran-dor|cran-lat|cran-ven|dent-lat|dent-occ|dis|fem-cau|fem-cra|lab|lin|man-buc|man-lat|man-lin|max-occ|mes|pes-dor|pes-ven|scu-dor|scu-ven|tib-dor|tib-ven|vert-cau|vert-cra|vert-dor|cast|cpt|mold|pt|seed|leaf|cone|hab|hab-dor/siU';

		$codeToDesc = array(
		"abd-dor" => "Abdomen, dorsal view",
		"abd-ven" => "Abdomen, ventral view",
		"epig-area" => "Epigastric area",
		"eye-fron" => "Eye, frontal view",
		"fragm" => "Fragment",
		"gen" => "Genitalia",
		"hab" => "Habitus",
		"hab-dor" => "Habitus, dorsal view",
		"hab-dor-lat-ven" => "Habitus, dorsal, lateral, and ventral views",
		"hab-dor-select" => "Habitus, dorsal view, selected specimen(s)",
		"hab-fron" => "Habitus, frontal view",
		"hab-lat" => "Habitus, lateral view",
		"hab-lat-left" => "Habitus, lateral view, left side",
		"hab-lat-left-xr" => "Habitus, lateral view, left side, x-ray",
		"hab-lat-right" => "Habitus, lateral view, right side",
		"hab-lat-right-xr" => "Habitus, lateral view, right side, x-ray",
		"hab-ven" => "Habitus, ventral view",
		"hab-ven-select" => "Habitus, ventral view, selected specimen(s)",
		"head-dor" => "Head, dorsal view",
		"head-fron" => "Head, frontal view",
		"head-lat" => "Head, lateral view",
		"head-ven" => "Head, ventral view",
		"label" => "Label",
		"thorax" => "Thorax",
		"thorax-dor" => "Thorax, dorsal",
		"abact-oss" => "Abactinal ossicle",
		"act-oss" => "Actinal ossicle",
		"adamb-oss" => "Adambulacral ossicle",
		"amb-oss" => "Ambulacral ossicle",
		"infmarg" => "Inferomarginal",
		"or-oss" => "Oral ossicle",
		"supmarg" => "Superomarginal",
		"term-oss" => "Terminal ossicle",
		"carap-dor" => "Carapace, dorsal view",
		"cephalon-dor" => "Cephalon, dorsal view",
		"cephalon-fron" => "Cephalon, frontal view",
		"cephalothor-dor" => "Cephalothorax, dorsal view",
		"cephalothor-lat" => "Cephalothorax, lateral view",
		"cephalothor-ven" => "Cephalothorax, ventral view",
		"epig-lat" => "Epigynium, lateral view",
		"epig-ven" => "Epigynium, ventral view",
		"max" => "Maxillae",
		"palp-dor" => "Palp, dorsal view",
		"palp-lat" => "Palp, lateral view",
		"palp-mes" => "Palp, mesal view",
		"palp-ven" => "Palp, ventral view",
		"pros-dor" => "Prosoma, dorsal view",
		"pros-ven" => "Prosoma, ventral view",
		"soma-dor" => "Soma, dorsal view",
		"soma-ven" => "Soma, ventral view",
		"spin-ven" => "Spinneret, ventral view",
		"ext" => "Exterior view",
		"hect-arm" => "Hectocotylized arm",
		"int" => "Interior view",
		"l-hinge" => "Left hinge",
		"l-val-ext" => "Left valve, exterior view",
		"l-val-int" => "Left valve, interior view",
		"r-hinge" => "Right hinge",
		"r-val-ext" => "Right valve, external view",
		"r-val-int" => "Right valve, internal view",
		"shell-dor" => "Shell, dorsal view",
		"shell-mo" => "Shell, mouth view",
		"shell-side" => "Shell, side view",
		"shell-top" => "Shell, apical view",
		"shell-ven" => "Shell, ventral view",
		"spec-arm" => "Specialised arm",
		"t-club" => "Tentacle club",
		"api" => "Apical view",
		"can-buc" => "Canine buccal view",
		"can-lin" => "Canine, lngual view",
		"cran-dor" => "Cranium, dorsal view",
		"cran-lat" => "Cranium, lateral view",
		"cran-ven" => "Cranium, ventral view",
		"dent-lat" => "Dentition, lateral view",
		"dent-occ" => "Dentition, occlusal view",
		"dis" => "Distal view",
		"fem-cau" => "Femur, caudal view",
		"fem-cra" => "Femur, cranial view",
		"lab" => "Labial view",
		"lin" => "Lingual view",
		"man-buc" => "Mandible, buccal view",
		"man-lat" => "Mandible, lateral view",
		"man-lin" => "Mandible, lingual view",
		"max-occ" => "Maxila, occusal view",
		"mes" => "Mesial view",
		"pes-dor" => "Pes, dorsal view",
		"pes-ven" => "Pes, ventral view",
		"scu-dor" => "Scutes, dorsal view",
		"scu-ven" => "Scutes, ventral view",
		"tib-dor" => "Tibia, dorsl view",
		"tib-ven" => "Tibia, ventralview",
		"vert-cau" => "Vertebrae, caudal view",
		"vert-cra" => "Vertebrae, cranial view",
		"vert-dor" => "Vertebrae, dorsal vew",
		"cast" => "Cast",
		"cpt" => "Counterpart",
		"mold" => "Mold",
		"pt" => "Part",
		"seed" => "Seed",
		"leaf" => "Leaf",
		"cone" => "Cone"
		);

		$desc='';
		preg_match_all($regEx, $filename, $matches);
		foreach($matches as $match) {
			if (isset($match[0])) {
				if (isset($codeToDesc[$match[0]])) {
					$desc.=$codeToDesc[$match[0]];
				}
			}
		}
		return $desc;
	}
	
}

?>
