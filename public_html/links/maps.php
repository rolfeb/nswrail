<?php

require_once "core.inc";

$intro = "";

$links = array(
array(
    "http://parishmaps.lands.nsw.gov.au/pmap.html",
    "Parish Maps (NSW Department of Lands)",
    "The online parish map project provides access to many thousands of scanned parish maps"
),
array(
    "http://www.gnb.nsw.gov.au/name_search",
    "Parish Name Search (NSW Geographic Names Register)",
    "A search form that returns the parish name for any geographic name in NSW. Useful for querying the Parish Maps site"
),
array(
    "http://gsp.maps.nsw.gov.au/",
    "Lands GeoSpatial Portal (NSW Department of Lands)",
    "An online portal for accessing topographic map data for NSW"
),
array(
     "http://maps.google.com/?ll=-32.62087,147.282715&amp;spn=10.005571,12.502441&amp;t=k",
    "NSW Satellite Images (Google Local)",
    "Google Local provides satellite images for NSW at various resolutions",
),
array(
    "http://iplan.australis.net.au/landview.php",
    "LandView (NSW Dept of Infrastructure, Planning & Natural Resources",
    "An online portal for accessing aerial photographs for NSW (requires IE)",
),
);

show_links("NSW Maps and Satellite Images", $intro, $links);
?>
