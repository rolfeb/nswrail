<?php

require "site.inc";

$tp = [
    'title' => "NSW Map",
    'map' => '/media/maps/nsw-indexmap.gif',
    'ne_imagemap' => implode("\n", file("nsw.map")),
];

normal_page('maps-indexmap.latte', $tp,
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);

?>
