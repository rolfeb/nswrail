<?php

require "site.inc";

$title = "NSW Railway Station Names and Origins";

$tp = ['title' => $title];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('station_names.latte', $tp));

?>
