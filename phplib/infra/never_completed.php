<?php

require "site.inc";

function run_infra_never_completed()
{
    $tp = [
        'title' => "NSW Railway Lines That Were Never Completed",
        'lines' => [],
    ];

    $lines = [
        [
            "NSW", "bonalbo", "Casino to Bonalbo", "61"
        ],
        [
            "NSW", "dombarton_maldon", "Dombarton to Maldon", "35"
        ],
        [
            "NSW", "gulgong_maryvale", "Gulgong to Maryvale", "72"
        ],
        [
            "NSW", "guyra_dorrigo", "Guyra to Dorrigo", "143"
        ],
        [
            "VIC", "robinvale", "Robinvale to Lette", "25"
        ],
        [
            "NSW", "eastern_suburbs", "Eastern Suburbs extension", "?"
        ],
    ];

    foreach ($lines as $l) {
        list($line_state, $line_name, $line_desc, $length) = $l;

        $url = '/lines/details.php?' .
            http_build_query([
                'name' => "$line_state:$line_name",
            ]);

        $tp['lines'][] = [
            'ne_url' => $url,
            'text' => $line_desc,
            'length' => $length,
        ];
    }
    return $tp;
}

normal_page_wrapper('run_infra_never_completed', 'infra-never-completed.latte');

?>
