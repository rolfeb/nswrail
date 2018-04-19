<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @param $tp
 * @param $state
 * @param $line
 * @return mixed
 */
function add_links($tp, $state, $line)
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LU.text,
            LU.url
            from
                r_line_url LU
            where
                LU.line_state = ?
                and
                LU.line_name = ?
            order by
                LU.seqno
    ");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($text, $url);

    while ($stmt->fetch()) {
        $tp['links'][] = [
            'text' => $text,
            'url' => $url,
        ];
    }
    $stmt->close();

    return $tp;
}

/**
 * @param mysqli $db
 * @param $tp
 * @param $state
 * @param $line
 * @return mixed
 */
function add_locations($db, $tp, $state, $line)
{
    /*
     * Construct location list
     */
    $locations = read_line_locations($state, $line);

    $max_location = count($locations) - 1;

    /*
     * Retrieve the icon[s] for each location
     */
    $location_icons = read_location_icons($state, $line);

    /*
     * Get the segment depth for this line
     */
    $max_depth = read_line_segment_depth($state, $line);
    $tp['max_icons'] = $max_depth + 1;

    /*
     * Display the location information
     */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            L.location_state,
            L.location_name,
            L.type,
            L.status,
            L.distance
        from
            r_location L
        where
            L.location_state = ?
            and
            L.location_name = ?
    ");

    $stmt->bind_result($location_state, $location, $type, $status,
        $distance);

    for ($l = 0; $l <= $max_location; $l++) {
        $data = $locations[$l];
        if (!$data["state"]) {
            die("No icon data for $l!");
        }
        $location_state = $data["state"];
        $location_name = $data["location"];
        $main_segment = $data["segment"];

        /* initialise the icon array */
        $icons = $location_icons["$location_state:$location_name"];

        $stmt->bind_param("ss", $location_state, $location_name);
        $stmt->execute();

        /*
         * Buffer the result to avoid conflict with subsequent fetches for
         * other statement.
         */
        $stmt->store_result();

        if ($stmt->fetch()) {
            $url = "/locations/details.php?"
                . http_build_query([
                    'name' => "$location_state:$location",
                    'line' => "$state:$line:$main_segment",
                ]);

            if ($distance == NULL) {
                $distance = "";
            } else {
                $distance = sprintf("%.3f", $distance);
            }

            $nphotos = get_location_nphotos($db, $location_state, $location);
            $nphotos == 0 && $nphotos = "";

            $nurls = get_location_nurls($db, $location_state, $location);
            $nurls == 0 && $nurls = "";

            $open_date = get_location_open_date($location_state,
                $location);
            $close_date = get_location_close_date($db, $location_state,
                $location);

            if (!$close_date) {
                if ($status == "closed") {
                    $close_date = "unknown";
                } else if ($status == "in use") {
                    $close_date = "-";
                }
            }

            if ($status == "not opened") {
                $open_date = "-";
                $close_date = "-";
            }

            if ($type == "border") {
                $status = "-";
                $open_date = "-";
                $close_date = "-";
            }

            $icon_classes = [];
            for ($i = 0; $i < $max_depth + 1; $i++) {
                if ($icons[$i]) {
                    $icon_classes[] = $icons[$i];
                } else {
                    $icon_classes[] = '';
                }
            }

            $tp['locations'][] = [
                'icons' => $icon_classes,
                'ne_url' => $url,
                'name' => $location,
                'facility' => locn_type2text($type),
                'status' => locn_status2text($status),
                'ne_opened' => $open_date,
                'ne_closed' => $close_date,
                'distance' => $distance,
                'nphotos' => $nphotos,
            ];
        }
    }
    $stmt->close();

    return $tp;
}

/**
 * @param $line_state
 * @param $line_name
 * @return mixed
 */
