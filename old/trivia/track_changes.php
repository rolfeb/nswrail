<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("track_changes.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Railway Track Length Changes");
$t->parseCurrentBlock();

$t->show();

exit;

?>
