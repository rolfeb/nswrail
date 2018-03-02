var map;
function initMapCB()
{
    var geox = parseFloat(document.getElementById("map-geox").value);
    var geoy = parseFloat(document.getElementById("map-geoy").value);
    var scale = parseInt(document.getElementById("map-scale").value);

    var latlong = new google.maps.LatLng(geoy, geox);

    // instantiate the map
    map = new google.maps.Map(document.getElementById('googleMap'), {
        center: latlong,
        zoom: scale
    });

    // custom icon for the location
    var icon = new google.maps.MarkerImage(
        '/media/images/crosshair.png',
        new google.maps.Size(63, 63),   // size
        new google.maps.Point(0, 0),    // origin
        new google.maps.Point(32, 32)   // anchor
    );

    // add marker for this location
    var marker = new google.maps.Marker({
        map: map,
        position: latlong,
        icon: icon,
    });
}
