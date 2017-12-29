<?php

require_once "site.inc";
require_once "icon.inc";    /* for get_location_icon() */
require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$line = quote_external(get_post("line"));           /* obsolete */
$mode = quote_external(get_post("mode", ""));       /* optional */

if ($name)
    list($state, $line) = explode(":", $name);

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("show.tpl", true, true);

/*
 * Run the appropriate page
 */
run_multitab_page($state, $line);

exit();

if ($mode == "history")
    run_history_mode($state, $line, $common_data);
else
if ($mode == "maps")
    run_maps_mode($state, $line, $common_data);
else
    run_default_mode($state, $line, $common_data);

function run_multitab_page($state, $line)
{
    global $dbi, $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        dbline_get_details($state, $line);

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $fullname);

    /*
     * Calculate some summary details and add to each tab
     */
    list($length, $length_approx) = dbline_get_length($state, $line);

    list($active_stations, $total_stations)
        = dbline_get_station_count($state, $line);

    if ($length_approx)
    {
        if ($length == 0)
            $length_str = "unknown";
        else
            $length_str = "> $length km";
    }
    else
        $length_str = "$length km";

    foreach (array(1, 2) as $n)
    {
        $t->setCurrentBlock("SUMMARY$n");
        $t->setVariable("LINE", $line);

        if ($state == 'NSW')
            $t->setVariable("OVMAP", $line);
        else
            $t->setVariable("OVMAP", $line . '_' . strtolower($state));

        $t->setVariable("LINE-LENGTH", $length_str);
        $t->setVariable("LINE-STN-OPEN", $active_stations);
        $t->setVariable("LINE-STN-COUNT", $total_stations);
        $t->parseCurrentBlock();
    }

    /*
     * Display the Description tab details
     */
    $t->setCurrentBlock("DESCRIPTION-MODE");
    $t->setVariable("DESCRIPTION", html_markup_textblock(htmlentities($desc)));

    add_urls($state, $line);
    add_locations($state, $line, $maxsegment);

    if (auth_priv_editor())
    {
        $t->setCurrentBlock("CONTENT");
        $t->setVariable("EDIT-URL", "edit.php?"
            . urlenc("name=$state:$line"));
    }

    /*
     * Display the History tab details
     */

    /*
     * Retrieve the text associated with any segments
     */
    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($segment, $text);

    $segment_text = array();
    while ($stmt->fetch())
    {
        $segment_text[$segment] = $text;
    }
    $stmt->close();

    /*
     * Now retrieve the section/events for this line
     */
    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($segment, $start_name, $end_name, $type, $day, $month,
        $year, $year_error, $text);

    $sections = array();
    $footnotes = array();

    while ($stmt->fetch())
    {
        $section = "$segment: $start_name - $end_name";
        $date = date_cpts2text($day, $month, $year, $year_error);

        $n = 0;
        if (is_array($sections) and array_key_exists($section, $sections))
            $n = count($sections[$section]);

        if ($type == "ON" or $type == "OT")
        {
            $sections[$section][$n+1] = array();
            $sections[$section][$n+1]["open"] = $date;
            $sections[$section][$n+1]["close"] = "";
            $sections[$section][$n+1]["usage"] = 
                $type == "ON" ? "general" : "tourism";

            if ($text)
            {
                $fn = add_footnote($footnotes, $text);
                $footnotes[$fn] = $text;
                push($sections[$section][$n+1]["openfn"], $fn);
            }
        }
        elseif ($type == "CN" or $type == "CT")
        {
            $sections[$section][$n]["close"] = $date;

            if ($text)
            {
                $fn = add_footnote($footnotes, $text);
                $footnotes[$fn] = $text;
                push($sections[$section][$n]["closefn"], $fn);
            }
        }
        elseif ($type == "LT")
        {
            if (!$text)
                $text = "Last train ran $date";

            $fn = add_footnote($footnotes, $text);
            $footnotes[$fn] = $text;
            push($sections[$section][$n]["closefn"], $fn);
        }
        elseif ($type == "LI")
        {
            if (!$text)
            {
                if ($date != "unknown")
                    $text = "Track lifted $date";
                else
                    $text = "Track has been lifted";
            }

            $fn = add_footnote($footnotes, $text);
            $footnotes[$fn] = $text;
            push($sections[$section][$n]["closefn"], $fn);
        }
    }
    $stmt->close();

    /*
     * Print the section details
     */
    $prev_segment = "";
    $prev_segment_text = "";
    $prev_section = "";
    foreach ($sections as $section => $ranges)
    {
        $segment = ereg_replace(":.*", "", $section);
        $section = ereg_replace(".*: ", "", $section);

        if ($segment != $prev_segment and $segment >= 1
            and $prev_segment_text != $segment_text[$segment])
        {
            $t->setCurrentBlock("SEGMENT2");
            $t->setVariable("SEGMENT-TEXT", $segment_text[$segment]);
            $t->parseCurrentBlock();
            $t->parse("SEGMENT2-OR-SECTION");
        }

        foreach ($ranges as $n => $period)
        {
            /*
             * Get any cross-refs for this section/period
             */
            $openfns = "";
            if (array_key_exists("openfn", $period))
                $openfns = join("", $period["openfn"]);

            $closefns = "";
            if (array_key_exists("closefn", $period))
                $closefns = join("", $period["closefn"]);

            $t->setCurrentBlock("SECTION");
            if ($prev_section != $section)
                $t->setVariable("SECTION", $section);
            $t->setVariable("OPENED", $period["open"]);
            $t->setVariable("OPENFN", $openfns);
            $t->setVariable("CLOSED", $period["close"]);
            $t->setVariable("CLOSEFN", $closefns);
            $t->setVariable("USAGE", $period["usage"]);
            $t->parseCurrentBlock();
            $t->parse("SEGMENT2-OR-SECTION");

        }

        $prev_segment = $segment;
        $prev_section = $section;
        $prev_segment_text = $segment_text[$segment];
    }


    /*
     * Now display the footnotes
     */
    foreach ($footnotes as $seq => $text)
    {
        $t->setCurrentBlock("FOOTNOTES");
        $t->setVariable("XREF-SEQ", $seq);
        $t->setVariable("XREF-TEXT", $text);
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock("HISTORY-MODE");
    $t->parseCurrentBlock();

    $t->setCurrentBlock("CONTENT");
    $t->parseCurrentBlock();

    display_page($fullname, $t->get("CONTENT"),
        array(
            'HEAD-EXTRA'    => implode(file('show.hdr'), ""),
        )
    );
}

