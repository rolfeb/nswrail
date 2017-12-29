<?php

require_once "site.inc";

$title = "NSW Railway Trivia";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("trivia.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
