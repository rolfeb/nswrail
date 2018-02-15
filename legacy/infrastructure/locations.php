<?php

require_once "site.inc";
require_once "dbutil.inc";

$title = "Railway Locations";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("locations.tpl");

$STATE = "NSW";

$stmt = $db->stmt_init();
$stmt->prepare("
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
        L.location_state = ?
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
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $STATE);
$stmt->execute();
$stmt->bind_result($line_state, $line_name, $description, $segment, $seqno,
    $location_name, $type, $status, $distance);

$n = 0;
$current_first_letter = '';
$index_letters = array();

while ($stmt->fetch())
{
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
$stmt->close();

foreach ($index_letters as $letter)
{
    $t->setCurrentBlock("INDEX");
    $t->setVariable("LETTER", $letter);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
