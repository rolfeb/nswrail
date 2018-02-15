<?php

require "site.inc";

function run_maps_sydney_1969()
{
    $tp = [
        'title' => "Sydney Network Diagram - 1969",
        'text' => "The following is the network diagram for Sydney in 1969",
        'image_url' => "/media/maps/sydney-network-1969.png",
    ];

    return $tp;
}

normal_page_wrapper('run_maps_sydney_1969', 'maps-generic.latte');

?>
