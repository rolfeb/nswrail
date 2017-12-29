<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Maps");
$t->parseCurrentBlock();

$t->show();

exit;

?>
