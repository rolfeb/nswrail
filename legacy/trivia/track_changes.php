<?php

require "site.inc";

$title = "NSW Railway Track Length Changes";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("track_changes.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
