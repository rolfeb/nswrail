<?php

require_once "site.inc";

$title = "NSW Railway Lines Other Than Standard Gauge";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("other_gauge.tpl");

$lines = array(
    array(
        "NSW", "goondah_burrinjuck", "Goondah - Burrinjuck Line",
        "610mm",
        "Construction railway for Burrinjuck Dam"
    ),
    array(
        "NSW", "", "Katoomba - Jamieson Valley",
        "1067mm (narrow gauge)",
        "Katoomba Scenic Railway (world's steepest incline railway)."
    ),
    array(
        "NSW", "joadja", "Mittagong - Joadja Line",
        "1067mm (narrow gauge)",
        "Private railway serving the Joadja oil shale mines"
    ),
    array(
        "NSW", "tarrawingee", "Tarrawingee Line",
        "1067mm (narrow gauge)",
        "Originally a private line, later taken over by the NSWGR"
    ),
    array(
        "SA", "broken_hill_cockburn", "Port Pirie - Broken Hill Line",
        "1067mm (narrow gauge)",
        "Original main line from Adelaide"
    ),
    array(
        "QLD", "south_coast", "South Coast Line",
        "1067mm (narrow gauge)",
        "Queensland branch extending into NSW"
    ),
    array(
        "VIC", "robinvale", "Robinvale - Lette Line",
        "1600mm (broad gauge)",
        "Victorian branch extending into NSW"
    ),
    array(
        "VIC", "balranald", "Balranald Line",
        "1600mm (broad gauge)",
        "Victorian branch extending into NSW"
    ),
    array(
        "VIC", "deniliquin", "Deniliquin Line",
        "1600mm (broad gauge)",
        "Victorian branch extending into NSW"
    ),
    array(
        "VIC", "tocumwal", "Tocumwal Line",
        "1600mm (broad gauge)",
        "Victorian branch extending into NSW"
    ),
    array(
        "VIC", "stony_crossing", "Stony Crossing Line",
        "1600mm (broad gauge)",
        "Victorian branch extending into NSW"
    ),
    array(
        "VIC", "oaklands", "Oaklands Line",
        "1600mm (broad gauge)",
        "Victorian branch extending into NSW"
    ),
    array(
        "Vic", "", "Murray River Bridge - Albury Line",
        "1600mm (broad gauge)",
        "Dual gauge (standard/broad) extending into NSW"
    ),
);

foreach ($lines as $l)
{
    $t->setCurrentBlock("LINE");
    if ($l[1])
    {
        $t->setVariable("URL", "/lines/show.php?"
            . urlenc("name=$l[0]:$l[1]"));
        $t->setVariable("TEXT", $l[2]);
    }
    else
        $t->setVariable("NAME", $l[2]);
    $t->setVariable("GAUGE", $l[3]);
    $t->setVariable("NOTES", $l[4]);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
