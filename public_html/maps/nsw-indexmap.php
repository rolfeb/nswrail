<?php

require_once "site.inc";

$title = "NSW Map";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("indexmap.tpl", true, true);
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->setVariable("IMAGE", "/media/maps/nsw-indexmap.gif");
$t->setVariable("IMAGEMAP", image_map());
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"),
    array(
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    )
);

exit;

function image_map()
{
    # XXX: hard-coded version
    return implode("\n", file("nsw.map"));
}

?>
