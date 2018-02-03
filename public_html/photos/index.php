<?php

require "site.inc";

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

function add_cards($t, $cards, $restricted_cards)
{
    for ($i = 0; $i < sizeof($cards); $i++) {
        if ($restricted_cards) {
            list($url, $auth, $title, $text, $thumbnail) = $cards[$i];
        } else {
            list($url, $title, $text, $thumbnail) = $cards[$i];
            $auth = true;
        }

        if ($auth) {
            if ($thumbnail) {
                $t->setCurrentBlock("THUMBNAIL");
                $t->setVariable("THUMBNAIL-URL", $thumbnail);
                $t->parseCurrentBlock();
            }
            $t->setCurrentBlock("CARD");
            $t->setVariable("URL", $url);
            $t->setVariable("TITLE", $title);
            $t->setVariable("TEXT", $text);
            if ($restricted_cards) {
                $t->setVariable("STYLE", 'auth-card');
            }
            $t->parseCurrentBlock();
        }
    }
}

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl");

add_cards($t, $cards, false);
add_cards($t, $auth_cards, true);

$title = "Photographs";
$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
