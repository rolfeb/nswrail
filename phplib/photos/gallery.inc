<?php

require "site.inc";

/**
 * Class PhotoThemes
 */
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

/**
 * @param $params
 * @return string
 */
function encode_argv_url($params)
{
    $args = [];
    foreach ($params as $k => $v) {
        $args[] = "$k=$v";
    }

    return "?" . urlenc(implode("&", $args));
}

#
# Display the results page selector
#
/**
 * @param $tp
 * @param $page
 * @param $npages
 * @param $order
 * @param $mode
 * @param $extra_params
 * @return mixed
 */
function add_navigation($tp, $page, $npages, $order, $mode, $extra_params)
{
    #
    # Display links to change mode or ordering
    #
    $params = [
        'page' => 1,
        'order' => $order,
    ];
    $params = array_merge($params, $extra_params);

    if ($mode == "thumbnail") {
        $params["mode"] = "listing";
        $tp['alt_display_label'] = 'Listing';
    } else {
        $params["mode"] = "thumbnail";
        $tp['alt_display_label'] = 'Thumbnails';
    }
    $tp['ne_alt_display_url'] = encode_argv_url($params);

    $params = [
        'page' => 1,
        'mode' => $mode,
    ];
    $params = array_merge($params, $extra_params);

    if ($order == "location") {
        $params["order"] = "year";
        $tp['alt_order_label'] = 'Year';
    } else {
        $params["order"] = "location";
        $tp['alt_order_label'] = 'Location';
    }
    $tp['ne_alt_order_url'] = encode_argv_url($params);

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

        $params = [
            'mode' => $mode,
            'order' => $order,
        ];
        $params = array_merge($params, $extra_params);

        if ($page > 1) {
            $params['page'] = 1;
            $tp['ne_nav_first_page_url'] = encode_argv_url($params);
            $params['page'] = $page - 1;
            $tp['ne_nav_prev_page_url'] = encode_argv_url($params);
        }

        if ($page < $npages) {
            $params['page'] = $page + 1;
            $tp['ne_nav_next_page_url'] = encode_argv_url($params);
            $params['page'] = $npages;
            $tp['ne_nav_last_page_url'] = encode_argv_url($params);
        }
    }

    return $tp;
}

/**
 * @param $theme
 * @param $themeextra
 * @param $title
 * @param $intro
 */
function theme_gallery($theme, $themeextra, $title, $intro)
{
    /** @var mysqli $db */
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

    $extra_param = [];

    switch ($theme)
    {
    case PhotoThemes::Diagram:
    case PhotoThemes::Night:
    case PhotoThemes::Safeworking:
    case PhotoThemes::Signalbox:
    case PhotoThemes::Turntable:
        list($sql, $bind_types, $bind_params) = sql_photo_tag($db, $theme, $order);
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
        $extra_param = ['owner' => $themeextra];
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

        $tp['page'] = $page;
        $tp['npages'] = $npages;
        $tp['num_columns'] = $THUMBNAIL_COLUMNS;
    } else {
        $npages = 1;
    }

    $tp = add_navigation($tp, $page, $npages, $order, $mode, $extra_param);

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

/**
 * Construct a statement to retrieve photos matching the given theme
 * identifier (r_location_photo.themes).
 *
 * @param mysqli $db
 * @param $theme
 * @param $sorting
 * @return array
 */
function sql_photo_tag($db, $theme, $sorting)
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as owner,
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
/**
 * @param $sorting
 * @return array
 */
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as owner,
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
/**
 * @param $sorting
 * @return array
 */
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as owner,
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
/**
 * @param $sorting
 * @return array
 */
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as owner,
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
/**
 * @param $sorting
 * @param $owner_insecure
 * @return array
 */
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
            IFNULL(U.fullname, IFNULL(RP.legacy_owner, 'Rolfe Bozier')) as owner,
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
