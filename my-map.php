<?php
final class MyMap {

  public static function array_validation_rules() {
    return array(
      "map_name" => "required",
      "width" => "required",
      "height" => "required"
    );
  }

	public static function update() {
    $form_valid = tom_validate_form(MyMap::array_validation_rules());

		if ($form_valid) {

      $valid = tom_update_record_by_id("my_mapped_location_maps", 
      tom_get_form_query_strings("my_mapped_location_maps", array("created_at", "updated_at"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "ID", $_POST["ID"]);
      
      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete&action=edit&id=".$_POST["ID"]."";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete";
        }
        
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
      
    }
	}
	public static function create() {
    $form_valid = tom_validate_form(MyMap::array_validation_rules());

    if ($form_valid) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = tom_insert_record("my_mapped_location_maps", 
        tom_get_form_query_strings("my_mapped_location_maps", array("ID", "created_at", "updated_at"), array("created_at" => $current_datetime)));

      if ($valid) {
        global $wpdb;
        $map_id = $wpdb->insert_id;
        tom_insert_record("my_mapped_location_locations", 
          array("gmap_title" => "Location 1",
                "map_id" => $map_id, 
                "created_at" => $current_datetime
               )
        );

        $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=edit&id=".$map_id."&message=Record Created";
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }

    }
	}
	public static function delete() {
	  // Delete record by id.
    tom_delete_record_by_id("my_mapped_location_maps", "ID", $_GET["id"]);
    tom_delete_record_by_id("my_mapped_location_locations", "map_id", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Record Deleted";
    tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
    exit;
	}

	public static function render_admin_map_form($instance, $action) { ?>
    <div id="setting_column">
	  <?php
		  tom_add_form_field($instance, "hidden", "ID *", "ID", "ID", array(), "span", array("class" => "hidden"));
		  tom_add_form_field($instance, "text", "Name *", "map_name", "map_name", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "text", "Width (px) *", "width", "width", array("class" => "text"), "p", array());
      tom_add_form_field($instance, "text", "Height (px) *", "height", "height", array("class" => "text"), "p", array());
			tom_add_form_field($instance, "checkbox", "Directions", "include_directions", "include_directions", array("class" => "checkbox"), "p", array(), array("1" => "Yes"));
    ?>
    <div id="location_column">
      <?php if ($action == "Update") { ?>
        <h2>Locations <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=new&my_mapped_locations_page=locations&map_id=<?php echo($instance->ID); ?>">Add New Location</a></h2>
        <?php tom_generate_datatable("my_mapped_location_locations", array("LID", "gmap_title", "gmap_lat", "gmap_lng", "gmap_location" ), "LID", "map_id = ".$instance->ID, array("location_order ASC"), "", get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&my_mapped_locations_page=locations", false, true, true);
      } ?>
    </div>
    <input type="hidden" name="my_mapped_locations_page" value="" />
    <input type="hidden" name="action" value="<?php echo($action); ?>" />
    <p>
      <input type="submit" name="sub_action" value="<?php echo($action); ?>" /> 
      <?php if ($instance != null) { ?>
        <input type="submit" name="sub_action" value="Save and Finish" />
      <?php } ?>
    </p>
    </div>
    <?php
	}

}

?>