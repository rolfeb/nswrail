/*
 * Copyright (c) 2018. Rolfe Bozier
 */

let map;
function initMapCB()
{
    let geox = parseFloat(document.getElementById("map-geox").value);
    let geoy = parseFloat(document.getElementById("map-geoy").value);
    let scale = parseInt(document.getElementById("map-scale").value);

    let latlong = new google.maps.LatLng(geoy, geox);

    // instantiate the map
    map = new google.maps.Map(document.getElementById('googleMap'), {
        center: latlong,
        zoom: scale
    });

    // custom icon for the location
    let icon = new google.maps.MarkerImage(
        '/media/images/crosshair.png',
        new google.maps.Size(63, 63),   // size
        new google.maps.Point(0, 0),    // origin
        new google.maps.Point(32, 32)   // anchor
    );

    // add marker for this location
    let marker = new google.maps.Marker({
        map: map,
        position: latlong,
        icon: icon,
    });
}
