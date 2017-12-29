<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("links.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$links = array(
    array(
        "http://parishmaps.lands.nsw.gov.au/pmap.html",
        "Parish Maps (NSW Department of Lands)",
        "The online parish map project provides access to many thousands of scanned parish maps"
    ),
    array(
        "http://www.gnb.nsw.gov.au/name/search",
        "Parish Name Search (NSW Geographic Names Register)",
        "A search form that returns the parish name for any geographic name in NSW. Useful for querying the Parish Maps site"
    ),
    array(
        "http://maps.nsw.gov.au/viewer.htm",
        "Lands GeoSpatial Portal (NSW Department of Lands)",
        "An online portal for accessing topographic map data for NSW"
    ),
    array(
         "http://maps.google.com/?ll=-32.62087,147.282715&spn=10.005571,12.502441&t=k",
        "NSW Satellite Images (Google Local)",
        "Google Local provides satellite images for NSW at various resolutions",
    ),
    array(
        "http://iplan.australis.net.au/landview.php",
        "LandView (NSW Dept of Infrastructure, Planning & Natural Resources",
        "An online portal for accessing aerial photographs for NSW (requires IE)",
    ),


);

foreach ($links as $l)
{
    $t->setCurrentBlock("LINK");
    $t->setVariable("URL", urlenc($l[0]));
    $t->setVariable("TEXT", $l[1]);
    $t->setVariable("DESCRIPTION", $l[2]);
    $t->parseCurrentBlock();
}


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Map and Image Links");
$t->parseCurrentBlock();

$t->show();

exit;

?>
