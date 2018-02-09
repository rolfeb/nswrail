<?php

require_once "site.inc";

$title = "NSW Network Map - 1933";

$tp = [
    'title' => $title,
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('nsw-1933.latte', $tp));

?>
