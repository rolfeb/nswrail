<?php

require_once "site.inc";

$title = "Signalling in NSW";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("signalling.tpl");


$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