function run_default_mode($state, $line, $data)
{
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        dbline_get_details($state, $line);

    add_urls($state, $line);
    add_locations($state, $line, $maxsegment);

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $fullname);
    $t->setVariable("LINE", $line);
    $t->setVariable("MAIN-URL", "show.php?name=$state:$line");

    $t->touchBlock("LINK-DESC-STATIC");
    $t->setCurrentBlock("LINK-HIST-URL");
    $t->setVariable("HISTORY-URL", htmlentities("show.php?name=$state:$line&mode=history"));
    $t->parseCurrentBlock();

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("LINEMAP-URL", "linemap.php?name=$state:$line:1");
    $t->setVariable("DESCRIPTION", html_markup_textblock(htmlentities($desc)));
    $t->setVariable("LINE-LENGTH", $data['length_str']);
    $t->setVariable("LINE-STN-OPEN", $data['active_stations']);
    $t->setVariable("LINE-STN-COUNT", $data['total_stations']);

    if (auth_priv_editor())
    {
        $t->setVariable("EDIT-URL", "edit.php?"
            . urlenc("name=$state:$line"));
    }
    $t->parseCurrentBlock();

    display_page($fullname, $t->get("CONTENT"),
        array(
            'HEAD-EXTRA'    => implode(file('show.hdr'), ""),
        )
    );
}

function add_urls($state, $line)
{
    global $dbi;
    global $t;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($text, $url);

    while ($stmt->fetch())
    {
        $t->setCurrentBlock("EXT-LINK-URL");
        $t->setVariable("EXT-URL", $url);
        $t->setVariable("EXT-TEXT", $text);
        $t->parseCurrentBlock();
    }
    $stmt->close();
}

