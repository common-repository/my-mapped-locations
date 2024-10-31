<?php
final class MyMapLocationsController {

	public static function newAction() {
		MyMapLocationsPage::newPage();
	}

	public static function createAction() {

    if (MyMapLocationsValidation::is_valid()) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = MMLTomM8::insert_record("my_mapped_location_locations", 
        MMLTomM8::get_form_query_strings("my_mapped_location_locations", array("LID", "created_at", "updated_at"), array("created_at" => $current_datetime, "location_order" => 9999999)));

      if ($valid) {
        $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=edit&id=".$_POST["map_id"]."&message=Record Created";
        MMLTomM8::javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
    }
    MyMapLocationsPage::newPage();
	}

	public static function editAction() {
    MyMapLocationsPage::editPage();
	}

	public static function updateAction() {
		if (MyMapLocationsValidation::is_valid()) {
      $valid = MMLTomM8::update_record_by_id("my_mapped_location_locations", 
      MMLTomM8::get_form_query_strings("my_mapped_location_locations", array("created_at", "updated_at", "map_id"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "LID", $_POST["LID"]);

      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete&action=edit&id=".$_POST["LID"]."&controller=MyMapLocations";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete&action=edit&id=".$_POST["map_id"];
        }
        
        MMLTomM8::javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
      
    }
    MyMapLocationsPage::editPage();
	}

	public static function deleteAction() {
		// Delete record by id.
    $location = MMLTomM8::get_row_by_id("my_mapped_location_locations", "*", "LID", $_GET["id"]);
    $map_id = $location->map_id;
    MMLTomM8::delete_record_by_id("my_mapped_location_locations", "LID", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Record Deleted&action=edit&id=".$map_id;
    MMLTomM8::javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
    exit;
	}

}
?>