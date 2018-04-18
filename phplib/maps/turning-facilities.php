<?php

require "site.inc";

function run_maps_turning_facilities()
{
    $tp = [
        'title' => "NSW Turntables and Triangles Map",
    ];

    return $tp;
}

normal_page_wrapper('run_maps_turning_facilities', 'maps-turning-facilities.latte');
