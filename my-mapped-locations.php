<?php
/*
Plugin Name: My Mapped Locations
Plugin URI: http://wordpress.org/extend/plugins/my-mapped-locations/
Description: Adds a google map to your wordpress site.

Installation:

1) Install WordPress 4.0 or higher

2) Download the latest from: 

http://wordpress.org/extend/plugins/my-mapped-locations

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 3.3.2
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/

if (!class_exists("MMLTomM8")) {
  include_once("lib/tom-m8te.php");
}
include_once("admin/controllers/mymaps_controller.php");
include_once("admin/validations/mymaps_validation.php");
include_once("admin/pages/mymaps_page.php");
include_once("admin/controllers/mymaplocations_controller.php");
include_once("admin/validations/mymaplocations_validation.php");
include_once("admin/pages/mymaplocations_page.php");
require_once("my-mapped-location-path.php");
include_once (dirname (__FILE__) . '/tinymce/tinymce.php'); 

define(__MYMAPPEDLOCATIONS_DEFAULT_LIMIT__, "10");

register_activation_hook( __FILE__, 'my_mapped_locations_activate' );
function my_mapped_locations_activate() {
  global $wpdb;

  $my_mapped_location_maps_table = $wpdb->prefix . "my_mapped_location_maps";
  $checktable = $wpdb->query("SHOW TABLES LIKE '$my_mapped_location_maps_table'");
  if ($checktable == 0) {

    $sql = "CREATE TABLE $my_mapped_location_maps_table (
      ID mediumint(9) NOT NULL AUTO_INCREMENT, 
      map_name VARCHAR(255),
      width mediumint(9) DEFAULT 500,
      height mediumint(9) DEFAULT 500,
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (ID)
    )";
    $wpdb->query($sql); 

    $my_mapped_location_locations_table = $wpdb->prefix . "my_mapped_location_locations";
    $sql = "CREATE TABLE $my_mapped_location_locations_table (
      LID mediumint(9) NOT NULL AUTO_INCREMENT, 
      gmap_lat VARCHAR(255),
      gmap_lng VARCHAR(255),
      gmap_title VARCHAR(255),
      gmap_location VARCHAR(255),
      gmap_phone VARCHAR(255),
      gmap_website VARCHAR(255),
      gmap_image VARCHAR(255),
      location_order mediumint(9) DEFAULT 0,
      map_id mediumint(9) NOT NULL,
      created_at DATETIME,
      updated_at DATETIME,
      PRIMARY KEY  (LID)
    )";
    $wpdb->query($sql); 

  }

  $checkcol = $wpdb->query("SHOW COLUMNS FROM '$my_mapped_location_maps_table' LIKE 'include_directions'");
  if ($checkcol == 0) {
    $sql = "ALTER TABLE $my_mapped_location_maps_table ADD include_directions VARCHAR(1)";
    $wpdb->query($sql); 
  }

  if (!is_dir(get_template_directory()."/my_mapped_locations_css")) {
    my_mapped_location_copy_directory(LocationPath::normalize(dirname(__FILE__)."/css"), get_template_directory());  
  } else {
    add_option("my_mapped_location_current_css_file", "default.css");
  }

}

function are_my_mapped_locations_dependencies_installed() {
  return is_plugin_active("jquery-colorbox/jquery-colorbox.php");
}

add_action( 'admin_notices', 'my_mapped_locations_notice_notice' );
function my_mapped_locations_notice_notice(){
  $activate_nonce = wp_create_nonce( "activate-my-mapped-location-dependencies" );
  $colorbox_active = is_plugin_active("jquery-colorbox/jquery-colorbox.php");
  if (!($colorbox_active)) { ?>
    <div class='updated below-h2'><p>Before you can use My Mapped Locations, please install/activate the following plugin(s):</p>
    <ul>
      <?php if (!$colorbox_active) { ?>
        <li>
          <a target="_blank" href="http://wordpress.org/extend/plugins/jquery-colorbox/">Colorbox</a>
           &#8211; 
          <?php if (file_exists(ABSPATH."/wp-content/plugins/jquery-colorbox/jquery-colorbox.php")) { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/?my_mapped_locations_install_dependency=jquery-colorbox&_wpnonce=<?php echo($activate_nonce); ?>">Activate</a>
          <?php } else { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=jquery-colorbox&_wpnonce=<?php echo($activate_nonce); ?>&TB_iframe=true&width=640&height=876">Install</a> 
          <?php } ?>
        </li>
      <?php } ?>
    </ul>
    </div>
    <?php
  }

}

add_action( 'admin_init', 'register_my_mapped_locations_install_dependency_settings' );
function register_my_mapped_locations_install_dependency_settings() {
  if (isset($_GET["my_mapped_locations_install_dependency"])) {
    if (wp_verify_nonce($_REQUEST['_wpnonce'], "activate-my-mapped-location-dependencies")) {
      switch ($_GET["my_mapped_locations_install_dependency"]) {
        case 'jquery-colorbox':
          activate_plugin('jquery-colorbox/jquery-colorbox.php', 'plugins.php?error=false&plugin=jquery-colorbox.php');
          wp_redirect(get_option("siteurl")."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php");
          exit();
          break;    
        default:
          throw new Exception("Sorry unable to install plugin.");
          break;
      }
    } else {
      die("Security Check Failed.");
    }
  }
}

add_action( 'admin_init', 'register_my_mapped_locations_page_post_search_settings' );
function register_my_mapped_locations_page_post_search_settings() {
  $filter_page_post_name = $_POST["filter_gmap_page_post_name"];
  if ($filter_page_post_name != "") {
    $page_posts = MMLTomM8::get_results("posts", "*", "post_type IN ('page', 'post') AND post_title LIKE '%$filter_page_post_name%'", array("post_date DESC"), "7");
    echo "<ul id='gmap_page_posts'>";
    foreach ($page_posts as $page_post) { 
        ?>
        <li>
          <a href="<?php echo($page_post->guid); ?>"><?php echo($page_post->post_title); ?></a>          
        </li>
    <?php }
    echo "</ul>";
    exit();
  }
} 

add_action( 'admin_init', 'register_my_mapped_locations_search_settings' );
function register_my_mapped_locations_search_settings() {
  $filter_image_name = $_POST["filter_gmap_image_name"];
  if ($filter_image_name != "") {
    $images = MMLTomM8::get_results("posts", "*", "post_type='attachment' AND post_title LIKE '%$filter_gmap_image_name%' AND post_mime_type IN ('image/png', 'image/jpg', 'image/jpeg', 'image/gif')", array("post_date DESC"), "7");
    echo "<ul id='gmap_images'>";
    foreach ($images as $image) { 
        ?>
        <li>
          <img style='width: 100px; min-height: 100px' src='<?php echo($image->guid); ?>' />
        </li>

    <?php }
    echo "</ul>";
    exit();
  }
} 

add_action( 'admin_init', 'register_my_mapped_locations_upload_settings' );
function register_my_mapped_locations_upload_settings() {
  $uploadfiles = $_FILES['upload_gmap_image'];

  if (is_array($uploadfiles)) {

    foreach ($uploadfiles['name'] as $key => $value) {

      // look only for uploded files
      if ($uploadfiles['error'][$key] == 0) {

        $filetmp = $uploadfiles['tmp_name'][$key];

        //clean filename and extract extension
        $filename = $uploadfiles['name'][$key];

        // get file info
        // @fixme: wp checks the file extension....
        $filetype = wp_check_filetype( basename( $filename ), null );
        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
        $filename = $filetitle . '.' . $filetype['ext'];
        $upload_dir = wp_upload_dir();

        /**
         * Check if the filename already exist in the directory and rename the
         * file if necessary
         */
        $i = 0;
        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
          $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
          $i++;
        }
        $filedest = $upload_dir['path'] . '/' . $filename;

        /**
         * Check write permissions
         */
        if ( !is_writeable( $upload_dir['path'] ) ) {
          $this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
          return;
        }

        /**
         * Save temporary file to uploads dir
         */
        if ( !@move_uploaded_file($filetmp, $filedest) ){
          $this->msg_e("Error, the file $filetmp could not moved to : $filedest ");
          continue;
        }

        $attachment = array(
          'post_mime_type' => $filetype['type'],
          'post_title' => $filetitle,
          'post_content' => '',
          'post_status' => 'inherit',
        );

        $attach_id = wp_insert_attachment( $attachment, $filedest );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        preg_match("/\/wp-content(.+)$/", $filedest, $matches, PREG_OFFSET_CAPTURE);
        MMLTomM8::update_record_by_id("posts", array("guid" => get_option("siteurl").$matches[0][0]), "ID", $attach_id);
        echo $filedest;
      }
    }   
  }
}

