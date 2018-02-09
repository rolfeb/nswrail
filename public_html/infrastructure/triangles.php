<?php

require "site.inc";

$title = "NSW Railway Triangles";

$tp = ['title' => $title];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('triangles.latte', $tp));

?>
