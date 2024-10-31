<?php
final class MyMapsController {
	public static function indexAction() {
		$my_mapped_locations = MMLTomM8::get_results("my_mapped_location_maps", "*", "");
    if (count($my_mapped_locations) == 0) {
      $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=new";
      MMLTomM8::javascript_redirect_to($url, "<p>Start by creating a map.</p>");
    } else {
	    MyMapsPage::indexPage();	
    } 
	}

	public static function newAction() {
		MyMapsPage::newPage();
	}

	public static function createAction() {
    if (MyMapsValidation::is_valid()) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = MMLTomM8::insert_record("my_mapped_location_maps", 
        MMLTomM8::get_form_query_strings("my_mapped_location_maps", array("ID", "created_at", "updated_at"), array("created_at" => $current_datetime)));

      if ($valid) {
        global $wpdb;
        $map_id = $wpdb->insert_id;
        MMLTomM8::insert_record("my_mapped_location_locations", 
          array("gmap_title" => "Location 1",
                "map_id" => $map_id, 
                "created_at" => $current_datetime
               )
        );

        $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=edit&id=".$map_id."&message=Record Created";
        MMLTomM8::javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }

    }
    MyMapsPage::newPage();
	}

	public static function editAction() {
		MyMapsPage::editPage();
	}

	public static function updateAction() {
		if (MyMapsValidation::is_valid()) {
      $valid = MMLTomM8::update_record_by_id("my_mapped_location_maps", 
      MMLTomM8::get_form_query_strings("my_mapped_location_maps", array("created_at", "updated_at"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "ID", $_POST["ID"]);
      
      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete&action=edit&id=".$_POST["ID"]."";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete";
        }
        
        MMLTomM8::javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
      
    }
    MyMapsPage::editPage();
	}

	public static function deleteAction() {
	  // Delete record by id.
    MMLTomM8::delete_record_by_id("my_mapped_location_maps", "ID", $_GET["id"]);
    MMLTomM8::delete_record_by_id("my_mapped_location_locations", "map_id", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Record Deleted";
    MMLTomM8::javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
    exit;
	}
}
?>