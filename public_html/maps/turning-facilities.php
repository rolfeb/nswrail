<?php

require "site.inc";

$title = "NSW Turntables and Triangles Map";

$tp = [
    'title' => $title,
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('turning-facilities.latte', $tp));

?>
