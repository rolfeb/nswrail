<?php

require_once "../init.inc";

require "display-photos.inc";

$intro = "
This page contains photos which have been added to these pages in the
last 4 weeks.
";

display_photos("recent", "", "Recently Added NSW Railway Photos", $intro);
?>
