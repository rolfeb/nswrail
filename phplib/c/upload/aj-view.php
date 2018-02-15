<?php

require 'site.inc';

function display_thumbnail_image($image)
{
    global $user;

    $stage_dir = get_config('stage-dir') . "/" . $user->uid;

    # return the thumbnail
    $staged_thumbnail = "$stage_dir/small/$image";
    $im = new Imagick($staged_thumbnail);

    header('Content-Type: image/jpeg');
    print($im);
}

if ($user->is_guest()) {
    noperm_page();
}

$image = param_get_string('image');

try {
    display_thumbnail_image($image);
} catch (Exception $e) {
    print($e->getMessage());
}

?>
