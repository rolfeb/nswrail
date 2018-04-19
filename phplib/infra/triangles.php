<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_infra_triangles()
{
    $tp = [
        'title' => "NSW Railway Triangles",
    ];

    return $tp;
}

normal_page_wrapper('run_infra_triangles', 'infra-triangles.latte');
