<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_articles_lithgow_zigzag()
{
    $tp = [
        'title' => "The Lithgow Zig-Zag",
    ];

    return $tp;
}

normal_page_wrapper('run_articles_lithgow_zigzag', 'articles-lithgow-zigzag.latte');
