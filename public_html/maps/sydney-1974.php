<?php

require_once "site.inc";

$title = "Sydney Network Map - 1974";

$tp = [
    'title' => $title,
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('sydney-1974.latte', $tp));

?>
