var my_map, my_marker;
function findMe() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (pos) {
      var myLatlng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
      my_map.setCenter(myLatlng);
      my_marker.setPosition(myLatlng);
      mapChanged();
    });
  }
}
function findLocation() {
  var search = prompt(trans('Enter a place name nearby the location to search'), 'Bangkok');
  if (search !== null && search !== '') {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      address: search
    }, function (results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var myLatlng = results[0].geometry.location;
        my_map.setCenter(myLatlng);
        my_marker.setPosition(myLatlng);
        mapChanged();
      } else {
        alert(trans('Sorry Location not found'));
      }
    });
  }
}
function inintMapDemo() {
  var myLatlng;
  if ($E('map_latigude') && $E('map_latigude')) {
    myLatlng = new google.maps.LatLng($E('map_latigude').value, $E('map_lantigude').value);
  } else {
    myLatlng = new google.maps.LatLng($E('map_info_latigude').value, $E('map_info_lantigude').value);
  }
  var o = {
    zoom: floatval($E("map_zoom").value),
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  my_map = new google.maps.Map($E("map_canvas"), o);
  google.maps.event.addListener(my_map, "zoom_changed", function () {
    var p = my_marker.getPosition();
    my_map.panTo(p);
    mapChanged();
  });
  google.maps.event.addListener(my_map, "dragend", function () {
    mapChanged();
  });
  var info = new google.maps.LatLng($E('map_info_latigude').value, $E('map_info_lantigude').value);
  my_marker = new google.maps.Marker({
    position: info,
    map: my_map,
    draggable: true,
    title: trans('Drag the marker to the location you want')
  });
  google.maps.event.addListener(my_marker, "dragend", function () {
    var p = my_marker.getPosition();
    my_map.panTo(p);
    mapChanged();
  });
  if (navigator.geolocation) {
    $G('find_me').removeClass('hidden');
    callClick("find_me", findMe);
    callClick("map_search", findLocation);
  }
}
function mapChanged() {
  var p = my_marker.getPosition();
  $E("map_info_latigude").value = p.lat();
  $E("map_info_lantigude").value = p.lng();
  var c = my_map.getCenter();
  if ($E('map_latigude')) {
    $E("map_latigude").value = c.lat();
  }
  if ($E('map_lantigude')) {
    $E("map_lantigude").value = c.lng();
  }
  $E("map_zoom").value = my_map.getZoom();
}