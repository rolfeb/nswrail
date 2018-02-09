<?php

require "site.inc";

$title = "NSW Railway Spirals";

$tp = [
    'title' => $title,
    'spirals' => [],
];

$spirals = [
    ["NSW", "main_south", "Main South Line", "Tanyinna", "Bethungra"],
    ["NSW", "north_coast", "North Coast Line", "Cougal", "Border Loop"],
];

foreach ($spirals as $l)
{
    list($state, $line_name, $line_desc, $location1, $location2) = $l;

    $tp['spirals'][] = [
        'nc_url' => '/lines/details.php?' . urlenc("name=$state:$line_name"),
        'text' => $line_desc,
        'location1' => $location1,
        'location2' => $location2,
    ];
}

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('spirals.latte', $tp));

?>
