<?php

require_once "../init.inc";

require "display-photos.inc";

$intro = "
This page contains photos of a historic nature, defined as those which
were taken at least 20 years ago.
";

display_photos("historic", "", "Historic NSW Railway Photos", $intro);
?>
