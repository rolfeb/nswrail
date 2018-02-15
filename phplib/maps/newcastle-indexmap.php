<?php

require "site.inc";

function run_maps_newcastle_indexmap()
{
    $tp = [
        'title' => "Newcastle Map",
        'map' => '/media/maps/newcastle-indexmap.gif',
        'ne_imagemap' => implode("\n", file("newcastle.map")),
    ];
    return $tp;
}

normal_page_wrapper('run_maps_newcastle_indexmap', 'maps-indexmap.latte',
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);

?>
