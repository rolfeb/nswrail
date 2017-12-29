<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("station_names.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Railway Station Names and Origins");
$t->parseCurrentBlock();

$t->show();

exit;

?>
