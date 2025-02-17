<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_infra_other_gauge()
{
    $tp = [
        'title' => "NSW Railway Lines Other Than Standard Gauge",
        'lines' => [],
    ];

    $lines = [
        [
            "NSW", "goondah_burrinjuck", "Goondah - Burrinjuck Line",
            "610mm",
            "Construction railway for Burrinjuck Dam"
        ],
        [
            "NSW", "", "Katoomba - Jamieson Valley",
            "1067mm (narrow gauge)",
            "Katoomba Scenic Railway (world's steepest incline railway)."
        ],
        [
            "NSW", "joadja", "Mittagong - Joadja Line",
            "1067mm (narrow gauge)",
            "Private railway serving the Joadja oil shale mines"
        ],
        [
            "NSW", "tarrawingee", "Tarrawingee Line",
            "1067mm (narrow gauge)",
            "Originally a private line, later taken over by the NSWGR"
        ],
        [
            "SA", "broken_hill_cockburn", "Port Pirie - Broken Hill Line",
            "1067mm (narrow gauge)",
            "Original main line from Adelaide"
        ],
        [
            "QLD", "south_coast", "South Coast Line",
            "1067mm (narrow gauge)",
            "Queensland branch extending into NSW"
        ],
        [
            "VIC", "robinvale", "Robinvale - Lette Line",
            "1600mm (broad gauge)",
            "Victorian branch extending into NSW"
        ],
        [
            "VIC", "balranald", "Balranald Line",
            "1600mm (broad gauge)",
            "Victorian branch extending into NSW"
        ],
        [
            "VIC", "deniliquin", "Deniliquin Line",
            "1600mm (broad gauge)",
            "Victorian branch extending into NSW"
        ],
        [
            "VIC", "tocumwal", "Tocumwal Line",
            "1600mm (broad gauge)",
            "Victorian branch extending into NSW"
        ],
        [
            "VIC", "stony_crossing", "Stony Crossing Line",
            "1600mm (broad gauge)",
            "Victorian branch extending into NSW"
        ],
        [
            "VIC", "oaklands", "Oaklands Line",
            "1600mm (broad gauge)",
            "Victorian branch extending into NSW"
        ],
        [
            "VIC", "", "Murray River Bridge - Albury Line",
            "1600mm (broad gauge)",
            "Dual gauge (standard/broad) extending into NSW"
        ],
    ];

    foreach ($lines as $l) {
        list($state, $line_name, $line_desc, $gauge, $notes) = $l;

        if ($line_name) {
            $url = "/lines/details.php?" .
                http_build_query([
                    'name' => "$state:$line_name",
                ]);
        } else {
            $url = '';
        }

        $tp['lines'][] = [
            'ne_url' => $url,
            'text' => $line_desc,
            'gauge' => $gauge,
            'notes' => $notes,
        ];
    }
    return $tp;
}

normal_page_wrapper('run_infra_other_gauge', 'infra-other-gauge.latte');
