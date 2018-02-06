<?php

require_once "site.inc";
require_once "gallery.inc";

$age = get_setting('HISTORIC_PHOTO_YEARS');

$intro = "
This page contains photos of a historic nature, defined as those which
were taken at least $age years ago.
";

theme_gallery(PhotoThemes::Historic, "", "Historic NSW Railway Photos", $intro);
?>
