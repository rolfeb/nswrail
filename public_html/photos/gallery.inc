<?php

require_once "site.inc";

abstract class PhotoThemes
{
    const Diagram       = 0;
    const Historic      = 1;
    const Night         = 2;
    const Owner         = 3;
    const Recent        = 4;
    const Safeworking   = 5;
    const Signalbox     = 6;
    const Tunnel        = 7;
    const Turntable     = 8;
}

function theme_gallery($theme, $themeextra, $title, $intro)
{
    global $db;

    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("gallery.tpl");

    #
    # Get current display state
    #
    $order = quote_external(get_post("order", ""));     # location, year
    $mode = quote_external(get_post("mode", ""));       # thumbnail, text
    $page = quote_external(get_post("page", "1"));

    if ($order != "location" && $order != "year") {
        $order = "location";
    }
    if ($mode != "thumbnail" && $mode != "listing") {
        $mode = "thumbnail";
    }
    if ($page < 1) {
        $page = 1;
    }

    #
    # XXX: If we are running in thumbnail mode, manage the paging of results
    #
    $THUMBNAIL_COLUMNS = 4;
    $THUMBNAIL_PAGEROWS = 4;

    #
    # Obtain the SQL statement returning the apppropriate photo list. If
    # we are displaying paged output, calculate the rows to fetch.
    #
    $select_limits = "999999";
    if ($mode == "thumbnail") {
        $first = ($page - 1) * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $count = $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $select_limits = "$first,$count";
    }

    switch ($theme)
    {
    case PhotoThemes::Diagram:
    case PhotoThemes::Night:
    case PhotoThemes::Safeworking:
    case PhotoThemes::Signalbox:
    case PhotoThemes::Turntable:
        $sql = sql_photo_tag($theme, $order);
        break;

    case PhotoThemes::Historic:
        $sql = sql_historic($order);
        break;

    case PhotoThemes::Recent:
        $sql = sql_recent($order);
        break;

    case PhotoThemes::Tunnel:
        $sql = sql_tunnel($order);
        break;

    case PhotoThemes::Owner:
        $sql = sql_owner($order, $themeextra);
        break;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare($sql)
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $stmt->store_result();
    $nrows = $stmt->num_rows;
    $stmt->close();

    $stmt = $db->stmt_init();
    $stmt->prepare("$sql limit $select_limits")
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $stmt->bind_result($state, $line, $location_state, $location, $file,
        $seqno, $year, $owner_fullname, $owner_uid, $caption);

    $npages = 1;
    if ($mode == "thumbnail") {
        $npages = ceil($nrows / ($THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS));

        if ($page > 1) {
            $prev_page_link = $page - 1;
        }
        if ($page < $npages) {
            $next_page_link = $page + 1;
        }

        $first = ($page - 1) * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $last = $page * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS - 1;
        $select_limits = "$first,$last";
    }

    #
    # Display the photos
    #
    $n = 0;
    while ($stmt->fetch()) {
        $photo = "/media/photos/$file";
        $thumb = "/media/photos/thumbnails/$file";

        if ($mode == "listing") {
            #
            # Listing mode
            #
            $t->setCurrentBlock("LISTING-ROW");
            $t->setVariable("PHOTO-IMG", $photo);
            $t->setVariable("LOCATION", $location);
            $t->setVariable("DATE", $year == 0 ? 'unknown' : $year);
            $t->setVariable("TEXT", htmlentities($caption));
            $t->setVariable("UID", $owner_uid);
            $t->setVariable("FULLNAME", htmlentities($owner_fullname));
            $t->parseCurrentBlock();
        } else {
            #
            # Thumbnail mode
            #
            $t->setCurrentBlock("CELL");
            $t->setVariable("PHOTO-IMG", $photo);
            $t->setVariable("THUMB-IMG", $thumb);
            $t->setVariable("LOCATION", $location);
            $t->setVariable("DATE", $year == 0 ? 'unknown' : $year);
            $t->setVariable("TEXT", htmlentities($caption));
            $t->setVariable("UID", $owner_uid);
            $t->setVariable("FULLNAME", htmlentities($owner_fullname));
            $t->parseCurrentBlock();

            $col = $n % $THUMBNAIL_COLUMNS;
            if ($col == $THUMBNAIL_COLUMNS - 1) {
                $t->parse("ROW");
            }
        }

        $n++;
    }
    if ($mode == "listing") {
        $t->parse("LISTING");
    } else {
        $t->parse("GALLERY");
    }
    $stmt->close();

    # display the navigation links
    show_navigation($t, $page, $npages, $order, $mode);

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $title);
    $t->setVariable("INTRODUCTION", $intro);
    $t->parseCurrentBlock();

    display_page($title, $t->get("CONTENT"),
        array(
            'HEAD-EXTRA'    => implode(file('gallery.hdr'), ""),
        )
    );
}

