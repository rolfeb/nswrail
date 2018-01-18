<?php

require_once "site.inc";

$title = "NSW Turntables and Triangles Map";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("turning-facilities.tpl", true, true);
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

?>