add_action('admin_menu', 'register_my_mapped_locations_page');
function register_my_mapped_locations_page() {
  add_menu_page('My Mapped Locations', 'My Mapped Locations', 'manage_options', 'my-mapped-locations/my-mapped-locations.php', 'my_mapped_location_router', plugins_url("/tinymce/images/google_maps_icon.png", __FILE__));
  add_submenu_page('my-mapped-locations/my-mapped-locations.php', 'Styling', 'Styling', 'update_themes', 'my-mapped-locations/my-mapped-locations-styling.php');
}

add_action('wp_ajax_my_mapped_location_css_file_selector', 'my_mapped_location_css_file_selector');
function my_mapped_location_css_file_selector() {
  update_option("my_mapped_location_current_css_file", $_POST["css_file_selection"]);
  echo(@file_get_contents(get_template_directory()."/my_mapped_locations_css/".$_POST["css_file_selection"]));
  die();  
}

add_action('wp_ajax_my_mapped_locations_tinymce', 'my_mapped_locations_tinymce');
/**
 * Call TinyMCE window content via admin-ajax
 * 
 * @since 1.7.0 
 * @return html content
 */
function my_mapped_locations_tinymce() {

    // check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
      die(__("You are not allowed to be here"));
          
    include_once( dirname( dirname(__FILE__) ) . '/my-mapped-locations/tinymce/window.php');
    
    die();  
}

