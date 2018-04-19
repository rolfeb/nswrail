<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require_once "core.inc";

$intro = "";

$links = array(
array(
    "http://www.arhsnsw.com.au/",
    "Australian Railway Historic Society (NSW)",
    "The NSW division of the ARHS published Railway Digest and Australian Railway History, and has an excellent bookshop.",
),
array(
    "http://www.arhsnsw.com.au/resource.htm",
    "ARHS Railway Resource Centre",
    "The ARHS RRC maintains a huge collection of materials related to the NSW railways",
),
array(
    "http://www.railpage.com.au/forums/viewforum.php?f=2",
    "RailPage Forum: NSW",
    "The RailPage forum covering NSW issues.",
),
array(
    "http://members.tripod.com/nswrail/",
    "NSW Rail Historical Timetables",
    "Pages containing timetables and maps for the last 20 or so years."
),
array(
    "http://home.primus.com.au/hurste/rail/nsw.htm",
    "Tunnels",
    "The NSW section of the Tunnels Project (Roderick Smith/Steven Hurst)",
),
array(
    "http://locopage.railpage.org.au/",
    "LocoPage",
    "This is a comprehensive reference of diesel and electric locomotives in
    of Australia, including NSW railways.",
),
array(
    "http://zap.to/nswssmrc/",
    "NSW School Students' Model Railway Club",
    "This site includes various photos from around the state.",
),
);

show_links("General NSW Railway Links", $intro, $links);
?>
