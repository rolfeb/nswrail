<?php

require "site.inc";

/*
 * Return general details for a location
 */
/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return array|null
 */
function get_location_details($db, $state, $location)
{
    $result = null;

    /*
     * Get the location details
     */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            RL.line_state,
            RL.line_name,
            L.type,
            L.status,
            L.distance,
            L.geo_x,
            L.geo_y,
            L.geo_exact,
            L.version
        from
            r_location L,
            r_line_location RL
        where
            L.location_state = ?
            and
            L.location_name = ?
            and
            RL.location_state = L.location_state
            and
            RL.location_name = L.location_name
            and
            RL.mainline = 'Y'
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->store_result();  /* for num rows */
    $stmt->bind_result($line_state, $line_name, $type, $status, $distance,
        $geo_x, $geo_y, $geo_exact, $version);

    if ($stmt->fetch())
    {
        if (!$type)
            $type = "unknown";

        if (!$status)
            $status = "unknown";

        $desc = get_location_text($db, $state, $location, 'DESC');
        $curr = get_location_text($db, $state, $location, 'CURR');
        $nphoto = get_location_nphotos($db, $state, $location);
        $ndiagram = get_location_ndiagrams($db, $state, $location);

        $result = array(
            "line_state" => $line_state,
            "line_name" => $line_name,
            "type"      => $type,
            "status"    => $status,
            "distance"  => $distance,
            "geox"      => $geo_x,
            "geoy"      => $geo_y,
            "geoexact"  => $geo_exact,
            "desc"      => $desc,
            "curr"      => $curr,
            "nphoto"    => $nphoto,
            "ndiagram"  => $ndiagram,
            "version"   => $version,
        );
    }
    $stmt->close();

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LAT.distance,
            LAT.via_location
        from
            r_location_altdist LAT
        where
            LAT.location_state = ?
            and
            LAT.location_name = ?
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($altdist, $via_location);

    if ($stmt->fetch())
    {
        $result["altdist"] = $altdist;
        $result["altvia"] = $via_location;
    }
    $stmt->close();

    return $result;
}

/*
 * Return location history information
 */
/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return array
 */
function get_location_history($db, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LEV.type,
            LEV.day,
            LEV.month,
            LEV.year,
            LEV.year_error,
            LEV.current_name,
            LEV.text
        from
            r_location_event LEV
        where
            LEV.location_state = ?
            and
            LEV.location_name = ?
        order by
            LEV.seqno
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($type, $day, $month, $year, $year_error, $name, $text);

    $i = 0;
    $result = array();
    while ($stmt->fetch())
    {
        $result[$i] = array(
            "type"          => $type,
            "day"           => $day,
            "month"         => $month,
            "year"          => $year,
            "year_error"    => $year_error,
            "name"          => $name,
            "text"          => $text,
        );

        $i++;
    }
    $stmt->close();

    return $result;
}

/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @param $type
 * @return string
 */
function get_location_text($db, $state, $location, $type)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LT.text
        from
            r_location_text LT
        where
            LT.location_state = ?
            and
            LT.location_name = ?
            and
            LT.type = ?
        order by
            LT.seqno desc
        limit 1
    ");

    $stmt->bind_param("sss", $state, $location, $type);
    $stmt->execute();
    $stmt->bind_result($text);

    if (!$stmt->fetch())
        $text = "";

    $stmt->close();

    return $text;
}

/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return int
 */
function get_location_nphotos($db, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            count(*)
        from
            r_location_photo LP
        where
            LP.location_state = ?
            and
            LP.location_name = ?
            and
            LP.hold is null
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($nphotos);

    if (!$stmt->fetch())
        $nphotos = 0;

    $stmt->close();

    return $nphotos;
}

/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return int
 */
function get_location_ndiagrams($db, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            count(*)
        from
            r_location_diagram LD
        where
            LD.location_state = ?
            and
            LD.location_name = ?
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($ndiagrams);

    if (!$stmt->fetch())
        $ndiagrams = 0;

    $stmt->close();

    return $ndiagrams;
}

/*
 * Return location turntable information
 */
/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return array|null
 */
