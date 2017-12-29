<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("bulletin.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$stmt = mysql_query("
    select
        max(month),
        year
    from
        r_bulletin_index
    where
        year = (select max(year) from r_bulletin_index)
    group by
        year
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

$last_date = "[unknown]";

if ($row = mysql_fetch_array($stmt))
{
    list ($month, $year) = $row;


    $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
        "Sep", "Oct", "Nov", "Dec");

    $last_date = $months[$month-1] . " $year";
}
mysql_free_result($stmt);


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "ARHS Bulletin Index Search");
$t->setVariable("LATEST", $last_date);
$t->parseCurrentBlock();

$t->show();

exit;

?>
