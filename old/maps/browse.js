var map;
var Locations = new Array();
var AjaxLocationsReq;
var AjaxRequest;

function loadMap(wx, wy, scale)
{
    if (GBrowserIsCompatible())
    {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
        map.addControl(new GScaleControl());
        map.setCenter(new GLatLng(wy, wx), scale);

        GEvent.addListener(map, "moveend", function() {
            _redrawMarkers();
        });

        // start download of location data
        var url = "/maps/locations-aj.php";
        url += "?key=" + new Date().getTime();  // defeat browser cache
        AjaxLocationsReq = _newRequest();
        AjaxLocationsReq.open("GET", url, true);
        AjaxLocationsReq.onreadystatechange = _loadLocations;
        AjaxLocationsReq.send(null);

        // track the pointer position
        GEvent.addListener(map, "mousemove", function(point) {
            _showPosition(point);
        });

        if (devel_mode())
        {
            if (mode == "edit")
                AjaxRequest = _newRequest();
        }
    }
}

// redraw markers after a map pan/zoom
function _redrawMarkers()
{
    var scale = map.getZoom();
    var window = map.getBounds();

    for (var i = 0; i < Locations.length; i++)
    {
        var location = Locations[i];

        if (location.marker)
        {
            if (scale < 11 || !window.contains(location.point))
            {
                map.removeOverlay(location.marker);
                Locations[i].marker = null;
            }
        }
        else
        if (scale >= 11 && window.contains(location.point))
        {
            location.marker = _createMarker(location);
            map.addOverlay(location.marker);
            Locations[i].marker = location.marker;
        }
    }
}

// create a new marker symbol
function _createMarker(location)
{
    var icon = new GIcon();
    if (location.type == "Junction" || location.type == "Tunnel"
            || location.type == "Border" || location.type == "Crossing"
            || location.type == "Dead End")
        icon.image = "images/grey-pin.png";
    else
    if (location.status == "In Use")
        icon.image = "images/green-pin.png";
    else
        icon.image = "images/red-pin.png";
    icon.iconSize = new GSize(20, 60);
    icon.iconAnchor = new GPoint(0, 60);

    icon.shadow = "images/shadow.png";
    icon.shadowSize = new GSize(60, 60);


    var marker = new PdMarker(location.point, icon);

    marker.setTooltip(location.name);
    marker.setDetailWinHTML(location.html);

    return marker;
}

// update the current pointer position
function _showPosition(point)
{
    var lon = point.x.toFixed(5);
    var lat = point.y.toFixed(5);

    document.getElementById("lon").textContent = lon;
    document.getElementById("lat").textContent = lat;
}

function _handleMarkerMove(marker, seq)
{
    var state = Locations[seq].state;
    var name = Locations[seq].name;
    var point = marker.getPoint();
    var wx = point.lng();
    var wy = point.lat();

    Locations[seq].wx = wx;
    Locations[seq].wy = wy;

    // send a synchronous request to the server
    var params = "";
    params = "?name=" + escape(state) + ":" + escape(name) +
        "&wx=" + escape(wx) +
        "&wy=" + escape(wy);

    var url = "http://nswrail-proto/locations/update-coords.php" + params;

    AjaxRequest.open("GET", url, false);

    // send and wait for a response
    AjaxRequest.send(null);

    if (req.status != 200)
    {
        alert("There was a communications error: " + req.responseText);
    }
    else
    {
        var xml = req.responseXML;
        if (xml)
        {
            var update = xml.getElementsByTagName("update");
            if (update.length == 0)
            {
                // missing <update> element
                alert("Update failed: " + req.responseText);
            }
            else
            {
                update = update[0];
                var status = update.attributes.getNamedItem("status").value;
                if (status != 0)
                {
                    // failed on the server side
                    var message =
                        update.attributes.getNamedItem("message").value;
                    alert("Update failed: " + message);
                }
                else
                {
                    // success!
                    GLog.write(name + " updated on server.");
                }
            }
        }
        else
        {
            // failed to get an XML response
            alert("Update failed: " + req.responseText);
        }
    }
}

// ajax callback to load locations
function _loadLocations()
{
   if (AjaxLocationsReq.readyState != 4)
           return;

    var rows = AjaxLocationsReq.responseText.split("\n");

    var list = new Array();
    for (var i = 0; i < rows.length; i++)
    {
        l = rows[i].split(",");

        list.push(new Location(l[0], l[1], l[2], l[3], l[4], l[5], l[6], l[7], l[8], l[9]));
    }

    Locations = list;

    _redrawMarkers();
}

// constructor
function Location(state, name, type, status, distance, wx, wy, authority, nphotos, photo)
{
    this.state = state;
    this.name = name;
    this.status = status;
    this.type = type;
    this.distance = distance;
    this.point = new GLatLng(wy, wx);
    this.authority = authority;
    this.nphotos = nphotos;
    this.photo = photo;

    var detail_url = "/locations/show.php?name=" + state + ":" + name;
    var photo_url = "/locations/photos/small/" + photo;

    this.html = "<b>" + name + "</b>"                           + "<br/>";
    this.html += "<b>Type:</b> " + type + " (" + status + ")"   + "<br/>";
    this.html += "<b>Distance:</b> " + distance                 + "<br/>";
    this.html += "<b>Photos:</b> " + nphotos                    + "<br/>";
    this.html += "<img src=\"" + photo_url + "\" />"            + "<br/>";
    this.html += "<a href=\"" + detail_url + "\" target=\"details\">details</a>";

    this.marker = null;
    this.visible = false;
}

function devel_mode()
{
    if (location.host == "nswrail-proto")
        return true;

    return false;
}
