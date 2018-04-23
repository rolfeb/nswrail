<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

#
# aj-upload.php
#   AJAX callback handler: upload and save an image in the staging area
#
require 'site.inc';
require 'photo-util.php';

/**
 * @return string the filename of the uploaded file
 * @throws ImagickException
 * @throws InternalError
 * @throws SecurityError
 */
function save_upload_in_staging_area()
{
    global $user;

    $params = $_FILES['upload-filename'];
    $r_filename = basename($params['name'][0]);
    # $r_type = $params['type'][0];
    $r_tmpfile = $params['tmp_name'][0];
    # $r_error = $params['error'][0];
    # $r_size = $params['size'][0];

    # only allow extensions: jpg, jpeg, png
    $info = pathinfo($r_tmpfile);
    if (!in_array($info['extension'], array('jpg', 'jpeg', 'png'))) {
        throw new SecurityError("Error: invalid file extension: " . $info['extension']);
    }

    # only allow files containing image data
    $f = finfo_open();
    $mime_info = finfo_file($f, $r_tmpfile, FILEINFO_MIME_TYPE);
    finfo_close($f);
    if (!in_array($mime_info, array('image/jpeg', 'image/png'))) {
        throw new SecurityError("Error: invalid file type: $mime_info");
    }

    # create stage directories, if necessary
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

    # add an audit entry
    Audit::addentry(Audit::A_UPLOAD, $r_filename);

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
    # save the image and create a thumbname
    $thumbnail = save_upload_in_staging_area();

    # construct the HTML for the new queue entry on the client
    $queue_item_html = get_photo_queue_item_html($thumbnail);

    # tell the bootstrap fileinput component how we went
    $reply = [
        'initialPreview' => [
            "<img src='/c/upload/aj-view.php?image=$thumbnail' class='file-preview-image' title='Preview'>"
        ],
        'initialPreviewConfig' => [
            [
                'caption'   => 'uploaded successfully!',
                'url'       => '/c/upload/aj-delete.php',
                'key'       => $thumbnail
            ]
        ],
        'queueEntry' => $queue_item_html
    ];
} catch (Exception $e) {
    $reply = [ 'error' => $e->getMessage() ];
}

print(json_encode($reply));
