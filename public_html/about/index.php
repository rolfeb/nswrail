<?php

require "site.inc";

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
        '/photos/contributers.php',
        'Photo contributers',
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

display_card_page("About", "", $cards, []);

?>
