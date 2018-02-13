<?php

require "site.inc";

$contributers = [];
foreach (file("credits.dat") as $name) {
    $contributers[] = trim($name);
}

$tp = [
    'title' => "NSWrail.net Credits"<
    'contributers' => implode(', ', $contributers),
];

normal_page('about-credits.latte', $tp);

?>
