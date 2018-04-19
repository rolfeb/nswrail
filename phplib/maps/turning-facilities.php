<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_maps_turning_facilities()
{
    $tp = [
        'title' => "NSW Turntables and Triangles Map",
    ];

    return $tp;
}

normal_page_wrapper('run_maps_turning_facilities', 'maps-turning-facilities.latte');
