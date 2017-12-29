<?php

require_once "../init.inc";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("map.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAP");
$t->setVariable("IMAGE", "images/sydney-index.gif");
$t->setVariable("IMAGEMAP", image_map());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "Sydney Map");
$t->parseCurrentBlock();

$t->show();

exit;

function image_map()
{
    # XXX: hard-coded version
    return implode("\n", file("sydney.map"));
}

?>
