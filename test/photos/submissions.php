<?php

require_once "site.inc";

$title = "Photographic Submissions";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("submissions.tpl");
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));
?>
