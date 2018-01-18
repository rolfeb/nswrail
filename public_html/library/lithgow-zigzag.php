<?php

require_once "site.inc";

$title = "The Lithgow Zig-Zag";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("lithgow-zigzag.tpl");

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"),
    array(
        /*
         * This template makes use of <a target="..."> which is not strictly
         * conforming.
         */
        'DTD-TRANSITIONAL' => 1,
    )
);

?>
