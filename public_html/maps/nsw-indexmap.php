<?php

require_once "site.inc";

$title = "NSW Map";

$tp = [
    'title' => $title,
    'map' => '/media/maps/nsw-indexmap.gif',
    'ne_imagemap' => implode("\n", file("nsw.map")),
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('indexmap.latte', $tp),
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);

?>
