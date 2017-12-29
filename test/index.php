<?php

require_once "site.inc";
require_once "page_utils_main.inc";

$current_year = date("Y");

$log = changelog("changes.dat");
$last = count($log) - 1;

$date = $log[$last]['date'];
$text = $log[$last]['text'];


$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

foreach (db_random_pics(3) as $i => $row)
{
    list($state, $name, $seqno, $file) = $row;

    $url = "/locations/photo.php?" . urlenc("name=$state:$name:$seqno");
    $img = "/locations/photos/small/$file";

    $t->setCurrentBlock("RANDOM-PIC");
    $t->setVariable("URL", $url);
    $t->setVariable("IMG", $img);
    $t->parseCurrentBlock();
}

$t->setCurrentBlock("CONTENT");
$t->setVariable("COUNT-LOCATIONS", db_count_locations());
$t->setVariable("COUNT-PHOTOS", db_count_photographs());
/*
$t->setVariable("COUNT-VISITORS", count_visitors());
*/
$t->setVariable("COPYRIGHT-YEAR", $current_year);
$t->setVariable("LAST-CHANGE_DATE", $date);
$t->setVariable("LAST-CHANGE-LOG", $text);
$t->parseCurrentBlock();


display_page("NSWrail.net", $t->get("CONTENT"),
    array(
        "BANNER"        => '<div id="banner"></div>',
        'HEAD-EXTRA'    => '<link rel="stylesheet" type="text/css" href="/c/css/main.css" />',
    )
);

?>
