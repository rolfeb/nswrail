<?php

require "site.inc";

$title = "The South Maitland Railway Collieries";

$tp = ['title' => $title];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('smr.latte', $tp));

?>
