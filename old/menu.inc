<?php
function menu()
{
    $incoming_photos = "";
    $pending_photos = "";
    $missing_data = "";
    $missing_coords = "";

    if (auth_priv_admin())
    {
        $incoming_photos = "<li class=\"admin\"><a href=\"/photos/incoming.php\">Incoming</a></li>";
        $missing_data = "<li class=\"admin\"><a href=\"/infrastructure/missing.php\">Missing data</a></li>";
        $missing_coords = "<li class=\"admin\"><a href=\"/infrastructure/rpt-no_location.php\">Missing (x, y)</a></li>";
    }

    $html = "
<div id=\"menu\">
<ul id=\"nav\">

<li id=\"first\"><a href=\"/index.html\">Home</a>
</li>

<li><a href=\"#\">Lines</a>
    <ul>
    <li><a href=\"/lines/nsw-map.php\">NSW Map</a></li>
    <li><a href=\"/lines/sydney-map.php\">Sydney Map</a></li>
    <li><a href=\"/lines/newcastle-map.php\">Newcastle Map</a></li>
    <li><a href=\"/lines/nsw-lines.php\">NSW Lines</a></li>
    <li><a href=\"/lines/sydney-lines.php\">Sydney Lines</a></li>
    <li><a href=\"/lines/newcastle-lines.php\">Newcastle Lines</a></li>
    </ul>
</li>

<li><a href=\"#\">Photos</a>
    <ul>
    <li><a href=\"/photos/recent.php\">Newly Added</a></li>
    <li><a href=\"/photos/historic.php\">Historic</a></li>
    <li><a href=\"/photos/signal_box.php\">Signal Boxes</a></li>
    <li><a href=\"/photos/safeworking.php\">Safeworking</a></li>
    <li><a href=\"/photos/diagram.php\">Diagrams</a></li>
    <li><a href=\"/photos/turntable.php\">Turntables</a></li>
    <li><a href=\"/photos/tunnel.php\">Tunnels</a></li>
    <li><a href=\"/photos/night.php\">Night</a></li>
    <li><a href=\"/photos/submissions.php\">Submissions</a></li>
    <li><a href=\"/photos/index.php\">Contributers</a></li>
    $incoming_photos
    </ul>
</li>

<li><a href=\"#\">Maps</a>
    <ul>
    <li><a href=\"/maps/browse.php\">Browse NSW</a></li>
    <li><a href=\"/maps/nsw-network.php\">NSW by year</a></li>
    <li><a href=\"/maps/sydney-network.php\">Sydney by year</a></li>
    <li><a href=\"/maps/newcastle-network.php\">Newcastle by year</a></li>
    <li><a href=\"/maps/nsw-growth.php\">NSW growth</a></li>
    <li><a href=\"/maps/sydney-growth.php\">Sydney growth</a></li>
    <li><a href=\"/maps/newcastle-growth.php\">Newcastle growth</a></li>
    <li><a href=\"/maps/nsw-1933.php\">NSW in 1933</a></li>
    <li><a href=\"/maps/images/syd-network1969.gif\">Sydney in 1969</a></li>
    <li><a href=\"/maps/sydney-1974.php\">Sydney in 1974</a></li>
    <li><a href=\"/maps/images/newcastle1950.gif\">Newcastle in 1950</a></li>
    <li><a href=\"/maps/turning-facilities.php\">NSW turning facilities</a></li>
    </ul>
</li>

<li><a href=\"#\">Infrastructure</a>
    <ul>
    <li><a href=\"/infrastructure/timeline.php\">Timeline</a></li>
    <li><a href=\"/infrastructure/triangle.php\">Triangles</a></li>
    <li><a href=\"/infrastructure/tunnel.php\">Tunnels</a></li>
    <li><a href=\"/infrastructure/turntable.php\">Turntables</a></li>
    <li><a href=\"/infrastructure/locations.php\">All Locations</a></li>
    $missing_data
    $missing_coords
    </ul>
</li>

<li><a href=\"#\">Trivia</a>
    <ul>
    <li><a href=\"/trivia/short_lived_sections.php\">Short-lived Sections</a></li>
    <li><a href=\"/trivia/never_completed.php\">Lines not Completed</a></li>
    <li><a href=\"/trivia/other_gauge.php\">Non-Standard Gauge</a></li>
    <li><a href=\"/trivia/spirals.php\">Railway Spirals</a></li>
    <li><a href=\"/trivia/altitude.php\">Highs and Lows</a></li>
    <li><a href=\"/trivia/formally_closed.php\">Formally Closed Lines</a></li>
    <li><a href=\"/trivia/closed_sydney_stations.php\">Closed Sydney Stations</a></li>
    <li class=\"enclose\"><a href=\"/trivia/track_changes.php\">Track&nbsp;Length&nbsp;Changes</a></li>
    </ul>
</li>

<li><a href=\"#\">Library</a>
    <ul>
    <li><a href=\"/library/planned.php\">Planned Lines</a></li>
    <li><a href=\"/library/station_names.php\">Station Names</a></li>
    <li><a href=\"/library/bulletin.php\">ARHS Bulletin Index</a></li>
    <li><a href=\"/library/signalling.php\">Signalling</a></li>
    <li><a href=\"/library/smr.php\">South Maitland Railway</a></li>
    <li><a href=\"/library/widemere.php\">Widemere Quarry Line</a></li>
    <li><a href=\"/library/lithgow-zigzag.php\">Lithgow Zig-Zag</a></li>
    </ul>
</li>

<li><a href=\"#\">Links</a>
    <ul>
    <li><a href=\"/links/index.php\">General</a></li>
    <li><a href=\"/links/locations.php\">Locations</a></li>
    <li><a href=\"/links/photos.php\">Photographs</a></li>
    <li><a href=\"/links/operators.php\">Operators</a></li>
    <li><a href=\"/links/preservation.php\">Preservation</a></li>
    <li><a href=\"/links/maps.php\">Maps</a></li>
    </ul>
</li>

<li id=\"last\"><a href=\"#\">About</a>
    <ul>
    <li><a href=\"/about/index.php\">This Site</a></li>
    <li><a href=\"/about/changelog.php\">Change Log</a></li>
    <li><a href=\"/about/credits.php\">Credits</a></li>
    </ul>
</li>

</ul>
</div>
    ";
    return $html;
}

?>
