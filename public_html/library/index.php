<?php

require "site.inc";

$cards = [
    [
        '/library/planned.php',
        'Planned Lines',
        'Some lines that were gazetted in government legislation, but never built.',
    ],
    [
        '/library/station_names.php',
        'Station Names',
        'A copy of the paper "Names of Railway Stations in New South Wales. With their Meaning and Origin".',
    ],
    [
        '/library/smr.php',
        'South Maitland Railway',
        'A brief outline of the South Maitland Railway network.',
    ],
    [
        '/library/widemere.php',
        'Widemere Quarry Line',
        'An article on the Widemere Quarry branch.'
    ],
    [
        '/library/lithgow-zigzag.php',
        'Lithgow Zig-Zag',
        'A brief summary of the history of the Lithgow Zig-Zag area.',
    ],
];


$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

for ($i = 0; $i < sizeof($cards); $i++) {
    list($url, $title, $text) = $cards[$i];
    $t->setCurrentBlock("CARD");
    $t->setVariable("URL", $url);
    $t->setVariable("TITLE", $title);
    $t->setVariable("TEXT", $text);
    $t->parseCurrentBlock();
}

$title = "Articles";
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
