<?php
final class MyMapStylingController {
	public static function indexAction() {
		$css_content = file_get_contents(get_template_directory_uri()."/my_mapped_locations_css/".get_option("my_mapped_location_current_css_file"));
		if (isset($_POST["css_content"])) {
			$location = get_template_directory()."/my_mapped_locations_css/".get_option("my_mapped_location_current_css_file");
			$css_content = $_POST["css_content"];
			$css_content = str_replace('\"', "\"", $css_content);
			$css_content = str_replace("\'", '\'', $css_content);
			MMLTomM8::write_to_file($css_content, $location);
		}
		MyMapStylingPage::indexPage($css_content);
	}
}
?>