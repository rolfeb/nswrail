<?php

require "site.inc";

$cards = [
    [
        '/maps/nsw-indexmap.php',
        'NSW Map',
        'A search map for the NSW network.',
        '/media/maps/thumbnails/nsw-indexmap.png'
    ],
    [
        '/maps/sydney-indexmap.php',
        'Sydney Map',
        'A search map for the Sydney network.',
        '/media/maps/thumbnails/sydney-indexmap.png'
    ],
    [
        '/maps/newcastle-indexmap.php',
        'Newcastle Map',
        'A search map for the Newcastle network.',
        '/media/maps/thumbnails/newcastle-indexmap.png'
    ],
    [
        '/maps/turning-facilities.php',
        'Turning facilities map',
        'A map showing places where locomotives could be turned around (triangles, turntables).',
        '',
    ],
    /*
    [
        '/maps/google.php',
        'Google Maps',
        'TBD',
        ''
    ],
    [
        '/maps/nsw-by-year.php',
        'NSW by year',
        'TBD',
        ''
    ],
    [
        '/maps/sydney-by-year.php',
        'Sydney by year',
        'TBD',
        ''
    ],
    [
        '/maps/newcastle-by-year.php',
        'Newcastle by year',
        'TBD',
        ''
    ],
    */
    [
        '/maps/nsw-1933.php',
        'NSW in 1933',
        'A detailed map of the NSW network in 1933, including all known stations.',
        '/media/maps/thumbnails/nsw-network-1933.png',
    ],
    [
        '/media/maps/sydney-network-1969.png',
        'Sydney in 1969',
        'An official network map for Sydney, from 1969.',
        '/media/maps/thumbnails/sydney-network-1969.png',
    ],
    [
        '/maps/sydney-1974.php',
        'Sydney in 1974',
        'A detailed map of the Sydney network in 1974, including all sidings and goods lines.',
        '/media/maps/thumbnails/sydney-network-1974.png',
    ],
    [
        '/media/maps/newcastle-network-1950.png',
        'Newcastle in 1950',
        'A detailed map of the Newcastle network in 1950, including the Smouth Maitland and Richmond Vale Railways.',
        '/media/maps/thumbnails/newcastle-network-1950.png'
    ],

];

display_card_page("Maps", "", $cards, []);

?>
