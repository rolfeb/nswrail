<?php
require_once "../init.inc";
require "display-photos.inc";

$owner = quote_external(get_post("owner", ""));

display_photos("owner", $owner, "NSW Railway Photos owned by $owner", "");
?>
