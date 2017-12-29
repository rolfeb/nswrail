<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("closed_sydney_stations.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$stmt = mysql_query("
    select
        R.line_state,
        R.line_name,
        R.description,
        R.region,
        RL.seqno,
        L.location_state,
        L.location_name,
        L.type,
        L.status
    from
        r_line R,
        r_line_location RL,
        r_location L
    where
        R.line_state = 'NSW'
        and
        (
            (
                R.region = 'SY'
                and
                R.line_name != 'south_coast'
            )
            or
            (
                R.line_name = 'main_north'
                and
                RL.seqno <= 20      -- Cowan
            )
            or
            (
                R.line_name = 'main_west'
                and
                RL.seqno <= 22      -- Emu Plains
            )
            or
            (
                R.line_name = 'main_south'
                and
                RL.seqno <= 43      -- Macarthur
            )
            or
            (
                R.line_name = 'south_coast'
                and
                RL.seqno <= 38      -- Otford
            )
        )
        and
        RL.line_state = R.line_state
        and
        RL.line_name = R.line_name
        and
        RL.segment = 0
        and
        RL.mainline = 'Y'
        and
        L.location_state = RL.location_state
        and
        L.location_name = RL.location_name
        and
        L.status in ( 'closed', 'reused', 'not opened', 'unknown' )
        and
        L.type in ( 'station', 'platform', 'halt', 'unknown' )
    order by
        R.region desc,
        R.description,
        RL.seqno
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$curr_line_name = "";
while ($row = mysql_fetch_array($stmt))
{
    list($line_state, $line_name, $description, $region, $seqno,
        $location_state, $location_name, $type, $status) = $row;

    if ($curr_line_name != $line_name)
    {
        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE-URL", "/lines/show.php?"
            . urlenc("name=$line_state:$line_name"));
        $t->setVariable("LINE-TEXT", $description);
        $t->parseCurrentBlock();
    }
    $t->setCurrentBlock("STATION");
    $t->setVariable("LOCATION-URL", "/locations/show.php?"
        . urlenc("name=$location_state:$location_name"));
    $t->setVariable("LOCATION-TEXT", $location_name);
    $t->setVariable("TYPE", locn_type2text($type));
    $t->setVariable("STATUS", locn_status2text($status));
    $t->parseCurrentBlock();

    $curr_line_name = $line_name;
}
mysql_free_result($stmt);


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Closed Sydney Railway Stations");
$t->parseCurrentBlock();

$t->show();

exit;

?>
