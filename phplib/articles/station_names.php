<?php

require "site.inc";

function run_articles_station_names()
{
    $tp = [
        'title' => "NSW Railway Station Names and Origins",
    ];

    return $tp;
}

normal_page_wrapper('run_articles_station_names', 'articles-station-names.latte');
