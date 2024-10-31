jQuery(function() {

  if (jQuery( "#location_column" ).length > 0) {
    jQuery( "#location_column table tbody" ).sortable({
      update: function( event, ui ) {
        jQuery("#location_column table tbody tr").each(function() {
          jQuery.ajax({
            type: 'POST',
            url: MyMappedLocationsAjax.sort_locations_url,
            data: {LID: jQuery.trim(jQuery(this).find(".lid").html()), location_order: jQuery(this).index(), action: "update_order"}
          });
        });

        jQuery("table.data tr").removeClass("even").removeClass("odd");
        jQuery("table.data tr:odd").addClass("odd");
        jQuery("table.data tr:even").addClass("even");
      }
    });    
  }

  jQuery(".gmap-webpage-control").click(function() {
    jQuery.colorbox({inline:true, href:"#control_gmap_page_post_container", width: "730px", height: "550px"});
  });

  jQuery(".gmap-image-uploader").click(function() {
    jQuery.colorbox({inline:true, href:"#upload_gmap_image_container", width: "730px", height: "550px"});
  });

  jQuery("#css_file_selection").change(function() {
    jQuery.ajax({
      type: "post",url: MyMappedLocationsAjax.ajax_url,data: {css_file_selection: jQuery("#css_file_selection").val(), action: "my_mapped_location_css_file_selector"},
      success: function(response){
        jQuery("#css_content").html(response);
      }
    });
  });

  jQuery(document).delegate("#filter_gmap_image_name", "keydown", function() {
      if (jQuery(this).val().length < 2) {
        jQuery("#gmap_images_container").html("");
      } else {
        jQuery.post(MyMappedLocationsAjax.base_url+"/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php", { filter_gmap_image_name: jQuery(this).val() },
            function(data) {
              jQuery("#gmap_images_container").html(data);
            }
        );
      }
  });

  jQuery(document).delegate("#gmap_page_posts a", "click", function() {
    jQuery("#gmap_website").val(jQuery(this).attr("href"));
    jQuery("#cboxClose").click();
    return false;
  });

  jQuery(document).delegate("#filter_gmap_page_post_name", "keydown", function() {
      if (jQuery(this).val().length < 2) {
        jQuery("#gmap_page_post_container").html("");
      } else {
        jQuery.post(MyMappedLocationsAjax.base_url+"/wp-admin/admin.php?page=my-mapped-locations/my-mapped-locations.php", { filter_gmap_page_post_name: jQuery(this).val() },
            function(data) {
              jQuery("#gmap_page_posts_container").html(data);
            }
        );
      }
  });

  jQuery(document).delegate("#gmap_images img", "click", function() {
    jQuery("#gmap_image").val(jQuery(this).attr("src"));
    jQuery("#cboxClose").click();
  });

	if (jQuery("#upload_gmap_image_form").length > 0) {
	  var bar = jQuery('.bar');
	  var percent = jQuery('.percent');
	  jQuery(".percent").hide();
	  jQuery('#upload_gmap_image_form').ajaxForm({
	      beforeSend: function() {
	          jQuery(".percent").hide();
	          var percentVal = '0%';
	          bar.width(percentVal)
	          percent.html(percentVal);
	      },
	      uploadProgress: function(event, position, total, percentComplete) {
	          jQuery(".percent").show();
	          var percentVal = percentComplete + '%';
	          bar.width(percentVal)
	          percent.html(percentVal);
	      },
	      complete: function(xhr) {
	          jQuery(".percent").hide();
	          jQuery("#filter_gmap_image_name").val(jQuery("#upload_gmap_image").val().match("([0-9|a-z|A-Z]|\.|-|_)*$")[0]);
	          jQuery("#filter_gmap_image_name").keydown();
	      }
	  });
	} 
  
  jQuery("table.data tr:odd").addClass("odd");
  jQuery("table.data tr:even").addClass("even");

});