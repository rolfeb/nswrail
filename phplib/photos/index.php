<?php

require "site.inc";

function run_photos_index()
{
    global $user;

    $cards = [
        [
            '/photos/recent.php',
            'Newly Added',
            'All photos that have been added in the last month.',
            ''
        ],
        [
            '/photos/historic.php',
            'Historic',
            'All photos that were taken more than 30 years ago.',
            '/media/photos/thumbnails/trewilga/trewilga02.jpg'
        ],
        [
            '/photos/signal_box.php',
            'Signal Boxes',
            'Photos showing the exterior or interior of signal boxes.',
            '/media/photos/thumbnails/springwood/springwood03.jpg'
        ],
        [
            '/photos/safeworking.php',
            'Safeworking',
            'Photos showing safeworking equipment or processes.',
            '/media/photos/thumbnails/medway_junction/medway_junction08.jpg'
        ],
        [
            '/photos/diagram.php',
            'Diagrams',
            'Photos of track diagrams, usually inside signal boxes.',
            '/media/photos/thumbnails/binalong/binalong18.jpg'
        ],
        [
            '/photos/turntable.php',
            'Turntables',
            'Photos of turntables in various states of repair.',
            '/media/photos/thumbnails/valley_heights/valley_heights03.jpg'
        ],
        [
            '/photos/tunnel.php',
            'Tunnels',
            'Photos of tunnels.',
            '/media/photos/thumbnails/ardglen_tunnel/ardglen_tunnel05.jpg'
        ],
        [
            '/photos/night.php',
            'Night',
            'Photos taken at night.',
            '/media/photos/thumbnails/valley_heights/valley_heights08.jpg'
        ],
    ];

    $auth_cards = [
        [
            '/c/upload/photo.php',
            $user->is_loggedin(),
            'Upload photos',
            'Upload and annotate your photos to the site.',
            '/media/images/thumbnails/canon-dslr.jpg'
        ],
    ];

    return [
        'title' => 'Photographs',
        'text' => '',
        'cards' => $cards,
        'auth_cards' => $auth_cards,
    ];
}

card_page_wrapper('run_photos_index');
