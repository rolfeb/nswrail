<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("rpt-no_location.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$STATE = "NSW";

$stmt = mysql_query("
    select distinct
        RL.line_state,
        RL.line_name,
        R.description,
        L.location_state,
        L.location_name,
        L.distance
    from
        r_line_location RL,
        r_line R,
        r_location L
    where
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
        and
        RL.location_state = L.location_state
        and
        RL.location_name = L.location_name
        and
        L.location_state = '$STATE'
        and
        (
            L.geo_x is null or L.geo_y is null
        )
    order by
        R.line_state,
        R.description,
        RL.segment,
        RL.seqno
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$prev_line_name = "";
while ($row = mysql_fetch_array($stmt))
{
    list($line_state, $line, $description, $location_state, $location,
        $distance) = $row;

    if (!$distance)
        $distance = "?";

    if ($line != $prev_line_name)
    {
        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE-URL", "/lines/show.php?"
            . urlenc("name=$line_state:$line"));
        $t->setVariable("LINE-TEXT", $description);
        $t->parseCurrentBlock();
        $t->parse("LINE-OR-LOCATION");
    }

    $t->setCurrentBlock("LOCATION");
    $t->setVariable("LOCATION-URL", "/locations/show.php?"
            . urlenc("name=$location_state:$location"));
    $t->setVariable("LOCATION-TEXT", $location);
    $t->setVariable("DISTANCE", $distance);
    $t->parseCurrentBlock();
    $t->parse("LINE-OR-LOCATION");

    $prev_line_name = $line;
}
mysql_free_result($stmt);

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Missing Geographic Locations");
$t->parseCurrentBlock();

$t->show();

exit;

?>
