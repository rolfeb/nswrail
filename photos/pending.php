<?php

require_once "site.inc";

$title = "Pending NSW Railway Photos";

$RECENT_DAYS = 28;

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("pending.tpl");

$stmt = $db->stmt_init();
$stmt->prepare("
    select
        LP.owner,
        R.description,
        LP.location_name,
        LP.caption,
        LP.year
    from
        r_location_photo LP,
        r_line_location RL,
        r_line R
    where
        LP.status = 'P'
        and
        RL.location_state = LP.location_state
        and
        RL.location_name = LP.location_name
        and
        RL.mainline = 'Y'
        and
        R.line_state = RL.line_state
        and
        R.line_name = RL.line_name
    order by
        LP.owner,
        R.description,
        RL.segment,
        RL.seqno,
        LP.location_name,
        LP.seqno
")
    or dbi_error_trace("prepare failed");

$stmt->bind_result($owner, $line, $location, $caption, $year);
$stmt->execute();

while ($stmt->fetch())
{
    $href = "/locations/photo.php?"
        . urlenc("name=$state:$location:$seqno");

    empty($owner) && $owner = "Rolfe Bozier";

    $t->setCurrentBlock("PHOTOS");
    $t->setVariable("OWNER", $owner);
    $t->setVariable("HREF", $href);
    $t->setVariable("LINE", $owner);
    $t->setVariable("NAME", $location);
    $t->setVariable("TEXT", $caption);
    $t->setVariable("DATE", $year);
    $t->parseCurrentBlock();
}
$stmt->close();

$intro = "
This page contains photos are ready to be released.
";

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->setVariable("INTRODUCTION", $intro);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
