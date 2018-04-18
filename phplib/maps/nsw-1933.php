<?php

require "site.inc";

function run_maps_nsw_1933()
{
    $tp = [
        'title' => "NSW Network Map - 1933",
    ];
    return $tp;
}

normal_page_wrapper('run_maps_nsw_1933', 'maps-nsw-1933.latte');
