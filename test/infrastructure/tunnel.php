<?php

require_once "site.inc";
require_once "dbutil.inc";

$title = "NSW Railway Tunnels";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("tunnel.tpl");

$STATE = "NSW";

global $dbi;

$stmt = $dbi->stmt_init();
$stmt->prepare("
    select
        R.line_state,
        R.line_name,
        R.description,
        L.location_name,
        L.status,
        L.distance,
        LTU.lengths,
        LTU.type,
        RL1.location_name,
        RL2.location_name
    from
        r_line R,
        r_line_location RL,
        r_line_location RL1,
        r_line_location RL2,
        r_location L left join r_location_tunnel LTU on
            L.location_state = LTU.location_state
            and
            L.location_name = LTU.location_name
    where
        R.line_state = ?
        and
        RL.line_state = R.line_state
        and
        RL.line_name = R.line_name
        and
        L.location_state = RL.location_state
        and
        L.location_name = RL.location_name
        and
        L.type = 'tunnel'
        and
        RL1.line_state = RL.line_state
        and
        RL1.line_name = RL.line_name
        and
        RL1.segment = RL.segment
        and
        RL1.seqno = RL.seqno - 1
        and
        RL2.line_state = R.line_state
        and
        RL2.line_name = R.line_name
        and
        RL2.segment = RL.segment
        and
        RL2.seqno = RL.seqno + 1
    order by
        R.description,
        RL.segment,
        RL.seqno
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $STATE);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($line_state, $line_name, $description, $location_name,
    $status, $distance, $lengths, $type, $prev_location, $next_location);

$prev_line_name = "";
while ($stmt->fetch())
{
    if ($line_name != $prev_line_name)
    {
        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE-URL", "/lines/show.php?"
            . urlenc("name=$line_state:$line_name"));
        $t->setVariable("LINE-TEXT", $description);
        $t->parseCurrentBlock();
        $t->parse("LINE-OR-TUNNEL");
    }

    if ($distance != "")
        $distance = sprintf("%.1f km", $distance);
    else
        $distance = "???";

    $photos = count_photos($STATE, $location_name);
    if ($photos == 0)
        $photos = "";

    $t->setCurrentBlock("TUNNEL");
    $t->setVariable("TUNNEL-URL", "/locations/show.php?"
        . urlenc("name=$STATE:$location_name"));
    $t->setVariable("TUNNEL-TEXT", $location_name);
    $t->setVariable("TYPE", tunnel_type2text($type));
    $t->setVariable("STATUS", locn_status2text($status));
    $t->setVariable("LENGTH", tunnel_lengths2text($lengths));
    $t->setVariable("PHOTOS", $photos);
    $t->setVariable("DISTANCE", $distance);
    $t->setVariable("BETWEEN", "$prev_location and $next_location");
    $t->parseCurrentBlock();
    $t->parse("LINE-OR-TUNNEL");

    $prev_line_name = $line_name;
}
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
