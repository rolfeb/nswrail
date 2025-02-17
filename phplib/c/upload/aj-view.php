<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require 'site.inc';

/**
 * @param $image
 * @throws ImagickException
 */
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

try {
    $image = param_get_string('image');

    display_thumbnail_image($image);
} catch (Exception $e) {
    print($e->getMessage());
}
