<?php

require_once "site.inc";

$MAX = 30;

$title = "NSW Railway $MAX Shortest Lived Sections";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("short_lived_sections.tpl");

$STATE = "NSW";

$stmt = $db->stmt_init();
$stmt->prepare("
    select
        R.description,
        R.line_name,
        SEV1.start_state,
        SEV1.start_name,
        SEV1.end_state,
        SEV1.end_name,
        min(SEV1.year * 12 + SEV1.month) ts1,
        SEV1.day,
        SEV1.month,
        SEV1.year,
        SEV1.year_error,
        max(SEV2.year * 12 + SEV2.month) ts2,
        SEV2.day,
        SEV2.month,
        SEV2.year,
        SEV2.year_error,
        max(SEV2.year * 12 + IFNULL(SEV2.month-1, 0)) - min(SEV1.year * 12 + IFNULL(SEV1.month-1, 0)) tdiff,
        abs(L2.distance - L1.distance)
    from
        r_line R,
        r_section_event SEV1,
        r_section_event SEV2,
        r_location L2,
        r_location L1 
    where
        R.line_state = ?
        and
        SEV1.line_state = R.line_state
        and
        SEV1.line_name = R.line_name
        and
        SEV1.type = 'ON'
        and
        SEV2.line_state = SEV1.line_state
        and
        SEV2.line_name = SEV1.line_name
        and
        SEV2.segment = SEV1.segment
        and
        SEV2.start_state = SEV1.start_state
        and
        SEV2.start_name = SEV1.start_name
        and
        SEV2.end_state = SEV1.end_state
        and
        SEV2.end_name = SEV1.end_name
        and
        SEV2.type = 'CN'
        and
        not isnull(SEV2.year)
        and
        L1.location_state = SEV1.start_state
        and
        L1.location_name = SEV1.start_name
        and
        L2.location_state = SEV2.end_state
        and
        L2.location_name = SEV2.end_name
    group by
        R.line_name,
        SEV1.start_state,
        SEV1.start_name,
        SEV1.end_state,
        SEV1.end_name
    having
        tdiff > 0
    order by
        tdiff
    limit
        $MAX
")
    or dbi_error_trace("prepare failed");

$stmt->bind_param("s", $STATE);
$stmt->execute();
$stmt->bind_result($line_desc, $line, $start_state, $start_name, $end_state,
    $end_name, $ts1, $day1, $month1, $year1, $year_error1, $ts2, $day2,
    $month2, $year2, $year_error2, $tdiff, $length);

while ($stmt->fetch())
{
    $opened = date_cpts2text($day1, $month1, $year1, $year_error1);
    $closed = date_cpts2text($day2, $month2, $year2, $year_error2);

    if ($length)
        $length = sprintf("%.1f", $length);
    else
        $length = "?";

    $t->setCurrentBlock("SECTION");
    $t->setVariable("URL", "/lines/show.php?"
        . urlenc("name=$STATE:$line"));
    $t->setVariable("TEXT", $line_desc);
    $t->setVariable("START", $start_name);
    $t->setVariable("END", $end_name);
    $t->setVariable("OPENED", $opened);
    $t->setVariable("CLOSED", $closed);
    $t->setVariable("YEARS", floor($tdiff / 12));
    $t->setVariable("MONTHS", $tdiff % 12);
    $t->setVariable("LENGTH", $length);
    $t->parseCurrentBlock();
}
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
