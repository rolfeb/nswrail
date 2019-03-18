<?php
/**
 * Copyright (c) 2019 Rolfe Bozier
 */

require "site.inc";

/**
 * @param $filename
 */
function run_mdpage($filename)
{
    $md = file_get_contents("../" . $filename);
    $pd = new Parsedown();
    $html = $pd->text($md);

    $tp = [
        'ne_text' => $html,
    ];

    normal_page('util-mdpage.latte', $tp);
}

try {
    if (param_get_string_opt('file') != '') {

        run_mdpage(param_get_string('file'));

    } else {
        throw new InternalError('Malformed request');
    }
} catch (\Exception $e) {
    report_error($e);
}