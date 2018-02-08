<?php

function _add_cards($tp, $cards, $restricted_cards)
{
    for ($i = 0; $i < sizeof($cards); $i++) {
        if ($restricted_cards) {
            list($url, $auth, $title, $text, $thumbnail) = $cards[$i];
        } else {
            list($url, $title, $text, $thumbnail) = $cards[$i];
            $auth = true;
        }

        if ($auth) {
            $card = [
                'url' => $url,
                'title' => $title,
                'text' => $text,
            ];
            if ($thumbnail) {
                $card['opt_thumbnail_url'] = $thumbnail;
            }
            if ($restricted_cards) {
                $card['opt_style'] = 'auth-card';
            }

            $tp['cards'][] = $card;
        }
    }

    return $tp;
}

function display_card_page($title, $text, $cards, $auth_cards)
{
    $tp = [
        'title' => $title,
        'text' => $text,
    ];
    $tp = _add_cards($tp, $cards, false);
    $tp = _add_cards($tp, $auth_cards, true);

    $latte = new Latte\Engine;
    $tpldir = dirname(__FILE__) . '/../tpl/';
    display_page($title, $latte->renderToString($tpldir . 'card-page.latte', $tp));
}

?>
