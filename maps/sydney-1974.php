<?php

require_once "site.inc";

$title = "Sydney Network Map - 1974";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("sydney-1974.tpl", true, true);
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
