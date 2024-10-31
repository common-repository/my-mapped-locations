<?php
	
require_once("admin/controllers/mymapstyling_controller.php");
require_once("admin/pages/mymapstyling_page.php");

if (are_my_mapped_locations_dependencies_installed()) {
	add_action("admin_init", "register_my_mapped_location_style_scripts");
	function register_my_mapped_location_style_scripts() {
		wp_enqueue_script('jquery');
		wp_register_script( 'my-form-script', plugins_url('/admin_js/jquery.form.js', __FILE__) );
		wp_register_script("admin-my-mapped-locations", plugins_url("admin_js/application.js", __FILE__));
	  wp_enqueue_script("admin-my-mapped-locations");

	  wp_register_style("admin-my-mapped-locations", plugins_url("admin_css/style.css", __FILE__));
	  wp_enqueue_style("admin-my-mapped-locations");

		wp_localize_script( 'admin-my-mapped-locations', 'MyMappedLocationsAjax', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}
	
	if (isset($_POST["action"]) && $_POST["action"] == "Reset") {
		my_mapped_location_copy_directory(LocationPath::normalize(dirname(__FILE__)."/css"), get_template_directory());  		
	}
		
	MyMapStylingController::indexAction();

	?>
<?php } ?>