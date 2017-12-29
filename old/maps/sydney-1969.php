<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("generic.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Sydney Network Diagram - 1969");
$t->setVariable("TEXT", "The following is the network diagram for Sydney in 1969:");
$t->setVariable("IMAGE-URL", "/maps/images/syd-network1969.gif");
$t->parseCurrentBlock();

$t->show();

exit;

?>
