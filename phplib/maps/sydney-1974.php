<?php

require "site.inc";

function run_maps_sydney_1974()
{
    $tp = [
        'title' => "Sydney Network Map - 1974",
    ];

    return $tp;
}

normal_page_wrapper('run_maps_sydney_1974', 'maps-sydney-1974.latte');
