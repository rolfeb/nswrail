<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_articles_index()
{
    $cards = [
        [
            '/articles/planned.php',
            'Planned Lines',
            'Some lines that were gazetted in government legislation, but never built.',
            '',
        ],
        [
            '/articles/station_names.php',
            'Station Names',
            'A copy of the paper "Names of Railway Stations in New South Wales. With their Meaning and Origin".',
            '',
        ],
        [
            '/articles/smr.php',
            'South Maitland Railway',
            'A brief outline of the South Maitland Railway network.',
            '',
        ],
        [
            '/articles/widemere.php',
            'Widemere Quarry Line',
            'An article on the Widemere Quarry branch.',
            '',
        ],
        [
            '/articles/lithgow-zigzag.php',
            'Lithgow Zig-Zag',
            'A brief summary of the history of the Lithgow Zig-Zag area.',
            '',
        ],
    ];

    $auth_cards = [];

    $intro = <<<'EOS'
The following are some longer-form articles about various parts of the NSW
railway network. 
EOS;
    
    return [
        'title' => 'Articles',
        'text' => $intro,
        'cards' => $cards,
        'auth_cards' => $auth_cards,
    ];
}

card_page_wrapper('run_articles_index');
