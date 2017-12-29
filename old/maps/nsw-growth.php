<?php

require_once "../init.inc";
require_once "../util.inc";

$REGION = "NSW";
$MAP_PREFIX = "nsw";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("growth.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

$t->setCurrentBlock("MAP");
$t->setVariable("TEXT",
"The following image is an animated GIF file showing the history of the NSW
railway network. It is characterised by early growth from 1860 to around
1930, a period of maturity, followed by the closure of a number of branches
in the 1980's."
);
$t->setVariable("IMAGE", "images/${MAP_PREFIX}-changes.gif");
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "$REGION Growth");
$t->parseCurrentBlock();

$t->show();

exit;

?>
