<?php
#
# AJAX callback handler
#   aj-delete.php: delete a staged image on the server
#
require 'site.inc';

function delete_uploaded_image($image)
{
    global $user;

    $stage_dir = get_config('stage-dir') . "/" . $user->uid;
    $thumb_dir = "$stage_dir/small";

    $staged_image = "$stage_dir/$image";
    $staged_thumbnail = "$thumb_dir/$image";

    if (!unlink($staged_thumbnail)) {
        throw new InternalError("Error: unable to remove small/$image");
    }
    if (!unlink($staged_image)) {
        throw new InternalError("Error: unable to remove $image");
    }
}

if ($user->is_guest()) {
    noperm_page();
}

try {
    $image = param_get_string('key');
    delete_uploaded_image($image);
    $reply = [];
} catch (Exception $e) {
    $reply = [ 'error' => $e->getMessage() ];
}

print(json_encode($reply));

?>
