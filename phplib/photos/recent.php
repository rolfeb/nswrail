<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";
require "gallery.inc";

$recent = get_setting('RECENT_PHOTO_UPLOAD_WEEKS');

$intro = "
This page contains photos which have been added to these pages in the
last $recent weeks.
";

run_theme_gallery(PhotoThemes::Recent, "", "Recently Added NSW Railway Photos", $intro);
