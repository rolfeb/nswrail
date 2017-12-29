<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("timeline.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$stmt = mysql_query("
    select
        R.line_state,
        R.line_name,
        R.description,
        SEV.start_state,
        SEV.start_name,
        L1.distance,
        SEV.end_state,
        SEV.end_name,
        L2.distance,
        SEV.type,
        SEV.day,
        SEV.month,
        SEV.year,
        SEV.year_error
    from
        r_line R,
        r_section_event SEV,
        r_location L1,
        r_location L2
    where
        SEV.line_state = R.line_state
        and
        SEV.line_name = R.line_name
        and
        SEV.type IN ( 'ON', 'CN', 'OT', 'CT', 'LI' )
        and
        L1.location_state = SEV.start_state
        and
        L1.location_name = SEV.start_name
        and
        L2.location_state = SEV.end_state
        and
        L2.location_name = SEV.end_name
    order by
        SEV.year,
        SEV.month,
        SEV.day,
        R.line_name,
        SEV.segment,
        SEV.seqno,
        SEV.type
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$prev_date = "";
$prev_line_name = "";
while ($row = mysql_fetch_array($stmt))
{
    list($line_state, $line_name, $description, $start_state, $start_name,
        $start_distance, $end_state, $end_name, $end_distance, $type,
        $day, $month, $year, $year_error) = $row;

    $date = date_cpts2text($day, $month, $year, $year_error);

    if ($date == "unknown")
        continue;   /* skip events with unknown dates */

    if ($start_distance != "" and $end_distance != "")
    {
        $length = $end_distance - $start_distance;
        if ($length < 0)
            $length = -$length;

        $length = sprintf("%.1f", $length);
    }
    else
        $length = "?";

    $disp_date = $date != $prev_date ? $date : "";

    if ($type == "ON" or $type == "OT")
        $type = "Opened";
    elseif ($type == "CN" or $type == "CT")
        $type = "Closed";
    elseif ($type == "LI")
        $type = "Lifted";
    else
        $type = "???";

    $t->setCurrentBlock("EVENT");
    $t->setVariable("DATE", $disp_date);
    if ($line_name != $prev_line_name or $line_state != $prev_line_state
        or $date != $prev_date)
    {
        $t->setVariable("URL", "/lines/show.php?"
            . urlenc("name=$line_state:$line_name"));
        $t->setVariable("LINE", $description);
    }
    $t->setVariable("EVENT", $type);
    $t->setVariable("SECTION", "$start_name - $end_name");
    $t->setVariable("KM", $length);
    $t->parseCurrentBlock();

    $prev_line_state = $line_state;
    $prev_line_name = $line_name;
    $prev_date = $date;
}
mysql_free_result($stmt);

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Railway Timeline of Events");
$t->parseCurrentBlock();

$t->show();

exit;

?>
