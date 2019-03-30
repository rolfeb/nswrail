<?php
/**
 * Copyright (c) 2019 Rolfe Bozier
 */

require "site.inc";


/**
 * @param $thumbnail
 * @param $name
 */
function display_photo($thumbnail, $name)
{
    $media_dir = get_config('photo-media-dir');
    if ($thumbnail) {
        $path = $media_dir . "/thumbnails/" . $name;
    } else {
        $path = $media_dir . "/" . $name;
    }

    if (!file_exists($path)) {
        http_response_code(404);
        return;
    }

    $f = finfo_open();
    $mime_info = finfo_file($f, $path, FILEINFO_MIME_TYPE);
    finfo_close($f);
    if (!in_array($mime_info, array('image/jpeg', 'image/png'))) {
        error_log("Attempt to display illegal image type: $mime_info (name=$path)");
        http_response_code(415);
        return;
    }

    $fp = fopen($path, "rb");
    if (!$fp) {
        http_response_code(404);
        return;
    }
    header("Content-Type: " . $mime_info);
    fpassthru($fp);

    fclose($fp);
}

try {
    if (param_get_string_opt('t') != '') {
        $name = param_get_string('t');
        display_photo(true, $name);
    } else if (param_get_string_opt('p') != '') {
        $name = param_get_string('p');
        display_photo(false, $name);
    } else {
        throw new InternalError('Malformed request');
    }
} catch (\Exception $e) {
    report_error($e);
}