#
# Display the results page selector
#
function show_navigation(&$t, $page, $npages, $order, $mode)
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
    $t->setCurrentBlock("CONTENT");
    $m_argv = $argv;

    if ($mode == "thumbnail") {
        $m_argv["mode"] = "listing";
        unset($m_argv["order"]);
        unset($m_argv["page"]);
        $t->setVariable("ALT-DISPLAY", "Listing");
        $t->setVariable("ALT-DISPLAY-URL", encode_argv_url($m_argv));
    } else {
        unset($m_argv["mode"]);
        $m_argv["page"] = 1;
        $t->setVariable("ALT-DISPLAY", "Thumbnails");
        $t->setVariable("ALT-DISPLAY-URL", encode_argv_url($m_argv));
    }

    $m_argv = $argv;
    if ($order == "location") {
        $m_argv["order"] = "year";
        $m_argv["page"] = 1;
        $t->setVariable("ALT-ORDER", "Year");
        $t->setVariable("ALT-ORDER-URL", encode_argv_url($m_argv));
    } else {
        unset($m_argv["order"]);
        $m_argv["page"] = 1;
        $t->setVariable("ALT-ORDER", "Location");
        $t->setVariable("ALT-ORDER-URL", encode_argv_url($m_argv));
    }

    if ($npages <= 1) {
        return;
    }

    if ($mode == "thumbnail") {
        #
        # Instantiate the page navigation templates for the top and bottom of
        # the page
        #
        for ($x = 1; $x <= 2; $x++) {
            if ($page > 1) {
                $t->setCurrentBlock("PAGE-PREV-ACTIVE$x");
                $t->setVariable("FIRST-URL$x",
                    set_and_encode_argv_url($argv, "page", 1));
                $t->setVariable("PREV-URL$x",
                    set_and_encode_argv_url($argv, "page", $page - 1));
            } else {
                $t->touchBlock("PAGE-PREV-INACTIVE$x");
            }

            if ($page < $npages) {
                $t->setCurrentBlock("PAGE-NEXT-ACTIVE$x");
                $t->setVariable("LAST-URL$x",
                    set_and_encode_argv_url($argv, "page", $npages));
                $t->setVariable("NEXT-URL$x",
                    set_and_encode_argv_url($argv, "page", $page + 1));
            } else {
                $t->touchBlock("PAGE-NEXT-INACTIVE$x");
            }

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
function sql_photo_tag($theme, $sorting)
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
            IFNULL(U.fullname, RP.legacy_owner) as fullname,
            RP.owner_uid,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP left join r_user U on RP.owner_uid = U.uid
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.hold is null
            and
            FIND_IN_SET('$theme', RP.tags) > 0
        order by
            $sort
    ";

    return $sql;
}

#
# Construct a statement to retrieve "historic" photos.
#
function sql_historic($sorting)
{
    $age = get_setting('HISTORIC_PHOTO_YEARS');

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
            IFNULL(U.fullname, RP.legacy_owner) as fullname,
            RP.owner_uid,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP left join r_user U on RP.owner_uid = U.uid
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.hold is null
            and
            RP.year < YEAR(CURDATE()) - $age
            and
            RP.year > 0
            and
            RP.year is not null
        order by
            $sort
    ";

    return $sql;
}

#
# Construct a statement to retrieve recently-released photos.
#
function sql_recent($sorting)
{
    $RECENT_DAYS = get_setting('RECENT_PHOTO_UPLOAD_WEEKS') * 4;

    if ($sorting == "year")
        $sort = "RP.year, RL.location_name, RP.seqno";
    else
        $sort = "DATE(RP.publish_date) desc, RL.location_name, RP.seqno";

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            IFNULL(U.fullname, RP.legacy_owner) as fullname,
            RP.owner_uid,
            RP.caption
        from
            r_line_location RL,
            r_location_photo RP left join r_user U on RP.owner_uid = U.uid
        where
            RL.location_state = RP.location_state
            and
            RL.location_name = RP.location_name
            and
            RL.mainline = 'Y'
            and
            RP.hold is null
            and
            RP.publish_date > DATE_SUB(sysdate(), INTERVAL $RECENT_DAYS DAY)
        order by
            $sort
    ";

    return $sql;
}

#
# Construct a statement to retrieve tunnel photos.
#
function sql_tunnel($sorting)
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
            IFNULL(U.fullname, RP.legacy_owner) as fullname,
            RP.owner_uid,
            RP.caption
        from
            r_line_location RL,
            r_location L,
            r_location_photo RP left join r_user U on RP.owner_uid = U.uid
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
            RP.hold is null
            and
            L.type = 'tunnel'
        order by
            $sort
    ";

    return $sql;
}

#
# Construct a statement to retrieve photos for a given owner.
#
function sql_owner($sorting, $owner)
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
            IFNULL(U.fullname, RP.legacy_owner) as fullname,
            RP.owner_uid,
            RP.caption
        from
            r_line_location RL,
            r_location L,
            r_location_photo RP left join r_user U on RP.owner_uid = U.uid
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
            RP.hold is null
            and
            (owner = '$owner' $extra)
        order by
            $sort
    ";

    return $sql;
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
