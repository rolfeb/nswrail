<?php

require "site.inc";

function run_maps_nsw_indexmap()
{
    $tp = [
        'title' => "NSW Map",
        'map' => '/media/maps/nsw-indexmap.gif',
        'ne_imagemap' => implode("\n", file("nsw.map")),
    ];

    return $tp;
}

normal_page_wrapper('run_maps_nsw_indexmap', 'maps-indexmap.latte',
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);
