<?php

require "site.inc";

$title = "NSW Railway Altitude Highs and Lows";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("altitude.tpl");

$highest = array(
    array(
        "NSW", "skitube", "Skitube",
        "NSW", "Blue Cow", "",
        1900,
    ),
    array(
        "NSW", "main_north", "Main North Line",
        "", "", "Between Llangothlin and Ben Lomond",
        1377,
    ),
    array(
        "NSW", "main_north", "Main North Line",
        "NSW", "Ben Lomond", "",
        1363,
    ),
    array(
        "NSW", "oberon", "Oberon Branch",
        "NSW", "Oberon", "",
        1104,
    ),
    array(
        "NSW", "main_north", "Main North Line",
        "", "", "Between Walcha Road and Wollun",
        1102,
    ),
    array(
        "NSW", "main_west", "Main Western Line",
        "", "", "Between Bell and Newnes Junction",
        1092,
    ),
    array(
        "NSW", "main_north", "Main North Line",
        "NSW", "Wollun", "",
        1084,
    ),
    array(
        "NSW", "bombala", "Bombala Line",
        "NSW", "Nimmitabel", "",
        1068,
    ),
    array(
        "NSW", "main_west", "Main West Line",
        "NSW", "Newnes Junction", "",
        1068,
    ),
    array(
        "NSW", "main_west", "Main West Line",
        "NSW", "Bell", "",
        1067,
    ),
);

$lowest = array(
    array(
        "NSW", "eastern_suburbs", "Eastern Suburbs Line",
        "", "", "Under Hay Street, City",
        "-12.8",
    ),
    array(
        "NSW", "airport", "Airport Line",
        "", "", "",
        "approx -12",
    ),
    array(
        "NSW", "airport", "Airport Line",
        "NSW", "Wolli Creek", "",
        "approx -5",
    ),
    array(
        "NSW", "main_south", "Main South Line",
        "", "", "Central Station platforms 24 and 25",
        0.9,
    ),
    array(
        "NSW", "newcastle", "Newcastle Branch",
        "NSW", "Newcastle", "",
        1,
    ),
    array(
        "NSW", "city_circle", "City Circle Line",
        "NSW", "Wynyard", "",
        4,
    ),
);

foreach ($highest as $l)
{
    if ($l[3])
    {
        $t->setCurrentBlock("HIGH-STATION");
        $t->setVariable("LOCATION-URL", "/locations/show.php?"
            . urlenc("name=$l[3]:$l[4]"));
        $t->setVariable("LOCATION-TEXT", $l[4]);
        $t->parseCurrentBlock();
    }
    else
    {
        $t->setCurrentBlock("HIGH-LOCATION");
        $t->setVariable("LOCATION", $l[5]);
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock("HIGH");
    $t->setVariable("LINE-URL", "/lines/show.php?"
        . urlenc("name=$l[0]:$l[1]"));
    $t->setVariable("LINE-TEXT", $l[2]);
    $t->setVariable("HEIGHT", $l[6]);
    $t->parseCurrentBlock();
}

foreach ($lowest as $l)
{
    if ($l[3])
    {
        $t->setCurrentBlock("LOW-STATION");
        $t->setVariable("LOCATION-URL", "/locations/show.php?"
            . urlenc("name=$l[3]:$l[4]"));
        $t->setVariable("LOCATION-TEXT", $l[4]);
        $t->parseCurrentBlock();
    }
    else
    {
        $t->setCurrentBlock("LOW-LOCATION");
        $t->setVariable("LOCATION", $l[5]);
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock("LOW");
    $t->setVariable("LINE-URL", "/lines/show.php?"
        . urlenc("name=$l[0]:$l[1]"));
    $t->setVariable("LINE-TEXT", $l[2]);
    $t->setVariable("HEIGHT", $l[6]);
    $t->parseCurrentBlock();
}


$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
