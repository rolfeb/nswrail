<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_infra_altitude()
{
    $tp = [
        'title' => "NSW Railway Altitude Highs and Lows",
        'highs' => [],
        'lows' => [],
    ];

    $highest = [
        [
            "NSW", "skitube", "Skitube",
            "NSW", "Blue Cow", "",
            1900,
        ],
        [
            "NSW", "main_north", "Main North Line",
            "", "", "Between Llangothlin and Ben Lomond",
            1377,
        ],
        [
            "NSW", "main_north", "Main North Line",
            "NSW", "Ben Lomond", "",
            1363,
        ],
        [
            "NSW", "oberon", "Oberon Branch",
            "NSW", "Oberon", "",
            1104,
        ],
        [
            "NSW", "main_north", "Main North Line",
            "", "", "Between Walcha Road and Wollun",
            1102,
        ],
        [
            "NSW", "main_west", "Main Western Line",
            "", "", "Between Bell and Newnes Junction",
            1092,
        ],
        [
            "NSW", "main_north", "Main North Line",
            "NSW", "Wollun", "",
            1084,
        ],
        [
            "NSW", "bombala", "Bombala Line",
            "NSW", "Nimmitabel", "",
            1068,
        ],
        [
            "NSW", "main_west", "Main West Line",
            "NSW", "Newnes Junction", "",
            1068,
        ],
        [
            "NSW", "main_west", "Main West Line",
            "NSW", "Bell", "",
            1067,
        ],
    ];

    $lowest = [
        [
            "NSW", "eastern_suburbs", "Eastern Suburbs Line",
            "", "", "Under Hay Street, City",
            "-12.8",
        ],
        [
            "NSW", "airport", "Airport Line",
            "", "", "",
            "approx -12",
        ],
        [
            "NSW", "airport", "Airport Line",
            "NSW", "Wolli Creek", "",
            "approx -5",
        ],
        [
            "NSW", "main_south", "Main South Line",
            "", "", "Central Station platforms 24 and 25",
            0.9,
        ],
        [
            "NSW", "newcastle", "Newcastle Branch",
            "NSW", "Newcastle", "",
            1,
        ],
        [
            "NSW", "city_circle", "City Circle Line",
            "NSW", "Wynyard", "",
            4,
        ],
    ];

    foreach ($highest as $l) {
        list($line_state, $line_name, $line_desc, $location_state, $location_name, $location, $height) = $l;

        if ($location_name) {
            $location_url =
                '/locations/details.php?'
                .
                http_build_query([
                    'name' => "$location_state:$location_name",
                ]);
            $location_text = $location_name;
        } else {
            $location_url = '';
            $location_text = $location;
        }

        $tp['highs'][] = [
            'ne_line_url' =>
                '/lines/details.php?'
                .
                http_build_query([
                    'name' => "$line_state:$line_name",
                ]),
            'line_text' => $line_desc,
            'height' => $height,
            'ne_location_url' => $location_url,
            'location_text' => $location_text,
        ];
    }

    foreach ($lowest as $l) {
        list($line_state, $line_name, $line_desc, $location_state, $location_name, $location, $height) = $l;

        if ($location_name) {
            $location_url =
                '/locations/details.php?'
                .
                http_build_query([
                    'name' => "$location_state:$location_name",
                ]);
            $location_text = $location_name;
        } else {
            $location_url = '';
            $location_text = $location;
        }

        $tp['lows'][] = [
            'ne_line_url' =>
                '/lines/details.php?'
                .
                http_build_query([
                    'name' => "$line_state:$line_name",
                ]),
            'line_text' => $line_desc,
            'height' => $height,
            'ne_location_url' => $location_url,
            'location_text' => $location_text,
        ];
    }

    return $tp;
}

normal_page_wrapper('run_infra_altitude', 'infra-altitude.latte');
