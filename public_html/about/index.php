<?php

require_once "site.inc";

$title = "About NSWrail.net";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

?>
