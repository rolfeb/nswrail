<?php

require_once "site.inc";

$title = "Google NSW Railway Search";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("google_cse.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
