<?php

require_once "site.inc";

$title = "ARHS Bulletin Index Search";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("bulletin.tpl");

global $dbi;

$stmt = $dbi->stmt_init();
$stmt->prepare("
    select
        max(month),
        year
    from
        r_bulletin_index
    where
        year = (select max(year) from r_bulletin_index)
    group by
        year
")
    or dbi_error_trace("prepare failed");

$last_date = "[unknown]";

$stmt->execute();
$stmt->bind_result($month, $year);

if ($stmt->fetch())
{
    $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
        "Sep", "Oct", "Nov", "Dec");

    $last_date = $months[$month-1] . " $year";
}
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->setVariable("LATEST", $last_date);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
