<?php

require "site.inc";

function run_about_credits()
{
    $contributors = [];
    foreach (file("credits.dat") as $name) {
        $contributors[] = trim($name);
    }

    $tp = [
        'title' => "NSWrail.net Credits",
        'contributors' => implode(', ', $contributors),
    ];

    return $tp;
}

normal_page_wrapper('run_about_credits', 'about-credits.latte');
