<?php

require "site.inc";

$title = "Formally Closed NSW Railway Lines";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("formally_closed.tpl");

$closed = array(
    array(
        "NSW", "ballina", "Ballina Branch",
        1, 6, 1948,
        "Ballina to Booyong Railway (Cessation of Operation) Act 1953 No 13",
        "http://www.austlii.edu.au/au/legis/nsw/num_act/btbrooa1953n13512.txt"
    ),
    array(
        "NSW", "westby", "Westby Branch",
        24, 1, 1952,
        "", ""
    ),
    array(
        "NSW", "richmond", "Richmond to Kurrajong Line",
        26, 7, 1952,
        "Richmond to Kurrajong Railway (Cessation of Operation) Act 1954 No 9",
        "http://www.austlii.edu.au/au/legis/nsw/num_act/rtkrooa1954n9564.txt"
    ),
    array(
        "NSW", "morpeth", "Morpeth Branch",
        31, 8, 1953,
        "Maitland to Morpeth Railway (Cessation of Operation) Act 1953 No 38",
        "http://www.austlii.edu.au/au/legis/nsw/num_act/mtmrooa1953n38536.txt"
    ),
    array(
        "NSW", "kunama", "Kunama Branch",
        1, 2, 1957,
        "", ""
    ),
    array(
        "NSW", "taralga", "Taralga Branch",
        1, 5, 1957,
        "", ""
    ),
    array(
        "NSW", "camden", "Camden Branch",
        1, 1, 1963,
        "Campbelltown to Camden Tramway and Jerilderie towards Deniliquin Railway Act 1963 No 8",
        "http://www.austlii.edu.au/au/legis/nsw/num_act/ctctajtdra1963n8713.txt",
    ),
    array(
        "NSW", "dorrigo", "Dorrigo Branch",
        9, 11, 1993,
        "GLENREAGH TO DORRIGO RAILWAY (CLOSURE) ACT 1993",
        "http://www.austlii.edu.au/au/legis/nsw/consol_act/gtdra1993369.txt",
    ),
);

foreach ($closed as $l)
{
    $t->setCurrentBlock("LINE");
    $t->setVariable("URL", "/lines/show.php?"
        . urlenc("name=$l[0]:$l[1]"));
    $t->setVariable("TEXT", $l[2]);
    $t->setVariable("CLOSED", date_cpts2text($l[3], $l[4], $l[5], 0));
    $t->setVariable("ACT", $l[6]);
    $t->setVariable("ACT-URL", $l[7]);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();
display_page($title, $t->get("CONTENT"));

?>
