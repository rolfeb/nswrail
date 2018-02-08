<?php

require "site.inc";

$title = "The Widemere Quarry Branch";

$tp = ['title' => $title];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('widemere.latte', $tp));

?>
