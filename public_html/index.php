<?php

require "site.inc";

$latte = new Latte\Engine;
display_page('NSWrail.net', $latte->renderToString('index.latte', []));

?>