function add_locations($state, $line, $maxsegment)
{
    global $dbi;
    global $t;

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

    /*
     * Create the appropriate number of icon column headers
     */
    for ($i = 0; $i < $max_depth + 1; $i++)
    {
        $t->setCurrentBlock("ICON-HEADING");
        $t->touchBlock("ICON-HEADING");
        $t->parseCurrentBlock();
    }

    /*
     * Display the location information
     */
    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_result($location_state, $location, $type, $status,
        $distance);

    for ($l = 0; $l <= $max_location; $l++)
    {
        $data = $locations[$l];
        if (!$data["state"])
            die("No icon data for $l!");
        $location_state = $data["state"];
        $location_name = $data["location"];
        $main_segment = $data["segment"];
        $depth = $data["depth"];
        $line_first = ($l == 0);
        $line_last = ($l == $max_location);

        /* initialise the icon array */
        $icons = $location_icons["$location_state:$location_name"];

        $stmt->bind_param("ss", $location_state, $location_name);
        $stmt->execute();

        /*
         * Buffer the result to avoid conflict with subsequent fetches for
         * other statement.
         */
        $stmt->store_result();

        if ($stmt->fetch())
        {
            $url = "/locations/show.php?"
                . urlenc("name=$location_state:$location&line=$state:$line:$main_segment");

            if ($distance == NULL)
                $distance = "";
            else
                $distance = sprintf("%.3f", $distance);

            $nphotos = get_location_nphotos($location_state, $location);
            $nphotos == 0 && $nphotos = "";

            $nurls = get_location_nurls($location_state, $location);
            $nurls == 0 && $nurls = "";

            $open_date = get_location_open_date($location_state,
                $location);
            $close_date = get_location_close_date($location_state,
                $location);

            if (!$close_date)
            {
                if ($status == "closed")
                    $close_date = "unknown";
                else
                if ($status == "in use")
                    $close_date = "-";
            }

            if ($status == "not opened")
            {
                $open_date = "-";
                $close_date = "-";
            }

            if ($type == "border")
            {
                $status = "-";
                $open_date = "-";
                $close_date = "-";
            }


            for ($i = 0; $i < $max_depth + 1; $i++)
            {
                $t->setCurrentBlock("ICON-DATA");
                if ($icons[$i])
                {
                    $t->setVariable("ICON",
                        "/c/icons/" . $icons[$i] . ".png");
                }
                else
                    $t->touchBlock("ICON-DATA");
                $t->parseCurrentBlock();
            }

            $t->setCurrentBlock("LOCATION");
            $t->setVariable("URL", $url);
            $t->setVariable("NAME", $location);
            $t->setVariable("FACILITY", locn_type2text($type));
            $t->setVariable("STATUS", locn_status2text($status));
            $t->setVariable("OPENED", $open_date);
            $t->setVariable("CLOSED", $close_date);
            $t->setVariable("LOCATION", $distance);
            $t->setVariable("PHOTOS", $nphotos);
            $t->setVariable("LINKS", $nurls);
            $t->parseCurrentBlock();
            $t->setCurrentBlock("TABLE-ENTRY");
            $t->parseCurrentBlock();
        }
    }
    $stmt->close();
}

