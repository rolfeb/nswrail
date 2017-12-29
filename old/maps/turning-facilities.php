<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("turning-facilities.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Turntables and Triangles Map");
$t->parseCurrentBlock();

$t->show();

exit;

?>
