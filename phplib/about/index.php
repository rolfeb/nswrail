<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 */
function run_about_index()
{
    $cards = [
        [
            '/about/faq.txt',
            'FAQ',
            'TBD',
            ''
        ],
        [
            '/about/submissions.txt',
            'Submissions',
            'TBD',
            ''
        ],
        [
            '/photos/contributors.php',
            'Photo contributors',
            'Here are all the people who contributed photos.',
            ''
        ],
        [
            '/about/credits.php',
            'Thanks',
            'A large number of people have contributed to getting the site to where it is now.',
            ''
        ],
    ];

    $auth_cards = [];

    return [
        'title' => 'About',
        'text' => '',
        'cards' => $cards,
        'auth_cards' => $auth_cards,
    ];
}

card_page_wrapper('run_about_index');
