<?php

require_once "../init.inc";
require_once "../util.inc";

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
$t->setCurrentBlock("CONTROLS");
$t->setVariable("TOP", top());
$t->setVariable("MENU", menu());
$t->parseCurrentBlock();

if ($mode == "history")
    run_history_mode($state, $line);
else
if ($mode == "maps")
    run_maps_mode($state, $line);
else
    run_default_mode($state, $line);

function run_default_mode($state, $line)
{
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        get_line_details($state, $line);

    add_urls($state, $line);
    add_locations($state, $line, $maxsegment);

    $t->setCurrentBlock("MAIN");
    $t->setVariable("LINE", $fullname);
    $t->setVariable("MAIN-URL", "show.php?name=$state:$line");
    $t->setVariable("HISTORY-URL", htmlentities("show.php?name=$state:$line&mode=history"));
    $t->setVariable("LINEMAP-URL", "linemap.php?name=$state:$line:1");
    $t->setVariable("DESCRIPTION", html_markup_textblock(htmlentities($desc)));
    if (auth_priv_editor())
    {
        $t->setVariable("EDIT-URL", "edit.php?"
            . urlenc("name=$state:$line"));
    }

    $t->parseCurrentBlock();

    $t->show();
}

function add_urls($state, $line)
{
    global $db;
    global $t;

    $stmt = mysql_query("
        select
            LU.text,
            LU.url
            from
                r_line_url LU
            where
                LU.line_state = '$state'
                and
                LU.line_name = '$line'
            order by
                LU.seqno
        ", $db)
            or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list ($text, $url) = $row;

        $t->setCurrentBlock("LINK-URL");
        $t->setVariable("URL", $url);
        $t->setVariable("TEXT", $text);
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
}

function add_locations($state, $line, $maxsegment)
{
    global $db;
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

        $stmt = mysql_query("
            select
                L.location_state,
                L.location_name,
                L.type,
                L.status,
                L.distance
            from
                r_location L
            where
                L.location_state = '$location_state'
                and
                L.location_name = '$location_name'
        ", $db)
            or die("prepare failed: " . mysql_error() . "\n");

        if ($row = mysql_fetch_array($stmt))
        {
            list($location_state, $location, $type, $status, $distance) = $row;

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
                        "/lines/icons/" . $icons[$i] . ".gif");
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
        mysql_free_result($stmt);

    }
}

function read_line_locations($line_state, $line_name)
{
    global $db;

    /*
     * Construct an array of segments, each element being the list of locations
     * in that segment.
     */

    $stmt = mysql_query("
        select
            RL.segment,
            RL.location_state,
            RL.location_name
        from
            r_line_location RL
        where
            RL.line_state = '$line_state'
            and
            RL.line_name = '$line_name'
        order by
            RL.segment,
            RL.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $segment_list = array();

    while ($row = mysql_fetch_array($stmt))
    {
        list($segment, $state, $location) = $row;

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
    mysql_free_result($stmt);

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
    global $db;

    $stmt = mysql_query("
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
            RLI.line_state = '$state'
            and
            RLI.line_name = '$line'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $location_list = array();

    while ($row = mysql_fetch_array($stmt))
    {
        list($state, $location, $icon1, $icon2, $icon3, $icon4, $icon5, $icon6) = $row;

        $location_list["$state:$location"] = array($icon1, $icon2, $icon3,
            $icon4, $icon5, $icon6);
    }
    mysql_free_result($stmt);

    return $location_list;
}

function read_line_segment_depth($state, $line)
{
    global $db;

    $stmt = mysql_query("
        select
            R.segment_depth
        from
            r_line R
        where
            R.line_state = '$state'
            and
            R.line_name = '$line'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $depth = 0;
    if ($row = mysql_fetch_array($stmt))
    {
        list($depth) = $row;
    }
    mysql_free_result($stmt);

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
    global $db;

    $stmt = mysql_query("
        select
            max(RL.seqno),
            RS.text
        from
            r_line_location RL,
            r_line_segment RS
        where
            RL.line_state = '$state'
            and
            RL.line_name = '$line'
            and
            RS.line_state = RL.line_state 
            and
            RS.line_name = RL.line_name
            and
            RS.segment = RL.segment
        group by
            RL.segment
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $i = 0;
    while ($row = mysql_fetch_array($stmt))
    {
        list($maxseqno, $text) = $row;

        $result[$i++] = array("maxseq" => $maxseqno, "text" => $text);
    }
    mysql_free_result($stmt);

    return $result;
}

function get_location_open_date($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            LE.day,
            LE.month,
            LE.year,
            LE.year_error
        from
            r_location_event LE
        where
            LE.location_state = '$state'
            and
            LE.location_name = '$location'
            and
            LE.type = 'ON'
        order by
            LE.seqno
        limit 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
    {
        list($day, $month, $year, $year_error) = $row;
        $date = date_cpts2text($day, $month, $year, $year_error);
    }
    else
        $date = "unknown";

    mysql_free_result($stmt);

    return $date;
}

function get_location_close_date($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            day,
            month,
            year,
            year_error,
            type
        from
            r_location_event
        where
            location_state = '$state'
            and
            location_name = '$location'
        order by
            seqno desc
        limit 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt) and $row[4] == "CN")
    {
        list($day, $month, $year, $year_error, $type) = $row;
        $date = date_cpts2text($day, $month, $year, $year_error);
    }
    else
        $date = "";

    mysql_free_result($stmt);

    return $date;
}

function run_history_mode($state, $line)
{
    global $db;
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        get_line_details($state, $line);

    /*
     * Retrieve the text associated with any segments
     */
    $stmt = mysql_query("
        select
            RS.segment,
            RS.text
        from
            r_line_segment RS
        where
            RS.line_state = '$state'
            and
            RS.line_name = '$line'
        order by
            RS.segment
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $segment_text = array();
    while ($row = mysql_fetch_array($stmt))
    {
        list ($segment, $text) = $row;

        $segment_text[$segment] = $text;
    }
    mysql_free_result($stmt);

    /*
     * Now retrieve the section/events for this line
     */
    $stmt = mysql_query("
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
            SEV.line_state = '$state'
            and
            SEV.line_name = '$line'
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
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $sections = array();
    $footnotes = array();

    while ($row = mysql_fetch_array($stmt))
    {
        list($segment, $start_name, $end_name, $type, $day, $month,
            $year, $year_error, $text) = $row;

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
    mysql_free_result($stmt);

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

    $t->setCurrentBlock("MAIN");
    $t->setVariable("LINE", $fullname);
    $t->setVariable("MAIN-URL", "show.php?name=$state:$line");
    $t->setVariable("HISTORY-URL", htmlentities("show.php?name=$state:$line&mode=history"));
    $t->setVariable("LINEMAP-URL", "linemap.php?name=$state:$line:1");
    $t->parseCurrentBlock();
    $t->show();
}

function run_maps_mode($state, $line)
{
    global $t;

    list($fullname, $region, $traffic, $maxsegment, $desc, $version) = 
        get_line_details($state, $line);

    $t->setCurrentBlock("MAIN");
    $t->setVariable("LINE", $fullname);
    $t->setVariable("MAIN-URL", "show.php?name=$state:$line");
    $t->setVariable("HISTORY-URL", htmlentities("show.php?name=$state:$line&mode=history"));
    $t->setVariable("LINEMAP-URL", "linemap.php?name=$state:$line:1");
    $t->parseCurrentBlock();
    $t->show();
}

function get_location_nphotos($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            count(*)
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$location'
            and
            LP.status = 'Y'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $nphotos = 0;
    if ($row = mysql_fetch_array($stmt))
        list($nphotos) = $row;

    mysql_free_result($stmt);

    return $nphotos;
}

function    get_location_ndiagrams($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            count(*)
        from
            r_location_diagram LD
        where
            LD.location_state = '$state'
            and
            LD.location_name = '$location'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $ndiagrams = 0;
    if ($row = mysql_fetch_array($stmt))
        list($ndiagrams) = $row;

    mysql_free_result($stmt);

    return $ndiagrams;
}

function get_location_nurls($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            count(*)
        from
            r_location_url LU
        where
            LU.location_state = '$state'
            and
            LU.location_name = '$location'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $urls = 0;
    if ($row = mysql_fetch_array($stmt))
        list($urls) = $row;

    mysql_free_result($stmt);

    return $urls;
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
