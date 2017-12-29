<?php

require_once "../init.inc";
require_once "../util.inc";

$RECENT_DAYS = 28;

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("pending.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$stmt = mysql_query("
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
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

while ($row = mysql_fetch_array($stmt))
{
    list($owner, $line, $location, $caption, $year) = $row;

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
mysql_free_result($stmt);

$intro = "
This page contains photos are ready to be released.
";

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Pending NSW Railway Photos");
$t->setVariable("INTRODUCTION", $intro);
$t->parseCurrentBlock();


$t->show();

?>
