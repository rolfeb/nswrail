<?php

require_once "site.inc";

$title = "Sydney Network Diagram - 1969";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("generic.tpl", true, true);
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->setVariable("TEXT", "The following is the network diagram for Sydney in 1969:");
$t->setVariable("IMAGE-URL", "/maps/images/syd-network1969.gif");
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
