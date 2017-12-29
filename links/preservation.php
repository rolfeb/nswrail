<?php

require_once "core.inc";

$intro = "";

$links = array(
array(
    "http://www.ilrms.com.au/",
    "Wollongong: Illawarra Light Railway Museum Society",
    "The ILMRS was established to exhibit light railway locos and rolling
    stock, was emphasis on the Illawarra region of NSW.",
),
array(
    "http://infobluemountains.net.au/locodepot/",
    "Valley Heights Locomotive Depot Heritage Museum",
    "The former locomotive depot has been turned into a museum, preserving
    the buildings and railway heritage of the area.",
),
array(
    "http://www.railpage.org.au/statemine/",
    "Lithgow: State Mine Heritage Park and Railway",
    "The State Mine Colliery Branch ran from Lithgow to the nearby State Mine.
    The site has been turned into a museum, with plans to operate trains on
    the branch.",
),
);

show_links("NSW Railway Preservation Links", $intro, $links);
?>
