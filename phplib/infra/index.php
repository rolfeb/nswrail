<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_infra_index()
{
    $cards = [
        [
            '/infrastructure/tunnels.php',
            'Tunnels',
            'A listing of all tunnels in NSW, including closed tunnels and ones that no longer exist.',
            '',
        ],
        [
            '/infrastructure/turntables.php',
            'Turntables',
            'A list of all known turntables in NSW.',
            '',
        ],
        [
            '/infrastructure/triangles.php',
            'Triangles',
            'Track triangles were sometimes used at three-way junctions, or where the cost of a turntable was not warranted.',
            '',
        ],
        [
            '/infrastructure/other_gauge.php',
            'Non-Standard Gauge',
            'Generally track in NSW was standard gauge, however there are a few places where a different gauge was used.',
            '',
        ],
        [
            '/infrastructure/spirals.php',
            'Railway Spirals',
            'In a few places, the track loops around 360 degrees, in order to gain height over a longer distance.',
            '',
        ],
        [
            '/infrastructure/altitude.php',
            'Highs and Lows',
            'A brief listing of some of the high and low locations within the railway network.',
            '',
        ],
        [
            '/infrastructure/short_lived_sections.php',
            'Short-lived Sections',
            'Some sections of track were only open for a relatively short period. This table shows the shortest.',
            '',
        ],
        [
            '/infrastructure/never_completed.php',
            'Lines not Completed',
            'Some lines were started but never completed to their original length.',
            '',
        ],
        [
            '/infrastructure/formally_closed.php',
            'Formally Closed Lines',
            'Only a few lines have been formally closed by the required Act of Parliament.',
            '',
        ],
        [
            '/infrastructure/closed_sydney_stations.php',
            'Closed Sydney Stations',
            'There are a number of stations in Sydney that are either no longer in use, or were never opened as planned.',
            '',
        ],
    ];

    return [
        'title' => 'Infrastructure',
        'text' => '',
        'cards' => $cards,
        'auth_cards' => [],
    ];
}

card_page_wrapper('run_infra_index');
