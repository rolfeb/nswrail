<?php

require "site.inc";

$title = "The Lithgow Zig-Zag";

$tp = [
    'title' => $title,
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('lithgow-zigzag.latte', $tp));


?>
