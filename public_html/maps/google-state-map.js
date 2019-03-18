/*
 * Copyright (c) 2019 Rolfe Bozier
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

    /*
     * Start an AJAX request to get locations for markers
     */
    let url = "/maps/dump-locations-json.php" +
            "?key=" + new Date().getTime();  // defeat browser cache

    let req = aj_new_request();
    req.open("GET", url, true);
    req.onreadystatechange = function() { load_markers(req); };
    req.send(null);

    /*
     * Add an event listener for extent changes, to update the markers
     */
    google.maps.event.addListener(map, "bounds_changed",
        function(ev) { redraw_markers(); });
}


/*
 * A list of all Location objects
 */
let AllLocations    = [];

/*
 * Constructor for Location objects
 */
function Location(item)
{
    this.state          = item['state'];
    this.name           = item['name'];
    this.type           = item['type'];
    this.status         = item['status'];
    if (item['distance'])
        this.distance   = parseFloat(item['distance']);
    else
        this.distance   = "unknown";
    this.point          = new google.maps.LatLng(parseFloat(item['geo_y']), parseFloat(item['geo_x']));
    this.geo_authority  = item['geo_exact'];
    this.nphotos        = item['nphotos'];
    this.thumbnail      = item['thumbnail'];

    let url = "/locations/details.php?name=" + this.state + ":" + this.name;

    this.html = '';

    this.html += "<div class='infowindow'>";
    this.html += "<h1>" + this.name + "</h1>";

    if (this.thumbnail)
        this.html += "<img src='/media/photos/thumbnails/" + this.thumbnail + "' />";
    this.html += "<table class='simple'>";
    this.html += "<tr><th>Type:</th><td>" + this.type + "</td></tr>";
    this.html += "<tr><th>Status:</th><td>" + this.status + "</td></tr>";
    this.html += "<tr><th>Distance:</th><td>" + this.distance + " km</td></tr>";
    this.html += "<tr><th>Photos:</th><td>" + this.nphotos + "</td></tr>";
    this.html += "</table>";
    this.html += "<a href='" + url + "'>details</a>";
    this.html += "</div>";

    this.marker = null;
    this.visible = false;
}


/*
 * Callback function to load markers from an AJAX request
 */
function load_markers(req)
{
    if (req.readyState !== 4)
        return;

    /*
     * Parse locations and create a list of Location objects
     */
    dump = jsonParse(req.responseText);

    for (let i = 0; i < dump.length; i++)
        AllLocations.push(new Location(dump[i]));

    /*
     * Redraw markers
     */
    redraw_markers();
}

/*
 * Redraw the markers for the current map location
 */
function redraw_markers()
{
    let scale = map.getZoom();
    let window = map.getBounds();

    for (let i = 0; i < AllLocations.length; i++)
    {
        let l = AllLocations[i];

        lng = l.point.lng();
        lat = l.point.lat();

        if (l.marker)
        {
            if (scale < 11 || !window.contains(l.point))
            {
                l.marker.setMap(null);
                AllLocations[i].marker = null;
            }
        }
        else
        if (scale >= 11 && window.contains(l.point))
        {
            l.marker = create_marker(l);
            l.marker.setMap(map);
            AllLocations[i].marker = l.marker;
        }
    }
}

function create_marker(location)
{
    if (location.type === "Junction" || location.type === "Tunnel"
            || location.type === "Border" || location.type === "Crossing"
            || location.type === "Dead End") {
        url = "/c/images/grey-pin.png";
    }
    else
    if (location.status === "In Use" || location.status.startsWith('Opened')) {
        url = "/c/images/green-pin.png";
    }
    else {
        url = "/c/images/red-pin.png";
    }

    let icon = new google.maps.MarkerImage(
        url,
        new google.maps.Size(20, 60),  /* size */
        new google.maps.Point(0, 0),   /* origin */
        new google.maps.Point(0, 60)   /* anchor */
    );

    let shadow = new google.maps.MarkerImage(
        "/c/images/pin-shadow.png",
        new google.maps.Size(60, 60),  /* size */
        new google.maps.Point(0, 0),   /* origin */
        new google.maps.Point(0, 60)   /* anchor */
    );

    let marker = new google.maps.Marker({
        position:   location.point,
        icon:       icon,
        shadow:     shadow,
        title:      location.name + " (click for details)"
    });

    let infowindow = new google.maps.InfoWindow({
        content: location.html
    });

    google.maps.event.addListener(marker, 'click',
        function() { infowindow.open(map, marker); });

    return marker;
}

