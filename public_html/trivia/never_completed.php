<?php

require_once "site.inc";

$title = "NSW Railway Lines That Were Never Completed";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("never_completed.tpl");

$lines = array(
    array(
        "NSW", "bonalbo", "Casino to Bonalbo", "61"
    ),
    array(
        "NSW", "dombarton_maldon", "Dombarton to Maldon", "35"
    ),
    array(
        "NSW", "gulgong_maryvale", "Gulgong to Maryvale", "72"
    ),
    array(
        "NSW", "guyra_dorrigo", "Guyra to Dorrigo", "143"
    ),
    array(
        "VIC", "robinvale", "Robinvale to Lette", "25"
    ),
    array(
        "NSW", "eastern_suburbs", "Eastern Suburbs extension", "?"
    ),
);

foreach ($lines as $l)
{
    $t->setCurrentBlock("LINE");
    $t->setVariable("URL", "/lines/show.php?"
        . urlenc("name=$l[0]:$l[1]"));
    $t->setVariable("TEXT", $l[2]);
    $t->setVariable("LENGTH", $l[3]);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
