<?php

final class MyMapLocationsPage {

	public static function indexPage() {

	}

	public static function newPage() {
		$map = MMLTomM8::get_row_by_id("my_mapped_location_maps", "*", "ID", $_REQUEST["map_id"]);
		MyMapLocationsPage::common_header($map);
		?>
		<div class="postbox " style="display: block; ">
    <div class="inside">
      <form action="" method="post" enctype="multipart/form-data">
        <?php MyMapLocationsPage::render_admin_my_locations_form(null, "Create"); ?>
      </form>
    </div>
    </div>
    </div><!-- End Wrap -->
  	<?php
	}

	public static function editPage() {
		// Display Edit Page
    $my_mapped_location_location_form = MMLTomM8::get_row_by_id("my_mapped_location_locations", "*", "LID", $_GET["id"]);
		$map = MMLTomM8::get_row_by_id("my_mapped_location_maps", "*", "ID", $my_mapped_location_location_form->map_id);
		MyMapLocationsPage::common_header($map);
		
    ?>
      <div class="postbox " style="display: block; ">
      <div class="inside">
        <form action="" method="post" enctype="multipart/form-data">
          <?php MyMapLocationsPage::render_admin_my_locations_form($my_mapped_location_location_form, "Update"); ?>
        </form>
      </div>
      </div>
    </div><!-- End Wrap -->
    <?php 
	}

	public static function render_admin_my_locations_form($instance, $action) { 
    ?>
    <div id="location_column">
      <?php
        
        MMLTomM8::add_form_field($instance, "hidden", "LID *", "LID", "LID", array(), "p", array("class" => "hidden"));
        MMLTomM8::add_form_field($instance, "hidden", "Map ID *", "map_id", "map_id", array(), "p", array("class" => "hidden"));
        MMLTomM8::add_form_field($instance, "text", "Title *", "gmap_title", "gmap_title", array(), "p", array("class" => "text"));
        MMLTomM8::add_form_field($instance, "text", "Lat *", "gmap_lat", "gmap_lat", array(), "p", array("class" => "text"));
        MMLTomM8::add_form_field($instance, "text", "Lng *", "gmap_lng", "gmap_lng", array(), "p", array("class" => "text"));
        MMLTomM8::add_form_field($instance, "text", "Location *", "gmap_location", "gmap_location", array(), "p", array("class" => "text"));
        MMLTomM8::add_form_field($instance, "text", "Phone", "gmap_phone", "gmap_phone", array(), "p", array("class" => "text"));
        
      ?>

      <div id="gmap_webpage_control_container">
        <?php MMLTomM8::add_form_field($instance, "text", "Website", "gmap_website", "gmap_website", array(), "p", array("class" => "text")); ?>
        <input type="button" class="gmap-webpage-control" value="Page/Posts" />
      </div>

      <div id="gmap_image_uploader_container">
        <?php MMLTomM8::add_form_field($instance, "text", "Image URL", "gmap_image", "gmap_image", array(), "p", array("class" => "text")); ?>
        <input type="button" class="gmap-image-uploader" value="Upload" />
      </div>
      <input type="hidden" name="my_mapped_locations_page" value="locations" />
      <input type="hidden" name="action" value="<?php echo($action); ?>" />
      <input type="hidden" name="controller" value="MyMapLocations" />
      <p>
        <input type="submit" name="sub_action" value="<?php echo($action); ?>" /> 
        <?php if ($instance != null) { ?>
          <input type="submit" name="sub_action" value="Save and Finish" />
        <?php } ?>
      </p>
    </div>
    <?php
  }

  	public static function common_header($map) {
		?>
		<div id="control_gmap_page_post_container">
		  <div class="wrap">
		  <h2>My Mapped Locations</h2>
		  <div class="postbox " style="display: block; ">
		  <div class="inside">
		    <table class="form-table">
		      <tbody>
		        <tr valign="top">
		          <th scope="row">
		            <label for="filter_gmap_page_post_name">Search</label>
		          </th>
		          <td>
		            <input type="text" id="filter_gmap_page_post_name" name="filter_gmap_page_post_name" value="" />
		          </td>
		        </tr>
		        <tr>
		          <th></th>
		          <td><div id="gmap_page_posts_container"></div></td>
		        </tr>
		      </tbody>
		    </table>
		  </div>
		  </div>
		  </div>
		</div>

		<div id="upload_gmap_image_container">
		  <div class="wrap">
		  <h2>My Mapped Locations</h2>
		  <div class="postbox " style="display: block; ">
		  <div class="inside">
		    <table class="form-table">
		      <tbody>

		        <tr valign="top">
		          <th scope="row">
		            <label for="upload_gmap_image">Upload</label>
		          </th>
		          <td>
		            <form id="upload_gmap_image_form" method="POST" enctype="multipart/form-data" action="#upload_gmap_image" accept-charset="utf-8" >
		              <input type="file" name="upload_gmap_image[]" id="upload_gmap_image" size="10" class="uploadfiles" />
		              <input class="button-primary" type="submit" name="action" value="Upload"  />
		            </form>
		            <div class="progress">
		                <div class="bar"></div >
		                <div class="percent">0%</div >
		            </div>
		          </td>
		        </tr>

		        <tr valign="top">
		          <th scope="row">
		            <label for="filter_gmap_image_name">Search</label>
		          </th>
		          <td>
		            <input type="text" id="filter_gmap_image_name" name="filter_gmap_image_name" value="" />
		          </td>
		        </tr>
		        <tr>
		          <th></th>
		          <td><div id="gmap_images_container"></div></td>
		        </tr>
		      </tbody>
		    </table>
		  </div>
		  </div>
		  </div>
		</div>
		  
		<div class="wrap">
		  <h2>My Mapped Locations - <?php echo($map->map_name); ?></h2>
		  <?php

		  if (isset($_GET["message"]) && $_GET["message"] != "") {
		    echo("<div class='updated below-h2'><p>".$_GET["message"]."</p></div>");
		  }
	}


}

?>