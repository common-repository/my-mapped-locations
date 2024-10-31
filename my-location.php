<?php
final class MyLocation {

  public static function array_validation_rules() {
    return array(
      "gmap_title" => "required",
      "gmap_lat" => "required",
      "gmap_lng" => "required",
      "gmap_location" => "required"
    );
  }

  public static function update() {

    $form_valid = tom_validate_form(MyLocation::array_validation_rules());

    if ($form_valid) {

      $valid = tom_update_record_by_id("my_mapped_location_locations", 
      tom_get_form_query_strings("my_mapped_location_locations", array("created_at", "updated_at", "map_id"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "LID", $_POST["LID"]);

      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete&action=edit&id=".$_POST["LID"]."&my_mapped_locations_page=locations";
        } else {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Update Complete&action=edit&id=".$_POST["map_id"];
        }
        
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }
      
    }
  }
  public static function create() {

    $form_valid = tom_validate_form(MyLocation::array_validation_rules());

    if ($form_valid) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $valid = tom_insert_record("my_mapped_location_locations", 
        tom_get_form_query_strings("my_mapped_location_locations", array("LID", "created_at", "updated_at"), array("created_at" => $current_datetime, "location_order" => 9999999)));

      if ($valid) {
        $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=edit&id=".$_POST["map_id"]."&message=Record Created";
        tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
        exit;
      }

    }
  }
  public static function delete() {
    // Delete record by id.
    $location = tom_get_row_by_id("my_mapped_location_locations", "*", "LID", $_GET["id"]);
    $map_id = $location->map_id;
    tom_delete_record_by_id("my_mapped_location_locations", "LID", $_GET["id"]);
    $url = get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&message=Record Deleted&action=edit&id=".$map_id;
    tom_javascript_redirect_to($url, "<p>Please <a href='$url'>Click Next</a> to continue.</p>");
    exit;
  }

  public static function render_admin_my_locations_form($instance, $action) { 
    ?>
    <div id="location_column">
      <?php
        
        tom_add_form_field($instance, "hidden", "LID *", "LID", "LID", array(), "p", array("class" => "hidden"));
        tom_add_form_field($instance, "hidden", "Map ID *", "map_id", "map_id", array(), "p", array("class" => "hidden"));
        tom_add_form_field($instance, "text", "Title *", "gmap_title", "gmap_title", array(), "p", array("class" => "text"));
        tom_add_form_field($instance, "text", "Lat *", "gmap_lat", "gmap_lat", array(), "p", array("class" => "text"));
        tom_add_form_field($instance, "text", "Lng *", "gmap_lng", "gmap_lng", array(), "p", array("class" => "text"));
        tom_add_form_field($instance, "text", "Location *", "gmap_location", "gmap_location", array(), "p", array("class" => "text"));
        tom_add_form_field($instance, "text", "Phone", "gmap_phone", "gmap_phone", array(), "p", array("class" => "text"));
        
      ?>

      <div id="gmap_webpage_control_container">
        <?php tom_add_form_field($instance, "text", "Website", "gmap_website", "gmap_website", array(), "p", array("class" => "text")); ?>
        <input type="button" class="gmap-webpage-control" value="Page/Posts" />
      </div>

      <div id="gmap_image_uploader_container">
        <?php tom_add_form_field($instance, "text", "Image URL", "gmap_image", "gmap_image", array(), "p", array("class" => "text")); ?>
        <input type="button" class="gmap-image-uploader" value="Upload" />
      </div>
      <input type="hidden" name="my_mapped_locations_page" value="locations" />
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