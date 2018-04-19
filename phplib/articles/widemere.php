<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_articles_widemere()
{
    $tp = [
        'title' => "The Widemere Quarry Branch",
    ];
    return $tp;
}

normal_page_wrapper('run_articles_widemere', 'articles-widemere.latte');
