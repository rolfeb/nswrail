<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_articles_smr()
{
    $tp = [
            'title' => "The South Maitland Railway Collieries",
        ];

    return $tp;
}

normal_page_wrapper('run_articles_smr', 'articles-smr.latte');
