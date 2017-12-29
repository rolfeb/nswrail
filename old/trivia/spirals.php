<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("spirals.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$spirals = array(
    array("NSW", "main_south", "Main South Line",
        "Tanyinna", "Bethungra"),
    array("NSW", "north_coast", "North Coast Line",
        "Cougal", "Border Loop"),
);

foreach ($spirals as $l)
{
    $t->setCurrentBlock("SPIRAL");
    $t->setVariable("URL", "/lines/show.php?"
        . urlenc("name=$l[0]:$l[1]"));
    $t->setVariable("TEXT", $l[2]);
    $t->setVariable("LOCATION1", $l[3]);
    $t->setVariable("LOCATION2", $l[4]);
    $t->parseCurrentBlock();
}


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Railway Spirals");
$t->parseCurrentBlock();

$t->show();

exit;

?>
