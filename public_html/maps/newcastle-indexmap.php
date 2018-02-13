<?php

require "site.inc";

$tp = [
    'title' => "Newcastle Map",
    'map' => '/media/maps/newcastle-indexmap.gif',
    'ne_imagemap' => implode("\n", file("newcastle.map")),
];

normal_page('maps-indexmap.latte', $tp,
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);

?>