add_action('admin_init', 'register_my_mapped_locations_admin_scripts');
function register_my_mapped_locations_admin_scripts() {
  if (preg_match("/my-mapped-locations/", $_REQUEST["page"])) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable');
    wp_register_script( 'my-jquery-colorbox', get_option("siteurl")."/wp-content/plugins/jquery-colorbox/js/jquery.colorbox-min.js" );
    wp_enqueue_script('my-jquery-colorbox');

    wp_register_script( 'my-form-script', plugins_url('/admin_js/jquery.form.js', __FILE__) );
    wp_enqueue_script('my-form-script');

    wp_register_script("admin-my-mapped-location", plugins_url("/admin_js/application.js", __FILE__));
    wp_enqueue_script("admin-my-mapped-location");

    wp_localize_script( 'admin-my-mapped-location', 'MyMappedLocationsAjax', array(
      "base_url" => get_option("siteurl"),
      "ajax_url" => admin_url('admin-ajax.php'),
      "sort_locations_url" => get_option('siteurl')."/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php&controller=MyMapLocations",
    ));

    wp_register_style("admin-my-mapped-location", plugins_url("/admin_css/style.css", __FILE__));
    wp_enqueue_style("admin-my-mapped-location");

    wp_register_style( 'my-jquery-colorbox-style',get_option("siteurl")."/wp-content/plugins/jquery-colorbox/themes/theme1/colorbox.css");
    wp_enqueue_style('my-jquery-colorbox-style');
  }
}

function my_mapped_location_router() {
  if (are_my_mapped_locations_dependencies_installed()) {
    $controller = "";
    $action = "";
    if ($_REQUEST["controller"] == "" || $_REQUEST["controller"] == "MyMaps") {
      $controller = "MyMapsController";
    } else if ($_REQUEST["controller"] == "MyMapLocations"){
      $controller = "MyMapLocationsController";
    }

    if ($_REQUEST["sub_action"] != "") {
      $action = $_REQUEST["action"]."Action";
    } else if ($_REQUEST["action"] != "") {
      $action = $_REQUEST["action"]."Action";
    } else {
      $action = "indexAction";
    }

    $controller::$action();
    MMLTomM8::add_social_share_links("http://wordpress.org/extend/plugins/my-mapped-locations/");
  }
}

