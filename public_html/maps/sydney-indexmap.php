<?php

require_once "site.inc";

$title = "Sydney Map";

$tp = [
    'title' => $title,
    'map' => '/media/maps/sydney-indexmap.gif',
    'ne_imagemap' => implode("\n", file("sydney.map")),
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('indexmap.latte', $tp),
    [
        'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>'
    ]
);

?>
