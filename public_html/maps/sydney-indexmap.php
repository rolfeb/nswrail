<?php

require "site.inc";

$tp = [
    'title' => "Sydney Map",
    'map' => '/media/maps/sydney-indexmap.gif',
    'ne_imagemap' => implode("\n", file("sydney.map")),
];

normal_page('maps-indexmap.latte', $tp,
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);

?>
