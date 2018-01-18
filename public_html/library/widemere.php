<?php

require_once "site.inc";

$title = "The Widemere Quarry Branch";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("widemere.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
