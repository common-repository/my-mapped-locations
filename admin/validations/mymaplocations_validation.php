<?php

final class MyMapLocationsValidation {

	public static function array_validation_rules() {
    return array(
      "gmap_title" => "required",
      "gmap_lat" => "required",
      "gmap_lng" => "required",
      "gmap_location" => "required"
    );
  }

	public static function is_valid() {
		return MMLTomM8::validate_form(MyMapLocationsValidation::array_validation_rules());
	}
}

?>