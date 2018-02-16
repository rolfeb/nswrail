<?php

require "site.inc";

abstract class PhotoThemes
{
    # the following must match values in r_location_photo.tags:
    const Diagram       = 'diagram';
    const Night         = 'night';
    const Safeworking   = 'safeworking';
    const Signalbox     = 'box';
    const Turntable     = 'turntable';
    # other themes:
    const Historic      = 'historic';
    const Owner         = 'owner';
    const Recent        = 'recent';
    const Tunnel        = 'tunnel';
}

function theme_gallery($theme, $themeextra, $title, $intro)
{
    global $db;

    #
    # Get current display state
    #
    $order = param_get_string_opt("order");     # = "location", "year"
    $mode = param_get_string_opt("mode");       # = "thumbnail", "listing"
    $page = param_get_integer_opt("page");

    if ($order != "location" && $order != "year") {
        $order = "location";
    }
    if ($mode != "thumbnail" && $mode != "listing") {
        $mode = "thumbnail";
    }
    if ($page === NULL || $page < 1) {
        $page = 1;
    }

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
    if ($mode == "thumbnail") {
        $first = ($page - 1) * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $count = $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $select_limits = "$first,$count";
    }

    $tp = [
        'title' => $title,
        'introduction' => $intro,

        'is_listing' => ($mode == "listing" ? 1 : 0),

        'photos' => [],
    ];

    switch ($theme)
    {
    case PhotoThemes::Diagram:
    case PhotoThemes::Night:
    case PhotoThemes::Safeworking:
    case PhotoThemes::Signalbox:
    case PhotoThemes::Turntable:
        list($sql, $bind_types, $bind_params) = sql_photo_tag($theme, $order);
        break;

    case PhotoThemes::Historic:
        list($sql, $bind_types, $bind_params) = sql_historic($order);
        break;

    case PhotoThemes::Recent:
        list($sql, $bind_types, $bind_params) = sql_recent($order);
        break;

    case PhotoThemes::Tunnel:
        list($sql, $bind_types, $bind_params) = sql_tunnel($order);
        break;

    case PhotoThemes::Owner:
        list($sql, $bind_types, $bind_params) = sql_owner($order, $themeextra);
        break;
    }

    $stmt = $db->stmt_init();
    $stmt->prepare($sql);
    if (count($bind_types) > 0) {
        $typestr = join('', $bind_types);
        $valrefs = array_merge([&$typestr], $bind_params);
        call_user_func_array(array($stmt, 'bind_param'), $valrefs);
    }
    $stmt->execute();
    $stmt->store_result();
    $nrows = $stmt->num_rows;
    $stmt->close();

    $stmt = $db->stmt_init();
    $stmt->prepare("$sql limit $select_limits");
    if (count($bind_types) > 0) {
        call_user_func_array(array($stmt, 'bind_param'), $valrefs);
    }
    $stmt->execute();
    $stmt->bind_result($state, $line, $location_state, $location, $file,
        $seqno, $year, $owner_fullname, $owner_uid, $caption);

    if ($mode == "thumbnail") {
        $npages = ceil($nrows / ($THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS));

        $first = ($page - 1) * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS;
        $last = $page * $THUMBNAIL_COLUMNS * $THUMBNAIL_PAGEROWS - 1;
        $select_limits = "$first,$last";

        $tp['page'] = $page;
        $tp['npages'] = $npages;
        $tp['num_columns'] = $THUMBNAIL_COLUMNS;
    } else {
        $npages = 1;
    }

    $tp = add_navigation($tp, $page, $npages, $order, $mode);

    while ($stmt->fetch()) {
        $tp['photos'][] = [
            'photo_img' => "/media/photos/$file",
            'thumb_img' => "/media/photos/thumbnails/$file",
            'location' => $location,
            'date' => $year == 0 ? 'unknown' : $year,
            'text' => $caption,
            'uid' => $owner_uid,
            'fullname' => $owner_fullname,
        ];
    }
    $stmt->close();

    # TODO: add dummy entries to pad out a full row if there is only a single partial row

    normal_page('photo-gallery.latte', $tp);
}

#
# Display the results page selector
#
function add_navigation($tp, $page, $npages, $order, $mode)
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
    $m_argv = $argv;

    if ($mode == "thumbnail") {
        $m_argv["mode"] = "listing";
        unset($m_argv["order"]);
        unset($m_argv["page"]);
        $tp['alt_display_label'] = 'Listing';
    } else {
        unset($m_argv["mode"]);
        $m_argv["page"] = 1;
        $tp['alt_display_label'] = 'Thumbnails';
    }
    $tp['alt_display_url'] = encode_argv_url($m_argv);

    $m_argv = $argv;
    if ($order == "location") {
        $m_argv["order"] = "year";
        $m_argv["page"] = 1;
        $tp['alt_order_label'] = 'Year';
    } else {
        unset($m_argv["order"]);
        $m_argv["page"] = 1;
        $tp['alt_order_label'] = 'Location';
    }
    $tp['alt_order_url'] = encode_argv_url($m_argv);

    if ($npages <= 1) {
        return $tp;
    }

    if ($mode == "thumbnail") {
        #
        # Instantiate the page navigation templates for the top and bottom of
        # the page
        #
        $tp['page'] = $page;
        $tp['npages'] = $npages;

        if ($page > 1) {
            $tp['nav_first_page_url'] =
                set_and_encode_argv_url($argv, "page", 1);
            $tp['nav_prev_page_url'] =
                set_and_encode_argv_url($argv, "page", $page - 1);
        }

        if ($page < $npages) {
            $tp['nav_next_page_url'] = 
                set_and_encode_argv_url($argv, "page", $page + 1);
            $tp['nav_last_page_url'] = 
                set_and_encode_argv_url($argv, "page", $npages);
        }
    }

    return $tp;
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as fullname,
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

    return [$sql, [], []];
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as fullname,
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

    return [$sql, [], []];
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as fullname,
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

    return [$sql, [], []];
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as fullname,
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

    return [$sql, [], []];
}

#
# Construct a statement to retrieve photos for a given owner.
#
function sql_owner($sorting, $owner_insecure)
{
    if ($sorting == 'year') {
        $sort = 'RP.year, RL.location_name, RP.seqno';
    } else {
        $sort = 'RL.location_name, RP.seqno';
    }

    if ($owner_insecure == 'Rolfe Bozier') {
        $extra = 'or RP.owner is null';
    } else {
        $extra = '';
    }

    $sql = "
        select
            RL.line_state,
            RL.line_name,
            RL.location_state,
            RL.location_name,
            RP.file,
            RP.seqno,
            RP.year,
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as fullname,
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
            (
                RP.legacy_owner = ?
                or
                U.fullname = ? $extra
            )
        order by
            $sort
    ";

    $bind_types = ['s', 's'];
    $bind_values = [&$owner_insecure, &$owner_insecure];

    return [$sql, $bind_types, $bind_values];
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
