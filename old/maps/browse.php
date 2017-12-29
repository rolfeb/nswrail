<?php

require_once "../init.inc";
require_once "../util.inc";

$pos = quote_external(get_post("pos"));     /* optional */
$mode = quote_external(get_post("mode"));   /* optional */

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("browse.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

/* default is whole of NSW */
$initial_wx = 147.28;
$initial_wy = -32.62;
$initial_scale = 6;

if ($pos)
{
    list($initial_wx, $initial_wy) = explode(",", $pos);
    $initial_scale = 14;
}

add_markers();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Map browser");
$t->setVariable("GMAPKEY", gmapkey("maps"));
$t->setVariable("INITIAL-WX", $initial_wx);
$t->setVariable("INITIAL-WY", $initial_wy);
$t->setVariable("INITIAL-SCALE", $initial_scale);
$t->setVariable("MODE", $mode);
$t->parseCurrentBlock();

$t->show();

exit;

function add_markers()
{
    global $db, $t;

    $stmt = mysql_query("
        select
            L.location_state,
            L.location_name,
            L.type,
            L.status,
            L.geo_x,
            L.geo_y
        from
            r_location L
        where
            L.geo_x is not null
            and
            L.geo_y is not null
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $n = 0;
    while ($row = mysql_fetch_array($stmt))
    {
        list($state, $location, $type, $status, $wx, $wy) = $row;

        $t->setCurrentBlock("NEW-MARKER");
        $t->setVariable("MARKER-SEQ", $n++);
        $t->setVariable("MARKER-WX", $wx);
        $t->setVariable("MARKER-WY", $wy);
        $t->setVariable("MARKER-STATE", $state);
        $t->setVariable("MARKER-NAME", locn_fulltitle($location, $type));
        $t->setVariable("MARKER-STATUS", locn_status2text($status));
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
}

?>
