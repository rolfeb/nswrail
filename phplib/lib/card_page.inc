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

function card_page($title, $text, $cards, $auth_cards)
{
    $tp = [
        'title' => $title,
        'text' => $text,
    ];
    $tp = _add_cards($tp, $cards, false);
    $tp = _add_cards($tp, $auth_cards, true);

    normal_page('util-card-layout.latte', $tp);
}

function card_page_wrapper($run_function)
{
    try {
        $p = $run_function();
        card_page($p['title'], $p['text'], $p['cards'], $p['auth_cards']);
    } catch (\Exception $e) {
        report_error($e);
    }
}