function read_line_locations($line_state, $line_name)
{
    global $dbi;

    /*
     * Construct an array of segments, each element being the list of locations
     * in that segment.
     */

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $line_state, $line_name);
    $stmt->execute();
    $stmt->bind_result($segment, $state, $location);

    $segment_list = array();

    while ($stmt->fetch())
    {
        if (!array_key_exists($segment, $segment_list))
            $segment_list[$segment] = array();

        $data = array();
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
    for ($s = 1; $s <= $max_segment; $s++)
    {
        $segment_ref = &$segment_list[$s];

        $pos1 = index_of($location_list, $segment_ref[0]);
        $pos2 = index_of($location_list, $segment_ref[count($segment_ref)-1]);

        if ($pos1 != -1)
            $new_depth = $location_list[$pos1]["depth"] + 1;
        else
            $new_depth = $location_list[$pos2]["depth"] + 1;

        $insert = $segment_list[$s];

        /* set new depth */
        foreach ($insert as $i => $data)
            $insert[$i]["depth"] = $new_depth;

        if ($pos1 == -1)
        {
            # entrant segment
            array_pop($insert);     /* discard last location */

            $location_list[$pos2]["segrejoin"] = 1;

            array_splice($location_list, $pos2, 0, $insert);
        }
        elseif ($pos2 == -1)
        {
            /* dead end segment */
            array_shift($insert);   /* discard first location */

            $location_list[$pos1]["segstart"] = 1;
            $insert[count($insert) - 1]["segend"] = 1;

            array_splice($location_list, $pos1 + 1, 0, $insert);
        }
        else
        {
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

function read_location_icons($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($state, $location, $icon1, $icon2, $icon3, $icon4,
        $icon5, $icon6);

    $location_list = array();

    while ($stmt->fetch())
    {
        $location_list["$state:$location"] = array($icon1, $icon2, $icon3,
            $icon4, $icon5, $icon6);
    }
    $stmt->close();

    return $location_list;
}

function read_line_segment_depth($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            R.segment_depth
        from
            r_line R
        where
            R.line_state = ?
            and
            R.line_name = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($depth);

    if (!$stmt->fetch())
        $depth = 0;

    $stmt->close();

    return $depth;
}

function index_of(&$location_list, $location)
{
    for ($i = 0; $i < count($location_list); $i++)
    {
        if ($location_list[$i]["state"] == $location["state"]
            && $location_list[$i]["location"] == $location["location"])
                return $i;
    }

    return -1;
}

function read_line_segments($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($maxseqno, $text) ;

    $i = 0;
    while ($stmt->fetch())
    {
        $result[$i++] = array("maxseq" => $maxseqno, "text" => $text);
    }
    $stmt->close();

    return $result;
}

function get_location_open_date($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($day, $month, $year, $year_error);

    if ($stmt->fetch())
        $date = date_cpts2text($day, $month, $year, $year_error);
    else
        $date = "unknown";

    $stmt->close();

    return $date;
}

function get_location_close_date($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($day, $month, $year, $year_error, $type);

    if ($stmt->fetch() and $type == "CN")
        $date = date_cpts2text($day, $month, $year, $year_error);
    else
        $date = "";

    $stmt->close();

    return $date;
}

function run_history_mode($state, $line, $data)
{
    global $dbi;
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        dbline_get_details($state, $line);

    /*
     * Retrieve the text associated with any segments
     */
    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($segment, $text);

    $segment_text = array();
    while ($stmt->fetch())
    {
        $segment_text[$segment] = $text;
    }
    $stmt->close();

    /*
     * Now retrieve the section/events for this line
     */
    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($segment, $start_name, $end_name, $type, $day, $month,
        $year, $year_error, $text);

    $sections = array();
    $footnotes = array();

    while ($stmt->fetch())
    {
        $section = "$segment: $start_name - $end_name";
        $date = date_cpts2text($day, $month, $year, $year_error);

        $n = 0;
        if (is_array($sections) and array_key_exists($section, $sections))
            $n = count($sections[$section]);

        if ($type == "ON" or $type == "OT")
        {
            $sections[$section][$n+1] = array();
            $sections[$section][$n+1]["open"] = $date;
            $sections[$section][$n+1]["close"] = "";
            $sections[$section][$n+1]["usage"] = 
                $type == "ON" ? "general" : "tourism";

            if ($text)
            {
                $fn = add_footnote($footnotes, $text);
                $footnotes[$fn] = $text;
                push($sections[$section][$n+1]["openfn"], $fn);
            }
        }
        elseif ($type == "CN" or $type == "CT")
        {
            $sections[$section][$n]["close"] = $date;

            if ($text)
            {
                $fn = add_footnote($footnotes, $text);
                $footnotes[$fn] = $text;
                push($sections[$section][$n]["closefn"], $fn);
            }
        }
        elseif ($type == "LT")
        {
            if (!$text)
                $text = "Last train ran $date";

            $fn = add_footnote($footnotes, $text);
            $footnotes[$fn] = $text;
            push($sections[$section][$n]["closefn"], $fn);
        }
        elseif ($type == "LI")
        {
            if (!$text)
            {
                if ($date != "unknown")
                    $text = "Track lifted $date";
                else
                    $text = "Track has been lifted";
            }

            $fn = add_footnote($footnotes, $text);
            $footnotes[$fn] = $text;
            push($sections[$section][$n]["closefn"], $fn);
        }
    }
    $stmt->close();

    /*
     * Print the section details
     */
    $prev_segment = "";
    $prev_segment_text = "";
    $prev_section = "";
    foreach ($sections as $section => $ranges)
    {
        $segment = ereg_replace(":.*", "", $section);
        $section = ereg_replace(".*: ", "", $section);

        if ($segment != $prev_segment and $segment >= 1
            and $prev_segment_text != $segment_text[$segment])
        {
            $t->setCurrentBlock("SEGMENT2");
            $t->setVariable("SEGMENT-TEXT", $segment_text[$segment]);
            $t->parseCurrentBlock();
            $t->parse("SEGMENT2-OR-SECTION");
        }

        foreach ($ranges as $n => $period)
        {
            /*
             * Get any cross-refs for this section/period
             */
            $openfns = "";
            if (array_key_exists("openfn", $period))
                $openfns = join("", $period["openfn"]);

            $closefns = "";
            if (array_key_exists("closefn", $period))
                $closefns = join("", $period["closefn"]);

            $t->setCurrentBlock("SECTION");
            if ($prev_section != $section)
                $t->setVariable("SECTION", $section);
            $t->setVariable("OPENED", $period["open"]);
            $t->setVariable("OPENFN", $openfns);
            $t->setVariable("CLOSED", $period["close"]);
            $t->setVariable("CLOSEFN", $closefns);
            $t->setVariable("USAGE", $period["usage"]);
            $t->parseCurrentBlock();
            $t->parse("SEGMENT2-OR-SECTION");

        }

        $prev_segment = $segment;
        $prev_section = $section;
        $prev_segment_text = $segment_text[$segment];
    }


    /*
     * Now display the footnotes
     */
    foreach ($footnotes as $seq => $text)
    {
        $t->setCurrentBlock("FOOTNOTES");
        $t->setVariable("XREF-SEQ", $seq);
        $t->setVariable("XREF-TEXT", $text);
        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $fullname);
    $t->setVariable("LINE", $line);
    $t->setVariable("MAIN-URL", "show.php?name=$state:$line");

    $t->setCurrentBlock("LINK-DESC-URL");
    $t->setVariable("DESCRIPTION-URL", htmlentities("show.php?name=$state:$line"));
    $t->parseCurrentBlock();
    $t->touchBlock("LINK-HIST-STATIC");

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("LINEMAP-URL", "linemap.php?name=$state:$line:1");
    $t->setVariable("LINE-LENGTH", $data['length_str']);
    $t->setVariable("LINE-STN-OPEN", $data['active_stations']);
    $t->setVariable("LINE-STN-COUNT", $data['total_stations']);
    $t->parseCurrentBlock();

    display_page($fullname, $t->get("CONTENT"));
}

function run_maps_mode($state, $line, $data)
{
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        dbline_get_details($state, $line);

    $t->setCurrentBlock("CONTENT");
    $t->setVariable("TITLE", $fullname);
    $t->setVariable("MAIN-URL", "show.php?name=$state:$line");
    $t->setVariable("HISTORY-URL", htmlentities("show.php?name=$state:$line&mode=history"));
    $t->setVariable("LINEMAP-URL", "linemap.php?name=$state:$line:1");
    $t->parseCurrentBlock();

    display_page($fullname, $t->get("CONTENT"));
}

function get_location_nphotos($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
            LP.status = 'Y'
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($nphotos);

    if (!$stmt->fetch())
        $nphotos = 0;

    $stmt->close();

    return $nphotos;
}

function    get_location_ndiagrams($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            count(*)
        from
            r_location_diagram LD
        where
            LD.location_state = ?
            and
            LD.location_name = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($ndiagrams);

    if (!$stmt->fetch())
        $ndiagrams = 0;

    $stmt->close();

    return $ndiagrams;
}

function get_location_nurls($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            count(*)
        from
            r_location_url LU
        where
            LU.location_state = ?
            and
            LU.location_name = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($nurls);

    if (!$stmt->fetch())
        $nurls = 0;

    $stmt->close();

    return $nurls;
}

/*
 * Return the existing footnote for the given text, or a new footnote
 * number if notalready present.
 */
function add_footnote(&$footnotes, $text)
{
    $fn = array_search($text, $footnotes);

    return $fn ? $fn : count($footnotes) + 1;
}

function push(&$array, $v)
{
    $array[count($array)] = $v;
}

?>
