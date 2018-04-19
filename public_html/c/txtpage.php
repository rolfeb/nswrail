<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

global $BASE_PATH;

$template_dir = "$BASE_PATH/c/tpl";

$t = new HTML_Template_ITX($template_dir);
$t->loadTemplateFile("txtpage.tpl", true, true);

$file = get_post('file', '');

if ($file && $text = file_get_contents("$BASE_PATH/" .$file)) {
    #
    # Extract the [h1] entry and use this for the page title
    #
    preg_match('/\[h1\](.+)\[\/h1\]/', $text, $matches);
    $title = $matches[1];

    #
    # Parse the BBCode text file
    #
    $parser = new HTML_BBCodeParser();
    $parser->addFilter('Lists');
    $parser->addFilter('Extended');
    $parser->addFilter('Links');
    $parser->setText($text);
    $parser->parse();
    $text = $parser->getParsed();
} else {
    error_page("invalid txt file: $file");
}


$t->setCurrentBlock('CONTENT');
$t->setVariable('TEXT', $text);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
