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
    if (!file_exists($stage_dir)) {
        if (!mkdir($stage_dir)) {
            return "Error: failed to create stage directory";
        }
    }

    $staged_file = "$stage_dir/$r_filename";
    if (!move_uploaded_file($r_tmpfile, $staged_file)) {
        return "Error: failed to save image [$r_tmpfile,$staged_file]";
    }

    # XXX: create thumbnail?

    return NULL;
}

if ($user->is_guest()) {
    noperm_page();
}

$err = save_upload_in_staging_area();
# XXX: replace double quotes in $err

if ($err) {
    print("{ \"error\": \"$err\" }");
} else {
    print("{ }");
}

print_r($_FILES);

?>
