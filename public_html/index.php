<?php

require "site.inc";

/*
$current_year = date("Y");

$log = changelog("changes.dat");
$last = count($log) - 1;

$date = $log[$last]['date'];
$text = $log[$last]['text'];
*/


/*
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
*/

/*
$t->setCurrentBlock("CONTENT");
$t->setVariable("COUNT-LOCATIONS", db_count_locations());
$t->setVariable("COUNT-PHOTOS", db_count_photographs());
list($count, $age) = db_count_recent_photographs();
if ($count > 0)
{
    $t->setCurrentBlock("RECENT-PHOTO-BLOCK");
    $t->setVariable("RECENT-PHOTO-COUNT", $count);
    if ($age == 0)
        $t->setVariable("RECENT-PHOTO-AGE", "today!");
    else if ($age == 1)
        $t->setVariable("RECENT-PHOTO-AGE", "1 day ago");
    else
        $t->setVariable("RECENT-PHOTO-AGE", "$age days ago");
    $t->parseCurrentBlock();
}

$t->setVariable("COUNT-VISITORS", count_visitors());
*/
/*
$t->setVariable("COPYRIGHT-YEAR", $current_year);
$t->setVariable("LAST-CHANGE_DATE", $date);
$t->setVariable("LAST-CHANGE-LOG", $text);
*/

$title = "NSWrail.net";
$tp = [];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('index.latte', $tp));

?>
