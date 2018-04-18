<?php

require "site.inc";

function run_maps_sydney_indexmap()
{
    $tp = [
        'title' => "Sydney Map",
        'map' => '/media/maps/sydney-indexmap.gif',
        'ne_imagemap' => implode("\n", file("sydney.map")),
    ];

    return $tp;
}

normal_page_wrapper('run_maps_sydney_indexmap', 'maps-indexmap.latte',
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);
