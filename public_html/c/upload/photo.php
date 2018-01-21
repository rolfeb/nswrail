<?php

require 'site.inc';

function show_upload_form()
{
    $t = new HTML_Template_ITX('.');
    $t->loadTemplateFile('photo.tpl', true, true);
    $t->setCurrentBlock('CONTENT');
    $t->touchBlock('CONTENT');
    $t->parseCurrentBlock();

    $head = file_get_contents("photo-style.html");
    $head .= "\n";
    $head .= '<script type="text/javascript" src="/c/upload/photo.js"></script>';
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
