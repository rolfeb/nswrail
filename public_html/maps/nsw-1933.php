<?php

require_once "site.inc";

$title = "NSW Network Map - 1933";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("nsw-1933.tpl", true, true);
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

?>
