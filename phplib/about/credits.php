<?php

require "site.inc";

function run_about_credits()
{
    $contributers = [];
    foreach (file("credits.dat") as $name) {
        $contributers[] = trim($name);
    }

    $tp = [
        'title' => "NSWrail.net Credits",
        'contributers' => implode(', ', $contributers),
    ];

    return $tp;
}

normal_page_wrapper('run_about_credits', 'about-credits.latte');

?>
