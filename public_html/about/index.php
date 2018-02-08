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
        'TBD.',
        ''
    ],
    [
        '/about/credits.php',
        'Credits',
        'TBD.',
        ''
    ],
];

display_card_page("About", "", $cards, []);

?>
