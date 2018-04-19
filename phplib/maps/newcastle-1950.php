<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_maps_newcastle_1950()
{
    $tp = [
        'title' => "Newcastle Network Map - 1950",
        'text' => "The following is a network map for Newcastle in 1950",
        'image_url' => "/media/maps/newcastle-network-1950.png",
    ];

    return $tp;
}

normal_page_wrapper('run_maps_newcastle_1950', 'maps-generic.latte');