function read_line_locations($line_state, $line_name)
{
    /** @var mysqli $db */
    global $db;

    /*
     * Construct an array of segments, each element being the list of locations
     * in that segment.
     */

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            RL.segment,
            RL.location_state,
            RL.location_name
        from
            r_line_location RL
        where
            RL.line_state = ?
            and
            RL.line_name = ?
        order by
            RL.segment,
            RL.seqno
    ");

    $stmt->bind_param("ss", $line_state, $line_name);
    $stmt->execute();
    $stmt->bind_result($segment, $state, $location);

    $segment_list = [];

    while ($stmt->fetch())
    {
        if (!array_key_exists($segment, $segment_list)) {
            $segment_list[$segment] = [];
        }

        $data = [];
        $data["state"] = $state;
        $data["location"] = $location;
        $data["segment"] = $segment;
        $data["depth"] = 0;
        $data["segstart"] = 0;
        $data["segrejoin"] = 0;
        $data["segend"] = 0;

        push($segment_list[$segment], $data);
    }
    $stmt->close();

    /* force the main segment status flags */
    $segment0 = &$segment_list[0];
    $segment0[0]["segstart"] = 1;
    $segment0[count($segment0) - 1]["segend"] = 1;

    /*
     * Now flatten the segments into a single list of locations
     */
    $location_list = $segment_list[0];

    $max_segment = count($segment_list) - 1;
    for ($s = 1; $s <= $max_segment; $s++) {
        $segment_ref = &$segment_list[$s];

        $pos1 = index_of($location_list, $segment_ref[0]);
        $pos2 = index_of($location_list, $segment_ref[count($segment_ref)-1]);

        if ($pos1 != -1) {
            $new_depth = $location_list[$pos1]["depth"] + 1;
        } else {
            $new_depth = $location_list[$pos2]["depth"] + 1;
        }

        $insert = $segment_list[$s];

        /* set new depth */
        foreach ($insert as $i => $data) {
            $insert[$i]["depth"] = $new_depth;
        }

        if ($pos1 == -1) {
            # entrant segment
            array_pop($insert);     /* discard last location */

            $location_list[$pos2]["segrejoin"] = 1;

            array_splice($location_list, $pos2, 0, $insert);
        } else if ($pos2 == -1) {
            /* dead end segment */
            array_shift($insert);   /* discard first location */

            $location_list[$pos1]["segstart"] = 1;
            $insert[count($insert) - 1]["segend"] = 1;

            array_splice($location_list, $pos1 + 1, 0, $insert);
        } else {
            /* deviation segment */
            array_shift($insert);   /* discard first location */
            array_pop($insert);     /* discard last location */

            $location_list[$pos1]["segstart"] = 1;
            $location_list[$pos2]["segrejoin"] = 1;

            array_splice($location_list, $pos2, 0, $insert);
        }
    }

    return $location_list;
}

/**
 * @param $state
 * @param $line
 * @return array
 */
function read_location_icons($state, $line)
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            RLI.location_state,
            RLI.location_name,
            RLI.icon1,
            RLI.icon2,
            RLI.icon3,
            RLI.icon4,
            RLI.icon5,
            RLI.icon6
        from
            r_line_location_icons RLI
        where
            RLI.line_state = ?
            and
            RLI.line_name = ?
    ");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($state, $location, $icon1, $icon2, $icon3, $icon4,
        $icon5, $icon6);

    $location_list = [];

    while ($stmt->fetch()) {
        $location_list["$state:$location"] = array($icon1, $icon2, $icon3,
            $icon4, $icon5, $icon6);
    }
    $stmt->close();

    return $location_list;
}

/**
 * @param $state
 * @param $line
 * @return int
 */
function read_line_segment_depth($state, $line)
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.segment_depth
        from
            r_line R
        where
            R.line_state = ?
            and
            R.line_name = ?
    ");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($depth);

    if (!$stmt->fetch()) {
        $depth = 0;
    }

    $stmt->close();

    return $depth;
}

/**
 * @param $location_list
 * @param $location
 * @return int
 */
function index_of(&$location_list, $location)
{
    for ($i = 0; $i < count($location_list); $i++) {
        if ($location_list[$i]["state"] == $location["state"]
                && $location_list[$i]["location"] == $location["location"]) {
            return $i;
        }
    }

    return -1;
}

/**
 * @param $state
 * @param $line
 * @return array
 */
function read_line_segments($state, $line)
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            max(RL.seqno),
            RS.text
        from
            r_line_location RL,
            r_line_segment RS
        where
            RL.line_state = ?
            and
            RL.line_name = ?
            and
            RS.line_state = RL.line_state 
            and
            RS.line_name = RL.line_name
            and
            RS.segment = RL.segment
        group by
            RL.segment
    ");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($maxseqno, $text) ;

    $result = [];
    while ($stmt->fetch()) {
        $result[] = [ "maxseq" => $maxseqno, "text" => $text ];
    }
    $stmt->close();

    return $result;
}

