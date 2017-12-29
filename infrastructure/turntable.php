<?php

require_once "site.inc";

$title = "Railway Turntables";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("turntable.tpl");

$STATE = 'NSW';

global $dbi;

$stmt = $dbi->stmt_init();
$stmt->prepare("
    select
        R.line_state,
        R.line_name,
        R.description,
        if(
            R.region = 'N' or R.region = 'NC' or R.line_name = 'north_coast' or (R.line_name = 'main_north' and RL.seqno > 20),
            'Northern',
            if(
                R.region = 'S' or (R.line_name = 'main_south' and RL.seqno > 43) or
                    (R.line_state = 'NSW' and R.line_name = 'south_coast' and RL.seqno > 38),
                'Southern',
                if(
                    R.region = 'W' or R.line_name = 'broken_hill' or (R.line_name = 'main_west' and RL.seqno > 22),
                    'Western',
                    'Metropolitan'
                )
            )
        ) as region,
        RL.seqno,
        LTT.location_state,
        LTT.location_name,
        LTT.type,
        LTT.size_ft,
        LTT.status,
        LTT.text,
        count(LP.status)
    from
        r_line_location RL,
        r_line R,
        r_location_turntable LTT
            left outer join r_location_photo LP
            on
                LP.location_state = LTT.location_state
                and
                LP.location_name = LTT.location_name
                and
                LP.status = 'Y'
                and
                FIND_IN_SET('turntable', LP.themes)
    where
        LTT.location_state = ?
        and
        RL.location_state = LTT.location_state
        and
        RL.location_name = LTT.location_name
        and
        RL.mainline = 'Y'
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
    group by
        R.line_state,
        R.line_name,
        R.description,
        region,
        RL.seqno,
        LTT.location_state,
        LTT.location_name,
        LTT.type,
        LTT.size_ft,
        LTT.status,
        LTT.text
    order by
        region,
        R.description,
        RL.seqno,
        LTT.seqno
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $STATE);
$stmt->execute();
$stmt->bind_result($line_state, $line_name, $description, $region, $seqno,
    $location_state, $location_name, $type, $size, $status, $notes, $photos);

$type_lookup = array(
    "electric"  => "E",
    "manual"    => "M",
    "unknown"   => "?",
);

$status_lookup = array(
    "in use"        => "In use",
    "out of use"    => "Out of use",
    "closed"        => "Closed",
    "derelict"      => "Derelict",
    "ruins"         => "Ruins",
    "no trace"      => "No trace",
    "unknown"       => "?",
);

$curr_region = "";
$curr_line_name = "";
while ($stmt->fetch())
{
    if ($curr_region != $region)
    {
        $t->setCurrentBlock("REGION");
        $t->setVariable("REGION", "$region Region");
        $t->parseCurrentBlock();
        $t->parse("TABLE-ROW");
        $curr_region = $region;
    }
    if ($curr_line_name != $line_name)
    {
        $url = "/lines/show.php"
            . urlenc("?name=$line_state:$line_name");
        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE-URL", $url);
        $t->setVariable("LINE-TEXT", htmlentities($description));
        $t->parseCurrentBlock();
        $t->parse("TABLE-ROW");
        $curr_line_name = $line_name;
    }

    $url = "/locations/show.php"
        . urlenc("?name=$location_state:$location_name");

    $t->setCurrentBlock("LOCATION");
    $t->setVariable("LOCATION-URL", $url);
    $t->setVariable("LOCATION-TEXT", $location_name);

    if (!$size)
        $size = "?";
    else if (floor($size) != $size)
    {
        $size = sprintf("%d'%.0f\"",
            floor($size),
            ($size - floor($size)) * 12);
    }
    else
        $size = "$size'";

    $t->setVariable("SIZE", $size);

    $t->setVariable("TYPE", $type_lookup[$type]);
    $t->setVariable("STATUS", $status_lookup[$status]);
    $t->setVariable("NOTES", htmlentities($notes));
    $t->setVariable("PHOTOS", $photos);
    $t->parseCurrentBlock();
    $t->parse("TABLE-ROW");
}
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
