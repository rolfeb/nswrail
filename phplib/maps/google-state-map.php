<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_maps_state()
{
    $tp = [
        'title' => "NSW Railway Google Map",

        'google_map_apikey' => 'AIzaSyAcfRpOxo-uKn1nY7XbBChfPWZhkXPnEPs',
        'map_geox' => 147.28,
        'map_geoy' => -32.62,
        'map_scale' => 6,
    ];
    return $tp;
}

normal_page_wrapper('run_maps_state', 'maps-state.latte');
