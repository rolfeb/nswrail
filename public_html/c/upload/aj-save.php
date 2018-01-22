<?php

require 'site.inc';

function save_upload_in_staging_area()
{
    global $user;

    $params = $_FILES['upload-filename'];
    $r_filename = basename($params['name'][0]);
    $r_type = $params['type'][0];
    $r_tmpfile = $params['tmp_name'][0];
    $r_error = $params['error'][0];
    $r_size = $params['size'][0];

    # XXX: validate params: size, error, type

    $stage_dir = get_config('stage-dir') . "/" . $user->uid;
    $thumb_dir = "$stage_dir/small";
    if (!file_exists($stage_dir)) {
        if (!mkdir($stage_dir)) {
            throw new InternalError("Error: failed to create stage directory");
        }
    }
    if (!file_exists($thumb_dir)) {
        if (!mkdir($thumb_dir)) {
            throw new InternalError("Error: failed to create thumbnail directory");
        }
    }

    # move the uploaded file into the per-user staging area
    $staged_file = "$stage_dir/$r_filename";
    if (!move_uploaded_file($r_tmpfile, $staged_file)) {
        throw new InternalError("Error: failed to save image [$r_tmpfile,$staged_file]");
    }

    # create a thumbnail
    $staged_thumbnail = "$stage_dir/small/$r_filename";
    $im = new Imagick($staged_file);
    $im->thumbnailImage(150, 150, true);
    $im->writeImage($staged_thumbnail);

    return "$r_filename";
}

if ($user->is_guest()) {
    noperm_page();
}

try {
    $thumbnail = save_upload_in_staging_area();
    $reply = [
        'initialPreview' => [
            "<img src='/c/upload/aj-view.php?image=$thumbnail' class='file-preview-image' title='Preview'>"
        ],
        'initialPreviewConfig' => [
            [
                'caption'   => $thumbnail,
                'url'       => '/c/upload/aj-delete.php',
                'key'       => $thumbnail
            ]
        ]
    ];
} catch (Exception $e) {
    $reply = [ 'error' => $e->getMessage() ];
}

print(json_encode($reply));

?>
