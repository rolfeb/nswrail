<?php

require "site.inc";

$title = "NSWrail.net Credits";

$contributers = [];
foreach (file("credits.dat") as $name) {
    $contributers[] = trim($name);
}

$tp = [
    'title' => $title,
    'contributers' => implode(', ', $contributers),
];

$latte = new Latte\Engine;
display_page($title, $latte->renderToString('credits.latte', $tp));

?>
