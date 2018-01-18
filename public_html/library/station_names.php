<?php

require_once "site.inc";

$title = "NSW Railway Station Names and Origins";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("station_names.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
