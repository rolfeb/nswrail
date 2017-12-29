<?php

require_once "site.inc";

$title = "The South Maitland Railway Collieries";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("smr.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
