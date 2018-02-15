<?php

require_once "site.inc";

$pos = quote_external(get_post("pos"));     /* optional */
$mode = quote_external(get_post("mode"));   /* optional */

$title = "Google Maps View";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("google.tpl", true, true);
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

if ($pos)
{
    list($lon, $lat) = explode(",", $pos);
    $zoom = 14;
}
else
{
    $lon = 147.28;
    $lat = -32.62;
    $zoom = 6;
}

$t2 = new HTML_Template_ITX(".");
$t2->loadTemplateFile("google.hdr", true, true);
$t2->setVariable("LON", $lon);
$t2->setVariable("LAT", $lat);
$t2->setVariable("ZOOM", $zoom);
$t2->parse();

$head_extra = $t2->get();

display_page($title, $t->get("CONTENT"),
    array(
        'HEAD-EXTRA'    => $head_extra,
    )
);

exit;

?>
