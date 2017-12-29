<?php

require_once "../init.inc";
require_once "../util.inc";

require_once "menubar.inc"; /* for menubar() */

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("zigzag.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("NAVBAR", implode("\n", file("../navbar.inc")));
$t->setVariable("MENUBAR", menubar());
$t->parseCurrentBlock();


$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "The Lithgow Zig-Zag History");
$t->parseCurrentBlock();

$t->show();

exit;

?>
