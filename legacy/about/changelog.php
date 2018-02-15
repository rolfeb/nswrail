<?php

require_once "site.inc";

$title = "NSWrail.net Change Log";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("changelog.tpl");

$log = changelog("../changes.dat");

for ($i = count($log) - 1; $i >= 0; $i--)
{
    $t->setCurrentBlock("ENTRY");
    $t->setVariable("DATE", $log[$i]["date"]);
    $t->setVariable("TEXT", $log[$i]["text"]);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
