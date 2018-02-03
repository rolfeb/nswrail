<?php

require "site.inc";


$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

$cards = [
    [
        '/infrastructure/tunnel.php',
        'Tunnels',
        'A listing of all tunnels in NSW, including closed tunnels and ones that no longer exist.' 
    ],
    [
        '/infrastructure/turntable.php',
        'Turntables',
        'A list of all known turntables in NSW'
    ],
    [
        '/infrastructure/triangle.php',
        'Triangles',
        'Track triangles were sometimes used at three-way junctions, or where the cost of a turntable was not warranted'
    ],
    [
        '/trivia/other_gauge.php',
        'Non-Standard Gauge',
        'Generally track in NSW was standard gauage, however there are a few places where a different gauage was used'
    ],
    [
        '/trivia/spirals.php',
        'Railway Spirals',
        'In a few places, the track loops around 360 degrees, in order to gain height over a longer distance'
    ],
    [
        '/trivia/altitude.php',
        'Highs and Lows',
        'A brief listing of some of the high and low locations within the railway network'
    ],
    [
        '/maps/turning-facilities.php',
        'Turning facilities map',
        'A map showing places where locomotives could be turned around (triangles, turntables'
    ],
    [
        '/trivia/short_lived_sections.php',
        'Short-lived Sections',
        'Some sections of track were only open for a relatively short period. This table shows the shortest.'
    ],
    [
        '/trivia/never_completed.php',
        'Lines not Completed',
        'Some lines were started but never completed to their original length'
    ],
    [
        '/trivia/formally_closed.php',
        'Formally Closed Lines',
        'Only a few lines have been formally closed by the required Act of Parliament'
    ],
    [
        '/trivia/closed_sydney_stations.php',
        'Closed Sydney Stations',
        'There are a number of stations n Sydney that are either no longer in use, or were never opened as planned.'
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

$title = "Infrastructure";
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
