<?php

require_once "site.inc";
require_once "gallery.inc";

$owner = quote_external(get_post("owner", ""));

theme_gallery(PhotoThemes::Owner, $owner, "NSW Railway Photos owned by $owner", "");
?>