function get_location_turntables($db, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LTT.type,
            LTT.size_ft,
            LTT.status,
            LTT.sellers,
            LTT.text
        from
            r_location_turntable LTT
        where
            LTT.location_state = ?
            and
            LTT.location_name = ?
        order by
            LTT.seqno
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($type, $size, $status, $sellers, $text);

    $results = array();
    while ($stmt->fetch())
    {
        $data = array(
            "type"      => $type,
            "size"      => $size,
            "status"    => $status,
            "sellers"   => $sellers,
            "text"      => $text,
        );

        array_push($results, $data);
    }
    $stmt->close();

    if (count($results) == 0)
        return null;

    return $results;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_location_history($db, $tp, $state, $location)
{
    $history = get_location_history($db, $state, $location);

    $count = 0;
    foreach ($history as $h)
    {
        $count++;

        $name = $h["name"];
        $date_html = date_cpts2html($h["day"], $h["month"], $h["year"],
            $h["year_error"]);

        switch ($h["type"])
        {
        case "ON":
            $text = sprintf("%s%s",
                $count == 1 ? "Opened" : "Re-opened",
                $name ? " as $name" : "");
            break;

        case "CN":
            $text = "Closed";
            break;
        
        case "RN":
            $text = sprintf("Renamed %s", $h["name"] ? $h["name"] : $location);
            break;

        case "MV":
            $text = "Moved site";
            if ($h["name"])
                $text .= ", renamed $name";
            break;

        case "PL":
            $text = "Platform added";
            if ($h["name"])
                $text .= ", renamed $name";
            break;
        }

        $tp['history'][] = [
            'ne_date' => $date_html,
            'event' => $text,
        ];
    }

    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_location_lines($db, $tp, $state, $location)
{
    /* XXX: move to dbutil.php */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select distinct
            RL.line_state,
            RL.line_name,
            R.description
        from
            r_line R,
            r_line_location RL
        where
            RL.location_state = ?
            and
            RL.location_name = ?
            and
            R.line_state = RL.line_state
            and
            R.line_name = RL.line_name
        order by
            RL.mainline,
            RL.line_state,
            RL.line_name
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($line_state, $line, $description);

    while ($stmt->fetch()) {
        $url = '/lines/details.php?' .
            http_build_query([
                'name' => "$line_state:$line",
            ]);

        $tp['lines'][] = [
            'ne_url' => $url,
            'text' => $description,
        ];
    }
    $stmt->close();

    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_station_details($db, $tp, $state, $location)
{
    /* TODO: implement station details? */

    /*
    $t->setCurrentBlock("OPT-STATION-DETAILS");
    $t->setVariable("STATION-DETAILS", "88m island platform, Pc3 building<br><i>A footbridge allows access to the platform.</i>");
    */
    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_goods_details($db, $tp, $state, $location)
{
    /* TODO: implement goods details? */

    /*
    $t->setCurrentBlock("OPT-GOODS-DETAILS");
    $t->setVariable("GOODS-DETAILS", "Goods shed, Gantry crane, Loading bank");
    */
    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_infra_details($db, $tp, $state, $location)
{
    $infra = [];

    /*
     * Turntable details
     */
    $list = get_location_turntables($db, $state, $location);
    if ($list) {
        $label = sprintf("<b>Turntable%s</b>: ", count($list) > 1 ? "s" : "");

        $entries = [];
        foreach ($list as $x) {
            if (!$x["size"]) {
                $size = "unknown size,";
            } else if (floor($x["size"]) != $x["size"]) {
                $size = sprintf("%d'%.0f\"", floor($x["size"]),
                    ($x["size"] - floor($x["size"])) * 12);
            } else {
                $size = $x["size"] . "'";
            }

            if ($x["type"] == "unknown") {
                $type = "manual?";
            } else {
                $type = $x["type"];
            }

            if ($x["status"] == "unknown") {
                $status = "unknown status";
            } else {
                $status = $x["status"];
            }

            $text = "";
            if ($x["text"]) {
                $text = " (<i>" . htmlspecialchars($x["text"]) . "</i>)";
            }

            array_push($entries, sprintf("%s %s, %s%s", $size, $type, $status, $text));
        }

        array_push($infra, $label . implode("; ", $entries));
    }

    if (count($infra)) {
        $tp['ne_other_details'] = implode("<br>", $infra);
    }

    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_link_details($db, $tp, $state, $location)
{
    /* XXX: move to dbutil.php */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LU.text,
            LU.url
        from
            r_location_url LU
        where
            LU.location_state = ?
            and
            LU.location_name = ?
        order by
            LU.seqno
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($text, $url);

    while ($stmt->fetch()) {
        $tp['links'][] = [
            'ne_url' => $url,
            'text' => $text,
        ];
    }
    $stmt->close();

    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_photo_details($db, $tp, $state, $location)
{
    /*
     * Build a list of the years for which we have photos for this location
     */
    /* XXX: move to dbutil.php */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select distinct
            LP.year,
            LP.daterange
        from
            r_location_photo LP
        where
            LP.location_state = ?
            and
            LP.location_name = ?
            and
            LP.hold is null
        order by
            LP.year
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($year, $year_error);

    $years = "";
    while ($stmt->fetch()) {
        if ($years)
            $years .= ", ";

        $years .= date_cpts2text(0, 0, $year, $year_error);
    }
    $stmt->close();

    /* only display the block if it is not empty */
    if ($years) {
        $tp['years'] = $years;
    }

    return $tp;
}

/*
 * Add any photographs for this location to the page
 */
/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @param $line_state
 * @param $line
 * @param $segment
 * @return mixed
 */
function add_photo_thumbnails($db, $tp, $state, $location, $line_state, $line, $segment)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LP.seqno,
            LP.file,
            LP.hold,
            LP.year,
            IFNULL(U.fullname, IFNULL(LP.legacy_owner, 'Rolfe Bozier')) as fullname,
            LP.owner_uid,
            LP.caption
        from
            r_location_photo LP left join r_user U on LP.owner_uid = U.uid
        where
            LP.location_state = ?
            and
            LP.location_name = ?
        order by
            LP.year,
            LP.month,
            LP.day,
            LP.seqno
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($seqno, $file, $hold, $year, $owner_fullname, $owner_uid,
        $caption);

    $curr_decade = '';

    $last_d = -1;
    while ($stmt->fetch()) {
        if ($hold == '') {
            $base = preg_replace("/.jpg$/", "", $file);

            if (!$year or $year < 1800) {
                $decade = "Unknown";
            } else {
                $decade = sprintf("%d - %d", floor($year / 10) * 10, ceil($year / 10) * 10);
            }

            if ($decade != $curr_decade) {
                $tp['decades'][] = [
                    'decade' => $decade,
                    'photos' => [],
                ];
                $last_d++;
                $curr_decade = $decade;
            }

            $tp['decades'][$last_d]['photos'][] = [
                'photo_img' => "/media/photos/$base.jpg",
                'thumb_img' => "/media/photos/thumbnails/$base.jpg",
                'date' => $year == 0 ? 'unknown' : $year,
                'text' => $caption,
                'uid' => $owner_uid,
                'fullname' => $owner_fullname,
            ];
        }
    }
    $stmt->close();
    return $tp;
}

/**
 * Add any diagrams for this location to the page
 *
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @return mixed
 */
function add_diagram_images($db, $tp, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LD.seqno,
            LD.year,
            LD.file
        from
            r_location_diagram LD
        where
            LD.location_state = ?
            and
            LD.location_name = ?
        order by
            LD.year
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($seqno, $year, $file);

    while ($stmt->fetch()) {
        $tp['diagrams'][] = [
            'year' => $year,
            'img' => "/locations/diagrams/$file.gif",
        ];
    }
    $stmt->close();

    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $location
 * @param $line_state
 * @param $line
 * @param $segment
 * @return mixed
 */
function add_prev_next_links($db, $tp, $state, $location, $line_state, $line, $segment)
{
    $prev_state = null;
    $prev_location = null;
    $next_state = null;
    $next_location = null;

    $sql_base = "
        select
            RL.location_state,
            RL.location_name
        from
            r_line_location RL,
            r_line_location RLn
        where
            RLn.line_state = ?
            and
            RLn.line_name = ?
            and
            RLn.segment = ?
            and
            RLn.location_state = ?
            and
            RLn.location_name = ?
            and
            RL.line_state = RLn.line_state
            and
            RL.line_name = RLn.line_name
            and
            RL.segment = RLn.segment
    ";

    $stmt = $db->stmt_init();
    $stmt->prepare("
        $sql_base
        and
        RL.seqno = RLn.seqno - 1
    ");

    $stmt->bind_param("ssiss", $line_state, $line, $segment, $state, $location);
    $stmt->execute();
    $stmt->bind_result($prev_state, $prev_location);

    $stmt->fetch();
    $stmt->close();

    $stmt = $db->stmt_init();
    $stmt->prepare("
        $sql_base
        and
        RL.seqno = RLn.seqno + 1
    ");

    $stmt->bind_param("ssiss", $line_state, $line, $segment, $state, $location);
    $stmt->execute();
    $stmt->bind_result($next_state, $next_location);

    $stmt->fetch();
    $stmt->close();

    if ($prev_location) {
        $tp['ne_line_prev_url'] =
            "/locations/details.php?"
            . http_build_query([
                'name' => "$prev_state:$prev_location",
                'line' => "$line_state:$line:$segment",
            ]);
        $tp['line_prev_name'] = $prev_location;
    }

    if ($next_location) {
        $tp['ne_line_next_url'] =
            "/locations/details.php?"
            . http_build_query([
                'name' => "$next_state:$next_location",
                'line' => "$line_state:$line:$segment",
            ]);
        $tp['line_next_name'] = $next_location;
    }

    if ($prev_location) {
        if ($next_location) {
            $tp['opt_prev_next_icon'] = "/c/images/locn_nav_np.png";
        }
        else {
            $tp['opt_prev_next_icon'] = "/c/images/locn_nav_p.png";
        }
    } else {
        $tp['opt_prev_next_icon'] = "/c/images/locn_nav_n.png";
    }
    
    return $tp;
}

/**
 * @return array|mixed
 */
function run_locations_details()
{
    /** @var mysqli $db */
    global $db;

    list($state, $location) = param_get_string2('name');
    list($line_state, $line_name, $segment) = param_get_string3_opt('line');

    $location = make_canonical_name($location);

    $tp = [
        'map_message1' => '',
        'map_message2' => '',
        'map_message3' => '',
        'lines' => [],
        'history' => [],
        'links' => [],
        'decades' => [],
        'diagrams' => [],
    ];

    $l = get_location_details($db, $state, $location);

    $head_extra = '';

    if ($l["geox"] and $l["geoy"])
    {
        $latlong_html = sprintf("(%.4f&deg;, %.4f&deg;)",
                        $l["geox"], $l["geoy"]);
    }
    else
        $latlong_html = "unknown";
    if ($l["geoexact"] and $l["geoexact"] == "Y")
        $posaccuracy = 'exact';
    else
        $posaccuracy = 'approx';

    if (!$line_state)
        $line_state = $l["line_state"];

    if ($line_state == "VIC")
        $origin = "Melbourne";
    else
        $origin = "Sydney";

    if ($l["distance"])
        $km_syd = sprintf("%.3f", $l["distance"]);
    else
        $km_syd = "unknown";

    $tp = add_location_history($db, $tp, $state, $location);
    $tp = add_location_lines($db, $tp, $state, $location);
    $tp = add_station_details($db, $tp, $state, $location);
    $tp = add_goods_details($db, $tp, $state, $location);
    $tp = add_infra_details($db, $tp, $state, $location);
    $tp = add_link_details($db, $tp, $state, $location);
    $tp = add_photo_details($db, $tp, $state, $location);

    if ($line_state != "" and $line_name != "")
        $tp = add_prev_next_links($db, $tp, $state, $location, $line_state, $line_name, $segment);

    if (array_key_exists("altdist", $l))
    {
        $tp['alt_distance'] = sprintf("%.3f", $l["altdist"]);
        $tp['alt_origin'] = $origin;
        $tp['alt_via'] = $l["altvia"];
    }

    /*
     * Add any photos or diagrams to the page
     */
    $tp = add_photo_thumbnails($db, $tp, $state, $location, $line_state, $line_name, $segment);
    ### $tp = add_diagram_images($db, $tp, $state, $location);

    /*
     * Add the location map
     */
    if ($l["geox"] and $l["geoy"]) {
        if (!$l["geoexact"] || $l["geoexact"] == "N")
        {
            $tp['map_message1'] = "(Approximate location only)";
        }

        $tp['map_geox'] = $l["geox"];
        $tp['map_geoy'] = $l["geoy"];
        $tp['map_scale'] = 14;
        $tp['google_map_apikey'] = 'AIzaSyAcfRpOxo-uKn1nY7XbBChfPWZhkXPnEPs';
    }
    else {
        $tp['map_message2'] = "Location not known";
    }

    $title = locn_fulltitle($location, $l["type"]);

    $tp['title'] = $title;
    $tp['location'] = $title;

    $tp['facility'] = locn_type2text($l["type"]);
    $tp['status'] = locn_status2text($l["status"]);
    $tp['ne_latlong'] = $latlong_html;
    $tp['posaccuracy'] = $posaccuracy;
    $tp['distance'] = $km_syd;
    $tp['origin'] = $origin;

    $tp['desc_text'] = $l["desc"];
    $tp['curr_text'] = $l["curr"];

    # $head_extra .= '<script src="/c/js/ajaxutil.js" type="text/javascript"></script>';
    # $head_extra .= '<script src="locations.js" type="text/javascript" ></script>';

    $args = array();
    if ($head_extra)
        $args['HEAD-EXTRA'] = $head_extra;

    return $tp;
}

normal_page_wrapper('run_locations_details', 'location-details.latte');
