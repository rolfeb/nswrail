<?php

require_once "../init.inc";
require_once "../util.inc";

function display_photos($theme, $themeextra, $title, $intro)
{
    global $db;

    #
    # Setup the page template
    #
    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("photo-list.tpl");
    $t->setCurrentBlock("CONTROLS");
    $t->setVariable("TOP", top());
    $t->setVariable("MENU", menu());
    $t->parseCurrentBlock();

    #
    # Get display modes from the URL. Valid values:
    #   order: location, year
    #   mode: thumbnail, text
    #
    $order = quote_external(get_post("order", ""));
    $mode = quote_external(get_post("mode", ""));
    $page = quote_external(get_post("page", "1"));

    if ($order != "location" && $order != "year")
        $order = "location";

    if ($mode != "thumbnail" && $mode != "listing")
        $mode = "thumbnail";

    if ($page < 1)
        $page = 1;

    #
    # If we are running in thumbnail mode, manage the paging of results
    #
    $THUMBNAIL_COLUMNS = 4;
    $THUMBNAIL_PAGEROWS = 4;

    #
    # Obtain the SQL statement returning the apppropriate photo list. If
    # we are displaying paged output, calculate the rows to fetch.
    #
    $select_limits = "999999";
    if ($mode == "thumbnail")
    {
        $first = ($page - 1) * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $count = $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $select_limits = "$first,$count";
    }

    switch ($theme)
    {
    case "diagram":
    case "night":
    case "safeworking":
    case "box":
    case "turntable":
        list($stmt, $nrows) = stmt_theme($db, $theme, $order, $select_limits);
        break;

    case "historic":
        list($stmt, $nrows) = stmt_historic($db, $order, $select_limits);
        break;

    case "recent":
        list($stmt, $nrows) = stmt_recent($db, $order, $select_limits);
        break;

    case "tunnel":
        list($stmt, $nrows) = stmt_tunnel($db, $order, $select_limits);
        break;

    case "owner":
        list($stmt, $nrows) = stmt_owner($db, $order, $themeextra, $select_limits);
        break;
    }


    $npages = 1;
    if ($mode == "thumbnail")
    {
        $npages = ceil($nrows / ($THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS));

        if ($page > 1)
            $prev_page_link = $page - 1;
        if ($page < $npages)
            $next_page_link = $page + 1;

        $first = ($page - 1) * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $last = $page * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS - 1;
        $select_limits = "$first,$last";
    }

    #
    # Display the photos
    #
    $n = 0;
    while ($row = mysql_fetch_array($stmt))
    {
        list($state, $line, $location_state, $location, $file, $seqno, $year,
            $owner, $caption) = $row;

        $href = "/locations/photo.php?"
            . urlenc("name=$location_state:$location:$seqno");

        if ($mode == "listing")
        {
            #
            # Listing mode
            #
            empty($owner) && $owner = "Rolfe Bozier";

            $t->setCurrentBlock("LISTING");
            $t->setVariable("HREF", $href);
            $t->setVariable("NAME", $location);
            $t->setVariable("DATE", $year == 0 ? 'unknown' : $year);
            $t->setVariable("TEXT", $caption);
            $t->parseCurrentBlock();
        }
        else
        {
            #
            # Thumbnail mode
            #
            $thumb = "/locations/photos/small/$file";

            $t->setCurrentBlock("CELL");
            $t->setVariable("THUMB-IMG", $thumb);
            $t->setVariable("PHOTO-URL", $href);
            $t->setVariable("LOCATION", $location);
            $t->setVariable("DATE", $year == 0 ? 'unknown' : $year);
            $t->parseCurrentBlock();

            $col = $n % $THUMBNAIL_COLUMNS;
            if ($col == $THUMBNAIL_COLUMNS - 1)
            {
                $t->setCurrentBlock("THUMBNAIL-ROW");
                $t->parseCurrentBlock();
            }
        }

        $n++;
    }
    mysql_free_result($stmt);

    #
    # Display the navigation links
    #
    show_navigation($db, $t, $page, $npages, $order, $mode);

    $t->setCurrentBlock("MAIN");
    $t->setVariable("TITLE", $title);
    $t->setVariable("INTRODUCTION", $intro);
    $t->parseCurrentBlock();

    $t->show();
}

