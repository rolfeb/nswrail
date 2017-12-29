/*
 * A list of all Location objects
 */
var AllLocations    = new Array();

/*
 *  The google map obkect
 */
var map;

/*
 * Initialise the Google map on this page
 */
function google_init(lon, lat, zoom)
{
    /*
     * Create a new map at the given location
     */
    var latlong = new google.maps.LatLng(lat, lon);

    var options = {
        zoom: zoom,
        center: latlong,
        mapTypeId: google.maps.MapTypeId.HYBRID
    }

    map_elt = document.getElementById("map");

    map = new google.maps.Map(map_elt, options);

    /*
     * Start an AJAX request to get locations for markers
     */
    var url = "/locations/aj-dump.php" + 
            "?key=" + new Date().getTime();  // defeat browser cache

    var req = aj_new_request();
    req.open("GET", url, true);
    req.onreadystatechange = function() { load_markers(req); };
    req.send(null);

    /*
     * Add an event listener to track the cursor position
     */
    google.maps.event.addListener(map, "mousemove",
        function(ev) { update_coords(ev.latLng); });

    /*
     * Add an event listener for extents changes, to update the markers
     */
    google.maps.event.addListener(map, "bounds_changed",
        function(ev) { redraw_markers(); });
}

/*
 * Update the current cursor position
 */
function update_coords(point)
{
    var lon = point.lng().toFixed(5);
    var lat = point.lat().toFixed(5);

    document.getElementById("lon").textContent = lon;
    document.getElementById("lat").textContent = lat;
}

/*
 * Callback function to load markers from an AJAX request
 */
function load_markers(req)
{
    if (req.readyState != 4)
        return;

    /*
     * Parse locations and create a list of Location objects
     */
    dump = jsonParse(req.responseText);

    for (var i = 0; i < dump.length; i++)
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
    var scale = map.getZoom();
    var window = map.getBounds();

    for (var i = 0; i < AllLocations.length; i++)
    {
        var l = AllLocations[i];

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
    if (location.type == "Junction" || location.type == "Tunnel"
            || location.type == "Border" || location.type == "Crossing"
            || location.type == "Dead End")
        url = "/c/images/grey-pin.png";
    else
    if (location.status == "In Use")
        url = "/c/images/green-pin.png";
    else
        url = "/c/images/red-pin.png";

    var icon = new google.maps.MarkerImage(
        url,
        new google.maps.Size(20, 60),  /* size */
        new google.maps.Point(0, 0),   /* origin */
        new google.maps.Point(0, 60)   /* anchor */
    );

    var shadow = new google.maps.MarkerImage(
        "/c/images/pin-shadow.png",
        new google.maps.Size(60, 60),  /* size */
        new google.maps.Point(0, 0),   /* origin */
        new google.maps.Point(0, 60)   /* anchor */
    );

    var marker = new google.maps.Marker({
        position:   location.point,
        icon:       icon,
        shadow:     shadow,
        title:      location.name + " (click for details)"
    });

    var infowindow = new google.maps.InfoWindow({
        content: location.html
    });

    google.maps.event.addListener(marker, 'click',
        function() { infowindow.open(map, marker); });

    return marker;
}

/*
 * Constructor for Location objects
 */
function Location(arr)
{
    this.state          = arr[0];
    this.name           = arr[1];
    this.type           = arr[2];
    this.status         = arr[3];
    if (arr[4])
        this.distance   = arr[4];
    else
        this.distance   = "unknown";
    this.point          = new google.maps.LatLng(arr[6], arr[5]);
    this.geo_authority  = arr[7];
    this.nphotos        = arr[8];
    this.thumbnail      = arr[9];

    var url = "/locations/show.php?name=" + this.state + ":" + this.name;

    this.html = '';

    this.html += "<div class='infowindow'>";
    this.html += "<h1>" + this.name + "</h1>";

    if (this.thumbnail)
        this.html += "<img src='/locations/photos/small/" + this.thumbnail + "' />";
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
