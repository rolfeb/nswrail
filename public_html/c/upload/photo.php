<?php

require 'site.inc';
require 'photo-util.php';

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

    $latte = new Latte\Engine;
    display_page('Photograph Upload', $latte->renderToString('photo.latte', $tp),
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
    # print_r($_REQUEST);
    # print_r($_FILES);
    # throw new InternalError('not yet implemented');

} catch (Exception $e) {
    report_error($e);
}


?>
