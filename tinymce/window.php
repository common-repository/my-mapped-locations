<?php

if ( !defined('ABSPATH') )
    die('You are not allowed to call this page directly.');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>My Mapped Locations</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.core.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.widget.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-content/plugins/my-mapped-locations/tinymce/tinymce.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url("/css/style.css", __FILE__); ?>" media="all" />

  <base target="_self" />
</head>

<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
	
	<div class="panel_wrapper">
		<?php
		global $wpdb;
		$my_mapped_location_maps_table = $wpdb->prefix."my_mapped_location_maps";
		$my_mapped_location_maps = $wpdb->get_results("SELECT * FROM $my_mapped_location_maps_table");
		?>
		<p><label for="my_mapped_location">My Mapped Location</label> 
			<select id="my_mapped_location" name="my_mapped_location">
			<option value=""></option>
			<?php foreach ($my_mapped_location_maps as $my_mapped_location_map) { ?>
				<option value="[my-mapped-location id='<?php echo($my_mapped_location_map->ID); ?>'][/my-mapped-location]"><?php echo($my_mapped_location_map->map_name); ?></option>
			<?php }?>
		</select></p>
		<div class="mceActionPanel">
			<div id="cancel_my_mapped_location">
				<input type="button" id="cancel" name="cancel_my_mapped_location" value="<?php _e("Cancel", 'my_mapped_locations'); ?>" onclick="tinyMCEPopup.close();" />
			</div>
			<div id="insert_my_mapped_location">
				<input type="submit" id="insert" name="insert_my_mapped_location" value="<?php _e("Insert", 'my_mapped_locations'); ?>" onclick="insertMyMappedLocation();" />
			</div>
		</div>
	</div>
</body>
</html>