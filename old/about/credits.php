<?php

require_once "../init.inc";
require_once "../util.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("credits.tpl");
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");

$contrib = "";
foreach (file("../credits.dat") as $v)
{
    $v = ereg_replace("\n", "", $v);

    if ($contrib != "")
        $contrib.= ", ";
    $contrib .=  $v;
}

$t->setVariable("CONTRIB", $contrib);

$t->setVariable("TITLE", "NSWrail.net Credits");
$t->parseCurrentBlock();

$t->show();

exit;

?>
