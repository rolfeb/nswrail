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
    $t = new HTML_Template_ITX('.');
    $t->loadTemplateFile('photo.tpl', true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('PHOTO-QUEUE', get_photo_queue_html());
    $t->touchBlock('CONTENT');
    $t->parseCurrentBlock();

    $head = file_get_contents("photo-style.html");
    $head .= "\n";
    $head .= '<script type="text/javascript" src="/c/upload/photo.js"></script>';
    $head .= '<script type="text/javascript" src="/c/upload/photo-queue.js"></script>';
    $head .= "\n";

    display_page("Photograph Upload", $t->get("CONTENT"),
        array(
            'HEAD-EXTRA' => $head
        )
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
