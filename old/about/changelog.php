<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("changelog.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$log = changelog("../changes.dat");

for ($i = count($log) - 1; $i >= 0; $i--)
{
    $t->setCurrentBlock("ENTRY");
    $t->setVariable("DATE", $log[$i]["date"]);
    $t->setVariable("TEXT", $log[$i]["text"]);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "NSWrail.net Change Log");
$t->parseCurrentBlock();

$t->show();

exit;

?>