add_shortcode( 'my-mapped-location', 'my_mapped_location_shortcode' );

function my_mapped_location_shortcode($atts) {
  $map = MMLTomM8::get_row_by_id("my_mapped_location_maps", "*", "ID", $atts["id"]);
  $locations = MMLTomM8::get_results("my_mapped_location_locations", "*", "map_id='".$atts["id"]."'", array("location_order ASC")); 
  ?>
  <div class="google-map-container">
    <input type="hidden" name="gmap_name" value="my_mapped_location_<?php echo($map->ID); ?>" />
		<input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_include_directions" value="<?php echo($map->include_directions); ?>" />
    <div class="google-map" id="my_mapped_location_<?php echo($map->ID); ?>" style="width: <?php echo($map->width); ?>px; height: <?php echo($map->height); ?>px;"></div>  
    <div class="location-container">
			
			<?php if ($map->include_directions) { ?>
	      <div class="directions">
	        <p><label for="my_mapped_location_<?php echo($map->ID); ?>_start">From</label><input type="text" id="my_mapped_location_<?php echo($map->ID); ?>_start" class="start" name="start" /></p>
	        <p><label for="my_mapped_location_<?php echo($map->ID); ?>_end">To</label><input type="text" id="my_mapped_location_<?php echo($map->ID); ?>_end" class="end" name="end" readonly /></p>
	        <p><input type="button" class="get-directions" id="my_mapped_location_<?php echo($map->ID); ?>_get_directions" value="Get Directions"/></p>
	      </div>
			<?php } ?>
      <p>Click below for map location</p>
      <ul class="locations" map="my_mapped_location_<?php echo($map->ID); ?>">
        <?php foreach ($locations as $location) { ?>
          <li id="my_mapped_location_<?php echo($map->ID); ?>_location_<?php echo($location->LID) ?>"><?php echo($location->gmap_title); ?></li>
        <?php } ?>
      </ul>
    </div>
    <?php foreach ($locations as $location) { ?>
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_lat" value="<?php echo($location->gmap_lat) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_lng" value="<?php echo($location->gmap_lng) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_title" value="<?php echo($location->gmap_title) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_location" value="<?php echo($location->gmap_location) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_phone" value="<?php echo($location->gmap_phone) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_website" value="<?php echo($location->gmap_website) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_image" value="<?php echo($location->gmap_image) ?>" />
      <input type="hidden" name="my_mapped_location_<?php echo($map->ID); ?>_gmap_location_id" value="my_mapped_location_<?php echo($map->ID); ?>_location_<?php echo($location->LID) ?>" />
    <?php } ?>
  </div>
  <?php
}

add_action('wp_head', 'add_my_mapped_locations_js_and_css');
function add_my_mapped_locations_js_and_css() { 
  wp_enqueue_script('jquery');

  wp_register_script("jquery-google-ui", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js");
  wp_enqueue_script("jquery-google-ui");
  
  wp_register_script("jquery-google-api", "http://maps.google.com/maps/api/js?sensor=false");
  wp_enqueue_script("jquery-google-api");

  wp_register_script("my-mapped-locations", plugins_url("/js/application.js", __FILE__));
  wp_enqueue_script("my-mapped-locations");

  wp_register_style("my-mapped-locations", get_template_directory_uri().'/my_mapped_locations_css/'.get_option("my_mapped_location_current_css_file"));
  wp_enqueue_style("my-mapped-locations");
} 

// Copy directory to another location.
function my_mapped_location_copy_directory($src,$dst) { 
    $dir = opendir($src); 
    try{
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    my_mapped_location_copy_directory($src . '/' . $file,$dst . '/' . $file); 
                } else { 
                    copy($src . '/' . $file,$dst . '/' . $file);
                } 
            }   
        }
        closedir($dir); 
    } catch(Exception $ex) {
        return false;
    }
    return true;
}

?>