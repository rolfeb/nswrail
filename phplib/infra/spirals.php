<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_infra_spirals()
{
    $tp = [
        'title' => "NSW Railway Spirals",
        'spirals' => [],
    ];

    $spirals = [
        ["NSW", "main_south", "Main South Line", "Tanyinna", "Bethungra"],
        ["NSW", "north_coast", "North Coast Line", "Cougal", "Border Loop"],
    ];

    foreach ($spirals as $l)
    {
        list($state, $line_name, $line_desc, $location1, $location2) = $l;

        $url = '/lines/details.php?' .
            http_build_query([
                'name' => "$state:$line_name",
            ]);

        $tp['spirals'][] = [
            'ne_url' => $url,
            'text' => $line_desc,
            'location1' => $location1,
            'location2' => $location2,
        ];
    }
    return $tp;
}

normal_page_wrapper('run_infra_spirals', 'infra-spirals.latte');
