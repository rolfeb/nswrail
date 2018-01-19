<?php
#
# Set some global parameters. These should be the same as in the .htaccess
# file.
#
# BASE_PATH         The top-level location of the site.
# AUSRAIL_STATE     The name of the state (lower-case).
# AUSRAIL_PHOTO_DIR The full path to the rail photos directory.
#
$BASE_PATH          = "/work/dev/nswrail-dev/public_html";
$AUSRAIL_PHOTO_DIR  = "/work/dev/nswrail-dev/public_html/locations/photos";

require_once "lib.inc";

init_request();

?>
