<?php

require "site.inc";

function run_articles_widemere()
{
    $tp = [
        'title' => "The Widemere Quarry Branch",
    ];
    return $tp;
}

normal_page_wrapper('run_articles_widemere', 'articles-widemere.latte');
