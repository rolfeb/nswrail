<?php

require_once "site.inc";

$title = "NSWrail.net Credits";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("credits.tpl");

$contrib = "";
foreach (file("../credits.dat") as $v)
{
    $v = ereg_replace("\n", "", $v);

    if ($contrib != "")
        $contrib.= ", ";
    $contrib .=  $v;
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("CONTRIB", $contrib);
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

exit;

?>
