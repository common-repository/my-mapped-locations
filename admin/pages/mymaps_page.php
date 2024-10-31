<?php
final class MyMapsPage {
	public static function indexPage() { 
		MyMapsPage::common_header();
		?>
    <div class="postbox " style="display: block; ">
    <div class="inside">
      <?php MMLTomM8::generate_datatable("my_mapped_location_maps", array("ID", "map_name"), "ID", "", array("map_name ASC"), __MYMAPPEDLOCATIONS_DEFAULT_LIMIT__, get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php", false, true, true, true, true); ?>
    </div>
    </div>
  	</div>
  	<?php
	}

	public static function newPage() { 
		MyMapsPage::common_header();
		?>
	  <div class="postbox " style="display: block; ">
	    <div class="inside">
	      <form action="" method="post">
	        <?php MyMapsPage::render_admin_map_form(null, "Create"); ?>
	      </form>
	    </div>
	  </div>
	  </div><!-- End Wrap -->
  <?php
	}

	public static function editPage() { 
	  MyMapsPage::common_header();
	  // Display Edit Page
	  $my_mapped_location_form = MMLTomM8::get_row_by_id("my_mapped_location_maps", "*", "ID", $_GET["id"]); ?>

	  <div class="postbox " style="display: block; ">
	  <div class="inside">
	    <form action="" method="post">
	      <?php MyMapsPage::render_admin_map_form($my_mapped_location_form, "Update"); ?>
	    </form>
	  </div>
	  </div>   
	  </div><!-- End Wrap -->
	  <?php
	}

	public static function render_admin_map_form($instance, $action) { ?>
    <div id="setting_column">
	  <?php
		  MMLTomM8::add_form_field($instance, "hidden", "ID *", "ID", "ID", array(), "span", array("class" => "hidden"));
		  MMLTomM8::add_form_field($instance, "text", "Name *", "map_name", "map_name", array("class" => "text"), "p", array());
      MMLTomM8::add_form_field($instance, "text", "Width (px) *", "width", "width", array("class" => "text"), "p", array());
      MMLTomM8::add_form_field($instance, "text", "Height (px) *", "height", "height", array("class" => "text"), "p", array());
			MMLTomM8::add_form_field($instance, "checkbox", "Include Directions", "include_directions", "include_directions", array("class" => "checkbox"), "p", array(), array("1" => "Yes"));
    ?>
    <div id="location_column">
      <?php if ($action == "Update") { ?>
        <h2>Locations <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=new&controller=MyMapLocations&map_id=<?php echo($instance->ID); ?>">Add New Location</a></h2>
        <?php MMLTomM8::generate_datatable("my_mapped_location_locations", array("LID", "gmap_title", "gmap_lat", "gmap_lng", "gmap_location" ), "LID", "map_id = ".$instance->ID, array("location_order ASC"), "", get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&controller=MyMapLocations", false, true, true);
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

	public static function common_header() {
		?>
		<div class="wrap">
	  <h2>My Mapped Locations <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&action=new">Add New Map</a></h2>
	  <?php
	  if (isset($_GET["message"]) && $_GET["message"] != "") {
	    echo("<div class='updated below-h2'><p>".$_GET["message"]."</p></div>");
	  }
	}
}
?>