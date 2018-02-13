<?php

require "site.inc";

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

    $tp['lines'][] = [
        'nc_url' => '/lines/details.php?' . urlenc("name=$line_state:$line_name"),
        'text' => $line_desc,
        'length' => $length,
    ];
}

normal_page('infra-never-completed.latte', $tp);

?>
