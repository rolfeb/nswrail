<?php

require_once "../init.inc";
require_once "../util.inc";

$year = quote_external(get_post("year"));           /* optional */

$REGION = "Newcastle";
$MAP_PREFIX = "newcastle";
$SCRIPT = "newcastle-network.php";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("network.tpl", true, true);
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

if (!$year)
    $year = "2004";

for ($y = 1860; $y <= 2000; $y += 5)
{
    $t->setCurrentBlock("OPTION-YEAR");
    $t->setVariable("YEAR", $y);
    if ($y == $year)
        $t->setVariable("SELECTED", "selected=\"selected\"");
    $t->parseCurrentBlock();
}
$t->setCurrentBlock("OPTION-YEAR");
$t->setVariable("YEAR", "2004");
if ($year == 2004)
    $t->setVariable("SELECTED", "selected=\"selected\"");
$t->parseCurrentBlock();

$t->setCurrentBlock("YEAR-FORM");
$t->setVariable("URL", $SCRIPT);
$t->parseCurrentBlock();

$t->setCurrentBlock("MAP");
$t->setVariable("IMAGE", "images/${MAP_PREFIX}$year.gif");
$t->parseCurrentBlock();

$t->setCurrentBlock("MAIN");
$t->setVariable("TITLE", "$REGION by Year - $year");
$t->parseCurrentBlock();

$t->show();

exit;

?>
