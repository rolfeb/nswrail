<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require 'site.inc';
require 'photo-util.php';

/**
 * @return string
 */
function get_photo_queue_html()
{
    global $user;

    $thumb_dir = get_config('stage-dir') . '/' . $user->uid . '/small';

    $html = '';
    if (file_exists($thumb_dir)) {
        $files = array_diff(scandir($thumb_dir), array('.', '..'));
        foreach ($files as $file) {
            $html .= get_photo_queue_item_html($file);
        }
    }
    
    return $html;
}

/**
 *
 */
function show_upload_form()
{
    $tp = [
        'photo_queue' => get_photo_queue_html(),
        'locations' => [],
    ];

    # populate the location name <datalist>
    foreach (get_locations() as $location) {
        $tp['locations'][] = [
            'location' => $location,
        ];
    }

    $head = file_get_contents("photo-style.html");
    $head .= "\n";
    $head .= '<script type="text/javascript" src="/c/upload/photo.js"></script>';
    $head .= "\n";

    normal_page('upload-photo.latte', $tp,
        [
            'HEAD-EXTRA' => $head
        ]
    );
}

if ($user->is_guest()) {
    noperm_page();
}

try {
    show_upload_form();
} catch (Exception $e) {
    report_error($e);
}
