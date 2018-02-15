<?php

require "site.inc";
require "gallery.inc";

$owner = quote_external(get_post("owner", ""));
theme_gallery(PhotoThemes::Owner, $owner, "Photos owned by $owner", "");
?>
