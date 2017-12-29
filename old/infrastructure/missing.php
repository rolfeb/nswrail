<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("missing.tpl");
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
        L.type,
        L.status,
        L.distance,
        count(LP.seqno)
    from
        r_line_location RL,
        r_line R,
        r_location L
            left outer join r_location_photo LP on
                LP.location_state = L.location_state
                and
                LP.location_name = L.location_name
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
            L.type is null or L.type = 'unknown'
            or
            L.status is null or L.status = 'unknown'
            or
            L.distance is null
        )
    group by
        RL.line_state,
        RL.line_name,
        R.description,
        L.location_state,
        L.location_name
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
    list($line_state, $line, $description, $location_state, $location, $type,
        $status, $distance, $nphotos) = $row;

    if (!$type)
        $type = "unknown";
    if (!$status)
        $status = "unknown";

    if ($nphotos == 0)
        $nphotos = "";

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
    $t->setVariable("TYPE", $type == "unknown" ? "No" : "-");
    $t->setVariable("STATUS", $status == "unknown" ? "No" : "-");
    $t->setVariable("DISTANCE", $distance ? "-" : "No");
    $t->setVariable("PHOTOS", $nphotos);
    $t->parseCurrentBlock();
    $t->parse("LINE-OR-LOCATION");

    $prev_line_name = $line;
}
mysql_free_result($stmt);

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Missing Data");
$t->parseCurrentBlock();

$t->show();

exit;

?>
