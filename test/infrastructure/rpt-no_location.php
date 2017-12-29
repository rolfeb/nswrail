<?php

require_once "site.inc";

$title = "Missing Geographic Locations";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("rpt-no_location.tpl");

$STATE = "NSW";

global $dbi;

$stmt = $dbi->stmt_init();
$stmt->prepare("
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
        L.location_state = ?
        and
        (
            L.geo_x is null or L.geo_y is null
        )
    order by
        R.line_state,
        R.description,
        RL.segment,
        RL.seqno
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $STATE);
$stmt->execute();
$stmt->bind_result($line_state, $line, $description, $location_state,
    $location, $distance);

$prev_line_name = "";
while ($stmt->fetch())
{
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
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
