<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("nsw-1933.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSW Network Map - 1933");
$t->parseCurrentBlock();

$t->show();

exit;

?>
