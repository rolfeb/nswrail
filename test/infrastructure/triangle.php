<?php

require_once "site.inc";

$title = "NSW Railway Triangles";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("triangle.tpl");
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
