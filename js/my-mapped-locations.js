jQuery(document).ready(function() {
  initialize_gmaps();
});

var map = new Array();
var geocoder = new google.maps.Geocoder;
var infoWindow = new google.maps.InfoWindow;
// Index DOM elements to their marker.
var gmap_markers = {};
var old_where_id = null;
var new_where_id = null;
jQuery(document).delegate("ul.locations li", "click", function() {
  new_where_id = jQuery(this).attr("id");
  if (new_where_id != old_where_id) {
    map[jQuery(this).parent().attr("map")].setZoom(17);
    map[jQuery(this).parent().attr("map")].setCenter(gmap_markers[jQuery(this).attr("id")].getPosition());
    map[jQuery(this).parent().attr("map")].panTo(gmap_markers[jQuery(this).attr("id")].getPosition());
    markerClick(gmap_markers[jQuery(this).attr("id")]);
    old_where_id = new_where_id;
  }
});

markerClick = function(marker_location) {
  // Ensure that hover will position over previous marker when something else is clicked.
  old_where_id = null;
  var markerLatLng = marker_location.getPosition();
  var title = "";
  if (marker_location.website != "") {
    title = "<span class='title'><a href='" + marker_location.website + "'>" + marker_location.title + "</a></span>";
  } else {
    title = "<span class='title'>" + marker_location.title + "</span>";
  }
  var content = "<div id='pop_up'>" + title;
  if (marker_location.image_url != "") {
    content += "<a href='" + marker_location.website + "'><img class='image' src='" + marker_location.image_url + "' /></a>";
  }
  content += "<span class='wrapper'>";
  if (marker_location.location != "") {
    content += "<span class='address'>" + marker_location.location + "</span>";
  }
  content += "<span><a href='" + marker_location.website + "'>Find out more</a></span>";
  content += "</span>";
  content += "<br /></div>";
  infoWindow.setContent(content);
  infoWindow.setPosition(markerLatLng);
  infoWindow.open(map[marker_location.map_id]);
};

function initialize_gmaps() {
  for (var map_i=0;map_i<jQuery("input[name=gmap_name]").length;map_i++) {
    var google_map_name = jQuery("input[name=gmap_name]")[map_i].value.replace(" ", "_").toLowerCase();
    var zoom_level = 15;
    if(jQuery("input[name="+google_map_name+"_gmap_lat]").length > 0 && jQuery("input[name="+google_map_name+"_gmap_lng]").length > 0 && jQuery("input[name="+google_map_name+"_gmap_location]").length > 0) {
      var latlng = new google.maps.LatLng(jQuery("input[name="+google_map_name+"_gmap_lat]:first").val(), jQuery("input[name="+google_map_name+"_gmap_lng]:first").val());
      var myOptions = {
        zoom: zoom_level,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false,
        streetViewControl: false,
        mapTypeControl: false
      };
      map[google_map_name] = new google.maps.Map(document.getElementById(google_map_name), myOptions);
      var bounds = new google.maps.LatLngBounds();
      for (var i=0;i<jQuery("input[name="+google_map_name+"_gmap_lng]").length;i++) {
        var local_latlng = new google.maps.LatLng(jQuery("input[name="+google_map_name+"_gmap_lat]")[i].value, jQuery("input[name="+google_map_name+"_gmap_lng]")[i].value);
        bounds.extend(local_latlng);
        var marker = new google.maps.Marker({
          map_id: google_map_name,
          map: map[google_map_name],
          position: local_latlng,
          title: jQuery("input[name="+google_map_name+"_gmap_title]")[i].value,
          location: jQuery("input[name="+google_map_name+"_gmap_location]")[i].value,
          phone: jQuery("input[name="+google_map_name+"_gmap_phone]")[i].value,
          website: jQuery("input[name="+google_map_name+"_gmap_website]")[i].value,
          image_url: jQuery("input[name="+google_map_name+"_gmap_image]")[i].value
        });
        google.maps.event.addListener(marker, 'click', function() {markerClick(this);});
        gmap_markers[google_map_name + jQuery("input[name="+google_map_name+"_gmap_title]")[i].value.replace(" ", "_").toLowerCase()] = marker;
      }
      var thismap = map[google_map_name];
      google.maps.event.addListener(thismap, 'zoom_changed', function() {
        zoomChangeBoundsListener = 
          google.maps.event.addListener(thismap, 'bounds_changed', function(event) {
              if (this.getZoom() > zoom_level && this.initialZoom == true) {
                // Change max/min zoom here
                this.setZoom(zoom_level);
                this.initialZoom = false;
              }
          google.maps.event.removeListener(zoomChangeBoundsListener);
        });
      });
      thismap.initialZoom = true;
      thismap.fitBounds(bounds);       
    }
  }
}