/**
 * @param $state
 * @param $location
 * @return string
 */
function get_location_open_date($state, $location)
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LE.day,
            LE.month,
            LE.year,
            LE.year_error
        from
            r_location_event LE
        where
            LE.location_state = ?
            and
            LE.location_name = ?
            and
            LE.type = 'ON'
        order by
            LE.seqno
        limit 1
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($day, $month, $year, $year_error);

    if ($stmt->fetch()) {
        $date = date_cpts2html($day, $month, $year, $year_error);
    } else {
        $date = "unknown";
    }

    $stmt->close();

    return $date;
}

/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return string
 */
function get_location_close_date($db, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            day,
            month,
            year,
            year_error,
            type
        from
            r_location_event
        where
            location_state = ?
            and
            location_name = ?
        order by
            seqno desc
        limit 1
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($day, $month, $year, $year_error, $type);

    if ($stmt->fetch() and $type == "CN") {
        $date = date_cpts2html($day, $month, $year, $year_error);
    } else {
        $date = "";
    }

    $stmt->close();

    return $date;
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

    if (!$stmt->fetch()) {
        $nphotos = 0;
    }

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

    if (!$stmt->fetch()) {
        $ndiagrams = 0;
    }

    $stmt->close();

    return $ndiagrams;
}

/**
 * @param mysqli $db
 * @param $state
 * @param $location
 * @return int
 */
function get_location_nurls($db, $state, $location)
{
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            count(*)
        from
            r_location_url LU
        where
            LU.location_state = ?
            and
            LU.location_name = ?
    ");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($nurls);

    if (!$stmt->fetch()) {
        $nurls = 0;
    }

    $stmt->close();

    return $nurls;
}

/*
 * Return the existing footnote for the given text, or a new footnote
 * number if notalready present.
 */
/**
 * @param $footnotes
 * @param $text
 * @return false|int|string
 */
function add_footnote(&$footnotes, $text)
{
    $fn = array_search($text, $footnotes);

    return $fn ? $fn : count($footnotes) + 1;
}

/**
 * @param $array
 * @param $v
 */
function push(&$array, $v)
{
    $array[count($array)] = $v;
}

/**
 * @return array|mixed
 * @throws SecurityError
 */
