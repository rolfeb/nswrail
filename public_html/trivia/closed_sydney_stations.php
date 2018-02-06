<?php

require "site.inc";

$title = "Closed Sydney Railway Stations";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("closed_sydney_stations.tpl");

global $db;

$stmt = $db->stmt_init();
$stmt->prepare("
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
                RL.segment = 0
                and
                RL.seqno <= 20      -- Cowan
            )
            or
            (
                R.line_name = 'main_west'
                and
                RL.segment = 0
                and
                RL.seqno <= 22      -- Emu Plains
            )
            or
            (
                R.line_name = 'main_south'
                and
                RL.segment = 0
                and
                RL.seqno <= 43      -- Macarthur
            )
            or
            (
                R.line_name = 'south_coast'
                and
                (
                    (
                        RL.segment = 0
                        and
                        RL.seqno <= 38      -- Otford
                    )
                    or
                    (
                        RL.segment = 2
                        and
                        RL.seqno <= 8      -- Otford
                    )
                )
            )
        )
        and
        RL.line_state = R.line_state
        and
        RL.line_name = R.line_name
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
")
    or dbi_error_trace("prepare failed");

$stmt->bind_result($line_state, $line_name, $description, $region, $seqno,
    $location_state, $location_name, $type, $status);
$stmt->execute();
$stmt->store_result();

$curr_line_name = "";
$rows = array();
$stations = array();

while ($stmt->fetch())
{
    $row = array($line_state, $line_name, $description, $region, $seqno,
        $location_state, $location_name, $type, $status);

    if (array_key_exists("$line_state:$line_name", $stations))
        $stations["$line_state:$line_name"]++;
    else
        $stations["$line_state:$line_name"] = 1;
    $rows[] = $row;
}
$stmt->close();

foreach ($rows as $row)
{
    list($line_state, $line_name, $description, $region, $seqno,
        $location_state, $location_name, $type, $status) = $row;

    if ($curr_line_name != $line_name)
    {
        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE-URL", "/lines/show.php?"
            . urlenc("name=$line_state:$line_name"));
        $t->setVariable("LINE-TEXT", $description);
        $t->setVariable("NUM-STATIONS", $stations["$line_state:$line_name"]);
        $t->parseCurrentBlock();
    }
    $t->setCurrentBlock("STATION");
    $t->setVariable("LOCATION-URL", "/locations/show.php?"
        . urlenc("name=$location_state:$location_name"));
    $t->setVariable("LOCATION-TEXT", $location_name);
    $t->setVariable("TYPE", locn_type2text($type));

    if ($status == 'not opened')
        $t->setVariable("CLOSURE", "Never opened");
    else
        $t->setVariable("CLOSURE", get_location_event_date_str($location_state, $location_name, 'CN', False));
    $t->parseCurrentBlock();

    $curr_line_name = $line_name;
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
