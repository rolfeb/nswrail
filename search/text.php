<?php

require_once "site.inc";

$title = "Text Search";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("text.tpl");

$searchmode = quote_external(get_post("searchmode"));

if ($searchmode)
    perform_search($t);

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"),
    array(
        'HEAD-EXTRA'    => implode(file('text.hdr'), ""),
    )
);

return;

function perform_search(&$t)
{
    global $dbi;

    $keywords = quote_external(get_post("keywords"));
    $keyword_join = quote_external(get_post("keywordjoin"));
    $match_locnname = quote_external(get_post("matchlocnname"));
    $match_locndesc = quote_external(get_post("matchlocndesc"));
    $match_photo = quote_external(get_post("matchphotos"));

    $results = array();

    if ($keywords)
    {
        $keyword_list = explode(" ", $keywords);

        if ($match_locnname)
        {
            #
            # Look through r_location.location_name
            #
            $stmt = $dbi->stmt_init();

            $list = $keyword_list;
            for ($i = 0; $i < count($list); $i++)
                $list[$i] = "locate(lower('$list[$i]'), lower(L.location_name)) != 0";
            if ($keyword_join == 'any')
                $subclause = implode(" or ", $list);
            else
                $subclause = implode(" and ", $list);

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

            $stmt->execute();
            $stmt->bind_result($state, $location);

            while ($stmt->fetch())
            {
                $url = url_location($state, $location);
                $context = "";

                $results[] = array($location, $url, "", $context);
            }
            $stmt->close();
        }

        if ($match_locndesc)
        {
            #
            # Look through r_location_text.text
            #
            $stmt = $dbi->stmt_init();

            $list = $keyword_list;
            for ($i = 0; $i < count($list); $i++)
                $list[$i] = "locate(lower('$list[$i]'), lower(LT.text)) != 0";
            if ($keyword_join == 'any')
                $subclause = implode(" or ", $list);
            else
                $subclause = implode(" and ", $list);

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

            $stmt->execute();
            $stmt->bind_result($state, $location, $text);

            while ($stmt->fetch())
            {
                $url = url_location($state, $location);

                foreach ($keyword_list as $kw)
                {
                    $text = preg_replace
                        (
                            "/(?!<b>)($kw)(?!<\/b>)/",
                            '<b>\1</b>',
                            $text
                        );
                }
                $context = $text;

                $results[] = array($location, $url, "", $context);
            }
            $stmt->close();
        }

        if ($match_photo)
        {
            #
            # Look through r_location_photo.caption
            #
            $stmt = $dbi->stmt_init();

            $list = $keyword_list;
            for ($i = 0; $i < count($list); $i++)
                $list[$i] = "locate(lower('$list[$i]'), lower(LP.caption)) != 0";
            if ($keyword_join == 'any')
                $subclause = implode(" or ", $list);
            else
                $subclause = implode(" and ", $list);

            $stmt->prepare("
                select
                    LP.location_state,
                    LP.location_name,
                    LP.seqno,
                    LP.caption
                from
                    r_location_photo LP
                where
                    ($subclause)
                limit 201
            ")
                or dbi_error_trace("prepare failed");

            $stmt->execute();
            $stmt->bind_result($state, $location, $seqno, $caption);

            while ($stmt->fetch())
            {
                $url = url_location_photo($state, $location, $seqno);

                # XXX: hacky
                $url .= '" rel="prettyPhoto[iframes]"';

                foreach ($keyword_list as $kw)
                {
                    $caption = preg_replace
                        (
                            "/(?!<b>)($kw)(?!<\/b>)/",
                            '<b>\1</b>',
                            $caption
                        );
                }
                $context = $caption;

                $results[] = array($location, $url, 'rel="prettyPhoto[iframes]"', $context);
            }
            $stmt->close();
        }
    }

    if (count($results) > 0)
    {
        uasort($results, 'sort_by_location');

        $count = 0;
        foreach ($results as $row)
        {
            if ($count > 200)
            {
                $t->setCurrentBlock("WARNING");
                $t->setVariable("MSG", "Too many matches; results have been truncated");
                $t->parseCurrentBlock();
                break;
            }

            $t->setCurrentBlock("RESULTS-LINE");
            $t->setVariable("REF-URL", $row[1]);
            if ($row[2])
            {
                $t->setVariable("REF-REL", $row[2]);
                $t->touchBlock("CAMERA-ICON");
            }
            $t->setVariable("REF-TEXT", $row[0]);
            $t->setVariable("CONTEXT", $row[3]);
            $t->parseCurrentBlock();
            $count++;
        }
    }
    else
    {
        $t->setCurrentBlock("WARNING");
        $t->setVariable("MSG", "No results matched the search criteria.");
        $t->parseCurrentBlock();
    }

    $t->touchBlock("RESULTS");

}

function sort_by_location($a, $b)
{
    if ($a[0] == $b[0])
        return 0;
    else
        return $a[0] < $b[0] ? -1 : 1;
}

?>