#
# Display the results page selector
#
function show_navigation($db, &$t, $page, $npages, $order, $mode)
{
    #
    # Convert the URL query parameters into an associative array
    #
    $url = parse_url($_SERVER["REQUEST_URI"]);
    if (array_key_exists("query", $url))
        $args = explode("&", $url["query"]);
    else
        $args = array();

    $argv = array();
    foreach ($args as $kv)
    {
        list($key, $value) = explode("=", $kv);
        $argv[$key] = $value;
    }

    #
    # Display navigation to change mode or ordering
    #
    $t->setCurrentBlock("MAIN");
    $m_argv = $argv;

    if ($mode == "thumbnail")
    {
        $m_argv["mode"] = "listing";
        unset($m_argv["order"]);
        unset($m_argv["page"]);
        $t->setVariable("ALT-DISPLAY", "Listing");
        $t->setVariable("ALT-DISPLAY-URL", encode_argv_url($m_argv));
    }
    else
    {
        unset($m_argv["mode"]);
        $m_argv["page"] = 1;
        $t->setVariable("ALT-DISPLAY", "Thumbnails");
        $t->setVariable("ALT-DISPLAY-URL", encode_argv_url($m_argv));
    }

    $m_argv = $argv;
    if ($order == "location")
    {
        $m_argv["order"] = "year";
        $m_argv["page"] = 1;
        $t->setVariable("ALT-ORDER", "Year");
        $t->setVariable("ALT-ORDER-URL", encode_argv_url($m_argv));
    }
    else
    {
        unset($m_argv["order"]);
        $m_argv["page"] = 1;
        $t->setVariable("ALT-ORDER", "Location");
        $t->setVariable("ALT-ORDER-URL", encode_argv_url($m_argv));
    }

    if ($npages <= 1)
        return;

    if ($mode == "thumbnail")
    {
        #
        # Instantiate the page navigation templates for the top and bottom of
        # the page
        #
        for ($x = 1; $x <= 2; $x++)
        {
            if ($page > 1)
            {
                $t->setCurrentBlock("PAGE-PREV-ACTIVE$x");
                $t->setVariable("FIRST-URL$x",
                    set_and_encode_argv_url($argv, "page", 1));
                $t->setVariable("PREV-URL$x",
                    set_and_encode_argv_url($argv, "page", $page - 1));
            }
            else
                $t->touchBlock("PAGE-PREV-INACTIVE$x");

            if ($page < $npages)
            {
                $t->setCurrentBlock("PAGE-NEXT-ACTIVE$x");
                $t->setVariable("LAST-URL$x",
                    set_and_encode_argv_url($argv, "page", $npages));
                $t->setVariable("NEXT-URL$x",
                    set_and_encode_argv_url($argv, "page", $page + 1));
            }
            else
                $t->touchBlock("PAGE-NEXT-INACTIVE$x");

            $t->setCurrentBlock("PAGE-SELECT$x");
            $t->setVariable("PAGE$x", $page);
            $t->setVariable("NPAGES$x", $npages);
            $t->parseCurrentBlock();
        }
    }
}

#
# Construct a statement to retrieve photos matching the given theme
# identifier (r_location_photo.themes).
#
function stmt_theme($db, $theme, $sorting, $limits)
{
    if ($sorting == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "RL.location_name, RP.seqno";

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            RP.owner,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.status = 'Y'
            and
            FIND_IN_SET('$theme', RP.themes)
        order by
            $sort
    ";

    return create_statement($db, $sql, $limits);
}

#
# Construct a statement to retrieve "historic" photos.
#
function stmt_historic($db, $sorting, $limits)
{
    $HISTORIC_YEARS = 20;

    if ($sorting == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "RL.location_name, RP.seqno";

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            RP.owner,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.status = 'Y'
            and
            RP.year < YEAR(CURDATE()) - $HISTORIC_YEARS
            and
            RP.year > 0
            and
            RP.year is not null
        order by
            $sort
    ";

    return create_statement($db, $sql, $limits);
}

#
# Construct a statement to retrieve recently-released photos.
#
function stmt_recent($db, $sorting, $limits)
{
    $RECENT_DAYS = 28;

    if ($sorting == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "DATE(RP.submit_date) desc, RL.location_name, RP.seqno";

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            RP.owner,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.status = 'Y'
            and
            RP.submit_date > DATE_SUB(sysdate(), INTERVAL $RECENT_DAYS DAY)
        order by
            $sort
    ";

    return create_statement($db, $sql, $limits);
}

#
# Construct a statement to retrieve tunnel photos.
#
function stmt_tunnel($db, $sorting, $limits)
{
    if ($sorting == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "RL.location_name, RP.seqno";

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            RP.owner,
            RP.caption
        from
            r_line_location RL,
            r_location L,
            r_location_photo RP
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            L.location_state = RP.location_state
            and
            L.location_name = RP.location_name
            and
            RP.status = 'Y'
            and
            L.type = 'tunnel'
        order by
            $sort
    ";

    return create_statement($db, $sql, $limits);
}

#
# Construct a statement to retrieve photos for a given owner.
#
function stmt_owner($db, $sorting, $owner, $limits)
{
    if ($sorting == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "RL.location_name, RP.seqno";

    if ($owner == "Rolfe Bozier")
        $extra = "or RP.owner is null";
    else
        $extra = "";

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            RP.owner,
            RP.caption
        from
            r_line_location RL,
            r_location L,
            r_location_photo RP
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            L.location_state = RP.location_state
            and
            L.location_name = RP.location_name
            and
            RP.status = 'Y'
            and
            (RP.owner = '$owner' $extra)
        order by
            $sort
    ";

    return create_statement($db, $sql, $limits);
}

#
# Execute the statement once to getthe count of all rows, then execute a
# second time with limits applied; the second one will be used to 
# retrieve the subset of rows.
#
function create_statement($db, $sql, $limits)
{
    $stmt = mysql_query($sql, $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $nrows = mysql_num_rows($stmt);

    mysql_free_result($stmt);

    $stmt = mysql_query("$sql limit $limits", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    return array($stmt, $nrows);
}

function encode_argv_url($argv)
{
    $params = array();
    foreach ($argv as $k => $v)
    {
        $params[] = "$k=$v";
    }

    return "?" . urlenc(implode("&", $params));
}

function set_and_encode_argv_url($argv, $key, $value)
{
    if (strlen($value) > 0)
        $argv[$key] = $value;
    else
        unset($argv[$key]);

    return encode_argv_url($argv);
}

?>


