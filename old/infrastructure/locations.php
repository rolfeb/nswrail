<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "dbutil.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("locations.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$STATE = "NSW";

$stmt = mysql_query("
    select
        R.line_state,
        R.line_name,
        R.description,
        RL.segment,
        RL.seqno,
        L.location_name,
        L.type,
        L.status,
        L.distance
    from
        r_line R,
        r_line_location RL,
        r_location L
    where
        L.location_state = '$STATE'
        and
        RL.location_state = L.location_state
        and
        RL.location_name = L.location_name
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
    order by
        L.location_name
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$n = 0;
$current_first_letter = '';
$index_letters = array();

while ($row = mysql_fetch_array($stmt))
{
    list($line_state, $line_name, $description, $segment, $seqno,
        $location_name, $type, $status, $distance) = $row;

    if ($distance != "")
        $distance = sprintf("%.1f km", $distance);
    else
        $distance = "???";

    $first_letter = substr($location_name, 0, 1);
    if ($first_letter != $current_first_letter)
    {
        $t->setCurrentBlock("LOCATION-LINK-DEST");
        $t->setVariable("INDEX-TARGET", $first_letter);
        $t->setVariable("LOCATION-URL", "/locations/show.php?"
            . urlenc("name=$STATE:$location_name"));
        $t->setVariable("LOCATION", $location_name);
        $t->parseCurrentBlock();

        $current_first_letter = $first_letter;
        $index_letters[] = $first_letter;
    }
    else
    {
        $t->setCurrentBlock("LOCATION-NONLINK-DEST");
        $t->setVariable("LOCATION-URL", "/locations/show.php?"
            . urlenc("name=$STATE:$location_name"));
        $t->setVariable("LOCATION", $location_name);
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock("LOCATION");
    $t->setVariable("CLASS", $n % 2 == 0 ? "value" : "highlight");
    $t->setVariable("TYPE", locn_type2text($type));
    $t->setVariable("STATUS", locn_status2text($status));
    $t->setVariable("DISTANCE", $distance);
    $t->setVariable("LINE-URL", "/lines/show.php?"
        . urlenc("name=$STATE:$line_name"));
    $t->setVariable("LINE", $description);
    $t->parseCurrentBlock();

    $n++;
}
mysql_free_result($stmt);

foreach ($index_letters as $letter)
{
    $t->setCurrentBlock("INDEX");
    $t->setVariable("LETTER", $letter);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Railway Locations");
$t->parseCurrentBlock();

$t->show();

exit;

?>
