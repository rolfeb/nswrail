<?php

require_once "site.inc";
require_once "gallery.inc";

$recent = get_setting('RECENT_PHOTO_UPLOAD_WEEKS');

$intro = "
This page contains photos which have been added to these pages in the
last $recent weeks.
";

theme_gallery(PhotoThemes::Recent, "", "Recently Added NSW Railway Photos", $intro);
?>
