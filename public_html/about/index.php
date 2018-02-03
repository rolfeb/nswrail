<?php

require "site.inc";


$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

$cards = [
    [
        '/about/faq.txt',
        'FAQ',
        'TBD',
        ''
    ],
    [
        '/about/submissions.txt',
        'Submissions',
        'TBD',
        ''
    ],
    [
        '/photos/contributers.php',
        'Photo contributers',
        'TBD.',
        ''
    ],
    [
        '/about/credits.php',
        'Credits',
        'TBD.',
        ''
    ],
];

for ($i = 0; $i < sizeof($cards); $i++) {
    list($url, $title, $text) = $cards[$i];
    $t->setCurrentBlock("CARD");
    $t->setVariable("URL", $url);
    $t->setVariable("TITLE", $title);
    $t->setVariable("TEXT", $text);
    $t->parseCurrentBlock();
}

$title = "About";
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
