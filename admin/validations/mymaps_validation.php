<?php
final class MyMapsValidation {
  public static function array_validation_rules() {
    return array(
      "map_name" => "required",
      "width" => "required",
      "height" => "required"
    );
  }

  public static function is_valid() {
  	return MMLTomM8::validate_form(MyMapsValidation::array_validation_rules());
  }
}
?>