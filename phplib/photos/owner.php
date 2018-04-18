<?php

require "site.inc";
require "gallery.inc";

$owner = param_get_string("owner");

theme_gallery(PhotoThemes::Owner, $owner, "Photos owned by $owner", "");
