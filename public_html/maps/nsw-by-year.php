<?php

require_once "site.inc";

$REGION = "NSW";
$MAP_PREFIX = "nsw";

$title = "$REGION by Year";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("by-year.tpl", true, true);

for ($y = 1860; $y <= 2000; $y += 5)
{
    $t->setCurrentBlock("YEAR");
    $t->setVariable("MAP-YEAR", $y);
    $t->setVariable("MAP-IMAGE", "images/$MAP_PREFIX$y.png");
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->setVariable("REGION", $REGION);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"),
    array(
        "HEAD-EXTRA" => '<script type="text/javascript" src="/c/js/map-by-year.js"></script>',
        "BODY-EXTRA" => 'onload="loaded()"'
    )
);


?>
