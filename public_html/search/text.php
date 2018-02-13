<?php

require "site.inc";

#
# Highlight the position[s] of each keyword in the context string. The result
# will necessarily be rendered by latte without escaping, so we need to be
# careful:
#   1. The text string is untrusted.
#   2. The keyword is untrusted.
#
function highlight_keywords($text, $keywords)
{
    foreach ($keywords as $kw) {
        $kw_len = strlen($kw);
        list($done, $remaining) = ['', $text];

        while (TRUE) {
            $pos = stripos($remaining, $kw);
            if ($pos !== FALSE) {
                # match -> escape the prefix, highlight the kw, and continue
                $done .=
                    htmlspecialchars(substr($remaining, 0, $pos))
                    . "<span class=\"caption-highlight\">$kw</span>";
                $remaining = 
                    substr($remaining, $pos + $kw_len);
            } else {
                # no match -> escape the remaining text, and break
                $text = $done . htmlspecialchars($remaining);
                break;
            }
        }
    }

    return $text;
}

function sort_by_location($a, $b)
{
    if ($a['name'] == $b['name']) {
        return 0;
    } else {
        return $a['name'] < $b['name'] ? -1 : 1;
    }
}

function perform_search($tp)
{
    global $db;

    $keywords = quote_external(get_post("keywords"));
    $keyword_join = quote_external(get_post("keywordjoin"));
    $match_locnname = quote_external(get_post("matchlocnname"));
    $match_locndesc = quote_external(get_post("matchlocndesc"));
    $match_photo = quote_external(get_post("matchphotos"));

    $rows = [];

    if ($keywords) {
        $keyword_list = explode(" ", strtolower($keywords));

        if ($match_locnname) {
            # Look through r_location.location_name
            $stmt = $db->stmt_init();

            $match_sql = [];
            $valrefs = [str_repeat('s', count($keyword_list))];

            for ($i = 0; $i < count($keyword_list); $i++) {
                $match_sql[] = 'locate(?, lower(L.location_name)) != 0';
                $valrefs[] = &$keyword_list[$i];
            }

            if ($keyword_join == 'any') {
                $subclause = implode(" or ", $match_sql);
            } else {
                $subclause = implode(" and ", $match_sql);
            }

            $stmt->prepare("
                select
                    L.location_state,
                    L.location_name
                from
                    r_location L
                where
                    ($subclause)
                limit 201
            ")
                or dbi_error_trace("prepare failed");

            call_user_func_array(array($stmt, 'bind_param'), $valrefs);

            $stmt->execute();
            $stmt->bind_result($state, $location);

            while ($stmt->fetch()) {
                $url = url_location($state, $location);

                $rows[] = [
                    'name' => $location,
                    'ec_context' => '',
                    'u_location' => [
                            'url' => $url,
                        ],
                ];
            }
            $stmt->close();
        }

        if ($match_locndesc) {
            #
            # Look through r_location_text.text
            #
            $stmt = $db->stmt_init();

            $match_sql = [];
            $valrefs = [str_repeat('s', count($keyword_list))];

            for ($i = 0; $i < count($keyword_list); $i++) {
                $match_sql[] = 'locate(?, lower(LT.text)) != 0';
                $valrefs[] = &$keyword_list[$i];
            }

            if ($keyword_join == 'any') {
                $subclause = implode(" or ", $match_sql);
            } else {
                $subclause = implode(" and ", $match_sql);
            }

            $stmt->prepare("
                select
                    LT.location_state,
                    LT.location_name,
                    LT.text
                from
                    r_location_text LT
                where
                    ($subclause)
                limit 201
            ")
                or dbi_error_trace("prepare failed");

            call_user_func_array([$stmt, 'bind_param'], $valrefs);

            $stmt->execute();
            $stmt->bind_result($state, $location, $text);

            while ($stmt->fetch()) {
                $url = url_location($state, $location);
                $text = highlight_keywords($text, $keyword_list);

                $rows[] = [
                    'name' => $location,
                    'ec_context' => $text,
                    'u_location' => [
                            'url' => $url,
                        ],
                ];
            }
            $stmt->close();
        }

        if ($match_photo) {
            #
            # Look through r_location_photo.caption
            #
            $stmt = $db->stmt_init();

            $match_sql = [];
            $valrefs = [str_repeat('s', count($keyword_list))];

            for ($i = 0; $i < count($keyword_list); $i++) {
                $match_sql[] = 'locate(?, lower(LP.caption)) != 0';
                $valrefs[] = &$keyword_list[$i];
            }

            if ($keyword_join == 'any') {
                $subclause = implode(" or ", $match_sql);
            } else {
                $subclause = implode(" and ", $match_sql);
            }

            $stmt->prepare("
                select
                    LP.location_state,
                    LP.location_name,
                    LP.seqno,
                    LP.file,
                    LP.daterange,
                    LP.day,
                    LP.month,
                    LP.year,
                    LP.caption,
                    LP.owner_uid,
                    IFNULL(U.fullname, IFNULL(LP.legacy_owner, 'Rolfe Bozier')) as fullname
                from
                    r_location_photo LP left join r_user U on LP.owner_uid = U.uid
                where
                    LP.hold is NULL
                    and
                    ($subclause)
                limit 201
            ")
                or dbi_error_trace("prepare failed");

            call_user_func_array([$stmt, 'bind_param'], $valrefs);

            $stmt->execute();
            $stmt->bind_result($state, $location, $seqno, $file, $daterange, $day, $month, $year, $caption, $uid, $fullname);

            while ($stmt->fetch()) {
                $url = url_location_photo($state, $location, $seqno);
                $context = highlight_keywords($caption, $keyword_list);
                $date = date_cpts2html($day, $month, $year, $daterange);

                $rows[] = [
                    'name' => $location,
                    'ec_context' => $context,
                    'u_photo' => [
                            'photo_img' => "/media/photos/$file",
                            'date' => $date,
                            'caption' => $caption,
                            'uid' => $uid,
                            'fullname' => $fullname,
                            'thumb_img' => "/media/photos/thumbnails/$file",
                        ],
                ];
            }
            $stmt->close();
        }
    }

    $nrows = count($rows);
    if ($nrows > 0) {
        uasort($rows, 'sort_by_location');

        if ($nrows > 200) {
            $tp['opt_warning'] = "Too many matches; results have been truncated";
            array_splice($rows, 200);
        }

        $tp['opt_results'] = $rows;
    } else {
        $tp['opt_warning'] = "No results matched the search criteria.";
    }

    return $tp;
}


$tp = [
    'title' => "Text Search",
];

$searchmode = quote_external(get_post("searchmode"));

if ($searchmode) {
    $tp = perform_search($tp);
}

normal_page('search-text.latte', $tp);

return;

?>
