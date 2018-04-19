<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";
require "gallery.inc";

try {
    $owner = param_get_string("owner");

    run_theme_gallery(PhotoThemes::Owner, $owner, "Photos owned by $owner", "");
} catch (\Exception $e) {
    report_error($e);
}

