<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("formally_closed.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$closed = array(
    array(
        "NSW", "ballina", "Ballina Branch",
        1, 6, 1948,
    ),
    array(
        "NSW", "westby", "Westby Branch",
        24, 1, 1952,
    ),
    array(
        "NSW", "richmond", "Richmond to Kurrajong Line",
        26, 7, 1952,
    ),
    array(
        "NSW", "morpeth", "Morpeth Branch",
        31, 8, 1953,
    ),
    array(
        "NSW", "kunama", "Kunama Branch",
        1, 2, 1957,
    ),
    array(
        "NSW", "taralga", "Taralga Branch",
        1, 5, 1957,
    ),
    array(
        "NSW", "camden", "Camden Branch",
        1, 1, 1963,
    ),
    array(
        "NSW", "dorrigo", "Dorrigo Branch",
        9, 11, 1993,
    ),
);

foreach ($closed as $l)
{
    $t->setCurrentBlock("LINE");
    $t->setVariable("URL", "/lines/show.php?"
        . urlenc("name=$l[0]:$l[1]"));
    $t->setVariable("TEXT", $l[2]);
    $t->setVariable("CLOSED", date_cpts2text($l[3], $l[4], $l[5], 0));
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Formally Closed NSW Railway Lines");
$t->parseCurrentBlock();

$t->show();

exit;

?>