function run_lines_details()
{
    /** @var mysqli $db */
    global $db;

    list($state, $line) = param_get_string2("name");

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        dbline_get_details($state, $line);

    $tp = [
        'title' => $fullname,
        'links' => [],
        'desc_rows' => [],
        'hist_rows' => [],
        'hist_footnotes' => [],
    ];

    /*
     * Calculate some summary details 
     */
    list($length, $length_approx) = dbline_get_length($state, $line);

    list($active_stations, $total_stations) =
        dbline_get_station_count($state, $line);

    if ($length_approx) {
        if ($length == 0) {
            $length_str = "unknown";
        } else {
            $length_str = "> " . floor($length) . " km";
        }
    } else {
        $length_str = floor($length) . " km";
    }

    if ($state == 'NSW') {
        $ovmap = "/media/maps/ovmaps/$line.png";
    } else {
        $ovmap = "/media/maps/ovmaps/$line" . '_' . strtolower($state) . '.png';
    }
    $tp['ovmap_url'] = $ovmap;

    $tp['length'] = $length_str;
    $tp['nstations_open'] = $active_stations;
    $tp['nstations_total'] = $total_stations;

    /*
     * Collect the Description tab details
     */

    $tp['ne_description'] = html_markup_textblock(htmlentities($desc));

    $tp = add_links($tp, $state, $line);
    $tp = add_locations($db, $tp, $state, $line);

    /*
     * Collect the History tab details
     */

    /*
     * Retrieve the text associated with any segments
     */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            RS.segment,
            RS.text
        from
            r_line_segment RS
        where
            RS.line_state = ?
            and
            RS.line_name = ?
        order by
            RS.segment
    ");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($segment, $text);

    $segment_text = [];
    while ($stmt->fetch()) {
        $segment_text[$segment] = $text;
    }
    $stmt->close();

    /*
     * Now retrieve the section/events for this line
     */
    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            SEV.segment,
            SEV.start_name,
            SEV.end_name,
            SEV.type,
            SEV.day,
            SEV.month,
            SEV.year,
            SEV.year_error,
            SEV.text
        from
            r_section_event SEV,
            r_line_location RL0
        where
            SEV.line_state = ?
            and
            SEV.line_name = ?
            and
            RL0.line_state = SEV.line_state
            and
            RL0.line_name = SEV.line_name
            and
            RL0.segment = SEV.segment
            and
            RL0.location_state = SEV.start_state
            and
            RL0.location_name = SEV.start_name
        order by
            SEV.segment,
            RL0.seqno,
            SEV.seqno
    ");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($segment, $start_name, $end_name, $type, $day, $month,
        $year, $year_error, $text);

    $sections = [];
    $footnotes = [];

    while ($stmt->fetch()) {
        $section = "$segment: $start_name - $end_name";
        $date_html = date_cpts2html($day, $month, $year, $year_error);

        $n = 0;
        if (is_array($sections) and array_key_exists($section, $sections)) {
            $n = count($sections[$section]);
        }

        if ($type == "ON" or $type == "OT") {
            $sections[$section][$n+1] = [];
            $sections[$section][$n+1]["open"] = $date_html;
            $sections[$section][$n+1]["close"] = "";
            $sections[$section][$n+1]["usage"] = 
                $type == "ON" ? "general" : "tourism";

            if ($text) {
                $fn = add_footnote($footnotes, $text);
                $footnotes[$fn] = $text;
                push($sections[$section][$n+1]["openfn"], $fn);
            }
        } elseif ($type == "CN" or $type == "CT") {
            $sections[$section][$n]["close"] = $date_html;

            if ($text) {
                $fn = add_footnote($footnotes, $text);
                $footnotes[$fn] = $text;
                push($sections[$section][$n]["closefn"], $fn);
            }
        } elseif ($type == "LT") {
            if (!$text) {
                $text = "Last train ran $date_html";
            }

            $fn = add_footnote($footnotes, $text);
            $footnotes[$fn] = $text;
            push($sections[$section][$n]["closefn"], $fn);
        } elseif ($type == "LI") {
            if (!$text) {
                if ($date_html != "unknown") {
                    $text = "Track lifted $date_html";
                } else {
                    $text = "Track has been lifted";
                }
            }

            $fn = add_footnote($footnotes, $text);
            $footnotes[$fn] = $text;
            push($sections[$section][$n]["closefn"], $fn);
        }
    }
    $stmt->close();

    /*
     * Add the section details
     */
    $prev_segment = "";
    $prev_segment_text = "";
    $prev_section = "";
    foreach ($sections as $section => $ranges) {
        $segment = preg_replace("/:.*/", "", $section);
        $section = preg_replace("/.*: /", "", $section);

        if ($segment != $prev_segment and $segment >= 1
                and $prev_segment_text != $segment_text[$segment]) {
            /* add a new segment row */
            $tp['hist_rows'][] = [
                'u_segment' => [
                                'text' => $segment_text[$segment],
                            ],
            ];
        }

        foreach ($ranges as $n => $period) {
            /* get any cross-refs for this section/period */
            $openfns = "";
            if (array_key_exists("openfn", $period)) {
                $openfns = join("", $period["openfn"]);
            }

            $closefns = "";
            if (array_key_exists("closefn", $period)) {
                $closefns = join("", $period["closefn"]);
            }

            /* add a new section row */
            $tp['hist_rows'][] = [
                'u_section' => [
                                'name' => ($prev_section != $section ? $section : ''),
                                'ne_opened' => $period['open'],
                                'opened_fn_ids' => $openfns,
                                'ne_closed' => $period['close'],
                                'closed_fn_ids' => $closefns,
                                'usage' => $period['usage'],
                            ],
            ];
        }

        $prev_segment = $segment;
        $prev_section = $section;
        $prev_segment_text = $segment_text[$segment];
    }


    /*
     * Add the history footnotes
     */
    foreach ($footnotes as $seq => $text) {
        $tp['hist_footnotes'][] = [
            'id' => $seq,
            'ne_text' => $text,
        ];
    }

    return $tp;
}

normal_page_wrapper('run_lines_details', 'line-details.latte');
