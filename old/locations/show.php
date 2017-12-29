<?php
require_once "../init.inc";
require_once "../util.inc";
require_once "dbutil.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$mode = quote_external(get_post("mode"));           /* optional */

if ($name)
    list($state,$location) = explode(":", $name);

$location = make_canonical_name($location);

$line_state = null;
$line_name = null;
$line_segment = null;
if ($line)
    list($line_state, $line_name, $line_segment) = explode(":", $line);

$t = new HTML_Template_ITX(".");

run_default_mode($state, $location, $line_state, $line_name, $line_segment);

exit;

function run_default_mode($state, $location, $line_state, $line, $segment)
{
    global $db, $t;

    $t->loadTemplateFile("show.tpl");
    $t->setCurrentBlock("CONTROLS");
    $t->setVariable("TOP", top());
    $t->setVariable("MENU", menu());
    $t->parseCurrentBlock();

    $l = get_location_details($state, $location);

    if ($l["type"])
    {
        $version = $l["version"];

        if ($l["geox"] and $l["geoy"])
        {
            $latlong = sprintf("(%.4f&deg;, %.4f&deg;)",
                            $l["geox"], $l["geoy"]);
        }
        else
            $latlong = "unknown";

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

        add_location_history($state, $location);
        add_location_lines($state, $location);
        add_station_details($state, $location);
        add_goods_details($state, $location);
        add_infra_details($state, $location);
        add_link_details($state, $location);
        add_photo_details($state, $location);

        if ($line_state != "" and $line != "")
            add_prev_next_links($state, $location, $line_state, $line, $segment);

        if (array_key_exists("altdist", $l))
        {
            $t->setCurrentBlock("ALT-DIST");
            $t->setVariable("DISTANCE2", sprintf("%.3f", $l["altdist"]));
            $t->setVariable("ORIGIN", $origin);
            $t->setVariable("VIA-LOCATION", $l["altvia"]);
            $t->parseCurrentBlock();
        }

        add_map_sheet_link($state, $location, $l);

        $t->setCurrentBlock("CONTENT");
        $t->setVariable("GMAPKEY", gmapkey("locations"));
        $t->setVariable("TITLE", locn_fulltitle($location, $l["type"]));
        $t->setVariable("LOCATION", locn_fulltitle($location, $l["type"]));
        $t->setVariable("FACILITY", locn_type2text($l["type"]));
        $t->setVariable("STATUS", locn_status2text($l["status"]));
        $t->setVariable("LATLONG", $latlong);
        $t->setVariable("DISTANCE", $km_syd);
        $t->setVariable("ORIGIN", $origin);

        $t->setVariable("DESC", $l["desc"]);
        $t->setVariable("CURR", $l["curr"]);

        if (auth_priv_admin())
        {
            $params = urlenc("name=$state:$location");
            if ($line)
                $params = $params . urlenc("&line=$line_state:$line:$segment");

            $t->setVariable("EDIT-TURNTABLES-URL", "edit-turntables.php?" . $params);
            $t->setVariable("EDIT-LINKS-URL", "edit-links.php?" . $params);

            /*
             * Configure editing of facility (type/status)
             */
            foreach (locn_typelist() as $v)
            {
                $t->setCurrentBlock("TYPE-OPTION");
                if ($v == $l["type"])
                    $t->setVariable("SELECTED", "selected=\"selected\"");
                $t->setVariable("VALUE", $v);
                $t->setVariable("LABEL", locn_type2text($v));
                $t->parseCurrentBlock();
            }
            foreach (locn_statuslist() as $v)
            {
                $t->setCurrentBlock("STATUS-OPTION");
                if ($v == $l["status"])
                    $t->setVariable("SELECTED", "selected=\"selected\"");
                $t->setVariable("VALUE", $v);
                $t->setVariable("LABEL", locn_status2text($v));
                $t->parseCurrentBlock();
            }
            $t->touchBlock("EDIT-FACILITY-DATA");
            instantiate_edit_block("FACILITY", $state, $location, $version);

            /*
             * Configure editing of location
             */
            $t->setVariable("GEO_X", $l["geox"]);
            $t->setVariable("GEO_Y", $l["geoy"]);
            $t->touchBlock("EDIT-LOCATION-DATA");
            instantiate_edit_block("LOCATION", $state, $location, $version);

            /*
             * Configure editing of location
             */
            $t->setVariable("E-DISTANCE", $l["distance"]);
            if (array_key_exists("altdist", $l))
            {
                $t->setVariable("E-DISTANCE2", $l["altdist"]);
                $t->setVariable("E-VIA-LOCATION", $l["altvia"]);
            }
            else
            {
                $t->setVariable("E-DISTANCE2", "0.0");
                $t->setVariable("E-VIA-LOCATION", "");
            }
            $t->touchBlock("EDIT-DISTANCE-DATA");
            instantiate_edit_block("DISTANCE", $state, $location, $version);

            /*
             * Configure editing of history
             */
            $history = get_location_history($state, $location);

            $error_lookup = array(
                0   =>  "-",
                1   =>  "circa",
                2   =>  "decade",
                -1  =>  "before",
                -2  =>  "after",
            );

            for ($n = 1; $n <= 10; $n++)
            {
                $t->setCurrentBlock("E-HISTORY-DETAILS");
                $t->setVariable("SEQ", $n);

                $type = "-";
                $day = "";
                $month = "";
                $year = "";
                $error = 0;
                $name = "";
                if ($n <= count($history))
                {
                    $h = $history[$n-1];

                    $type = $h["type"];
                    $day = $h["day"];
                    $month = $h["month"];
                    $year = $h["year"];
                    $error = $h["year_error"];
                    if ($error == "")
                        $error = 0;
                    $name = $h["name"];
                }

                foreach (locn_eventtypelist() as $v)
                {
                    $t->setCurrentBlock("E-HIST-TYPE-OPTION");
                    $t->setVariable("VALUE", $v);
                    if ($v == $type)
                        $t->setVariable("SELECTED", "selected=\"selected\"");
                    $t->parseCurrentBlock();
                }
                $t->setCurrentBlock("E-HISTORY-DETAILS");

                $t->setVariable("DAY", $day);
                $t->setVariable("MONTH", $month);
                $t->setVariable("YEAR", $year);

                foreach (date_errorlist() as $v)
                {
                    $t->setCurrentBlock("E-HIST-ERROR-OPTION");
                    if ($v == $error)
                        $t->setVariable("SELECTED", "selected=\"selected\"");
                    $t->setVariable("VALUE", $v);
                    $t->setVariable("LABEL", $error_lookup[$v]);
                    $t->parseCurrentBlock();
                }
                $t->setCurrentBlock("E-HISTORY-DETAILS");
                $t->setVariable("NAME", $name);

                $t->parseCurrentBlock();
            }
            $t->touchBlock("EDIT-HISTORY-DATA");
            instantiate_edit_block("HISTORY", $state, $location, $version);
        }

        if (auth_priv_editor())
        {
            $t->touchBlock("EDIT-DESC-DATA");
            instantiate_edit_block("DESC", $state, $location, $version);

            $t->touchBlock("EDIT-CURR-DATA");
            instantiate_edit_block("CURR", $state, $location, $version);
        }

        /*
         * Add any photos or diagrams to the page
         */
        add_photo_thumbnails($state, $location, $line_state, $line, $segment);
        add_diagram_images($state, $location);

        /*
         * Add the location map
         */
        if ($l["geox"] and $l["geoy"])
        {
            /* include Google Map API functionality */
            $t->touchBlock("GOOGLE-INCLUDE1");

            /* define the coordinates */
            $t->setCurrentBlock("GOOGLE-INCLUDE2");
            $t->setVariable("LOCATION-WX", $l["geox"]);
            $t->setVariable("LOCATION-WY", $l["geoy"]);
            $t->parseCurrentBlock();

            if (!$l["geoauth"] || $l["geoauth"] == "approx")
            {
                $t->setVariable("MAP-MESSAGE-1", "(Approximate location only)");
            }

            $url = "/maps/browse.php?pos=" . $l["geox"] . "," . $l["geoy"];
            $t->setVariable("MAP-MESSAGE-3",
                "<a href=\"$url\">full screen</a>");
        }
        else
        {
            $t->setVariable("MAP-MESSAGE-2", "Location not known");
        }

        $t->parseCurrentBlock();
    }
    else
    {
        $state or $state = "[null]";
        $location or $location = "[null]";

        $t->setCurrentBlock("ERROR");
        $t->setVariable("TITLE", "Error");
        $t->setVariable("STATE", $state);
        $t->setVariable("LOCATION", $location);
        $t->parseCurrentBlock();
    }

    $t->show();
}

function instantiate_edit_block($name, $state, $location, $version)
{
    global $t;

    $name_lc = strtolower($name);

    $t->addBlock("EDIT-$name-PRE", "EDIT-$name-PRE", "
        <span id=\"edit-$name_lc-prompt\" class=\"edit-link\">
            <a href=\"#\" onclick=\"editStart('$name_lc'); return false;\">[edit]</a>
        </span>
        <span id=\"edit-$name_lc-working\" class=\"edit-link\" style=\"display: none;\">
            <span class=\"alert\">[saving...]</span>
        </span>
        <div id=\"edit-$name_lc\" style=\"display: none;\">
            <form action=\"#\">
");

    $t->touchBlock("EDIT-$name-PRE");

    $t->addBlock("EDIT-$name-POST", "EDIT-$name-POST", "
            </form>
            <a href=\"#\" onclick=\"editCancel('$name_lc'); return false;\">[cancel]</a>
            <a href=\"#\" onclick=\"editCommit('$name_lc', '$state', '$location', $version); return false;\">[save]</a>
        </div>
");
    $t->touchBlock("EDIT-$name-POST");
}

/*
 * Replace the HISTORY placemarker with a table of events
 */
function add_location_history($state, $location)
{
    global $db, $t;

    $history = get_location_history($state, $location);

    $count = 0;
    foreach ($history as $h)
    {
        $count++;

        $name = $h["name"];
        $date = date_cpts2text($h["day"], $h["month"], $h["year"],
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

        $t->setCurrentBlock("HISTORY-DETAILS");
        $t->setVariable("DATE", $date);
        $t->setVariable("EVENT", $text);
        $t->parseCurrentBlock();
    }
}

/*
 * Replace the LINES placemarker with a list of associated lines
 */
function add_location_lines($state, $location)
{
    global $db, $t;

    /* XXX: move to dbutil.php */
    $stmt = mysql_query("
        select distinct
            RL.line_state,
            RL.line_name,
            R.description
        from
            r_line R,
            r_line_location RL
        where
            RL.location_state = '$state'
            and
            RL.location_name = '$location'
            and
            R.line_state = RL.line_state
            and
            R.line_name = RL.line_name
        order by
            RL.mainline,
            RL.line_state,
            RL.line_name
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $count = 0;
    while ($row = mysql_fetch_array($stmt))
    {
        list($line_state, $line, $description) = $row;

        $t->setCurrentBlock("LINE-DETAILS");
        $t->setVariable("URL", "/lines/show.php?"
            . urlenc("name=$line_state:$line"));
        $t->setVariable("TEXT", $description);
        $t->parseCurrentBlock();
    }

    mysql_free_result($stmt);
}

function add_station_details($state, $location)
{
    global $t;

    /*
    $t->setCurrentBlock("OPT-STATION-DETAILS");
    $t->setVariable("STATION-DETAILS", "88m island platform, Pc3 building<br><i>A footbridge allows access to the platform.</i>");
    */
}

function add_goods_details($state, $location)
{
    global $t;

    /*
    $t->setCurrentBlock("OPT-GOODS-DETAILS");
    $t->setVariable("GOODS-DETAILS", "Goods shed, Gantry crane, Loading bank");
    */
}

function add_infra_details($state, $location)
{
    global $t;

    $infra = array();

    /*
     * Turntable details
     */
    $list = get_location_turntables($state, $location);
    if ($list)
    {
        $label = sprintf("<b>Turntable%s</b>: ", count($list) > 1 ? "s" : "");

        $entries = array();
        foreach ($list as $x)
        {
            if (!$x["size"])
                $size = "unknown size,";
            else
            if (floor($x["size"]) != $x["size"])
            {
                $size = sprintf("%d'%.0f\"", floor($x["size"]),
                    ($x["size"] - floor($x["size"])) * 12);
            }
            else
                $size = $x["size"] . "'";

            if ($x["type"] == "unknown")
                $type = "manual?";
            else
                $type = $x["type"];

            if ($x["status"] == "unknown")
                $status = "unknown status";
            else
                $status = $x["status"];

            $text = "";
            if ($x["text"])
                $text = " (<i>" . $x["text"] . "</i>)";

            array_push($entries, sprintf("%s %s, %s%s", $size, $type, $status, $text));
        }

        array_push($infra, $label . implode("; ", $entries));
    }

    if (count($infra))
    {
        $t->setCurrentBlock("OPT-INFRA-DETAILS");
        $t->setVariable("INFRA-DETAILS", implode("<br>", $infra));
        $t->parseCurrentBlock();
    }
}

function add_link_details($state, $location)
{
    global $db, $t;

    /* XXX: move to dbutil.php */
    $stmt = mysql_query("
        select
            LU.text,
            LU.url
        from
            r_location_url LU
        where
            LU.location_state = '$state'
            and
            LU.location_name = '$location'
        order by
            LU.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list($text, $url) = $row;

        $t->setCurrentBlock("URL-DETAILS");
        $t->setVariable("LINK-URL", $url);
        $t->setVariable("LINK-TEXT", $text);
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
}

/*
 * Populate the PHOTO-DETAILS block
 */
function add_photo_details($state, $location)
{
    global $db, $t;

    /*
     * Build a list of the years for which we have photos for this location
     */
    /* XXX: move to dbutil.php */
    $stmt = mysql_query("
        select distinct
            LP.year,
            LP.year_error
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$location'
            and
            LP.status = 'Y'
        order by
            LP.year
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $years = "";
    while ($row = mysql_fetch_array($stmt))
    {
        list($year, $year_error) = $row;

        if ($years)
            $years .= ", ";

        $years .= date_cpts2text(0, 0, $year, $year_error);
    }
    mysql_free_result($stmt);

    /* only display the block if it is not empty */
    if ($years)
    {
        $t->setCurrentBlock("PHOTO-DETAILS");
        $t->setVariable("PHOTO-YEARS", $years);
        $t->parseCurrentBlock();
    }
}

/*
 * Add any photographs for this location to the page
 */
function add_photo_thumbnails($state, $location, $line_state, $line, $segment)
{
    global $db, $t;

    $stmt = mysql_query("
        select
            LP.seqno,
            LP.file,
            LP.status
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$location'
        order by
            LP.year,
            LP.month,
            LP.day,
            LP.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $nphotos = 0;
    while ($row = mysql_fetch_array($stmt))
    {
        list($seqno, $file, $status) = $row;

        if ($status == "Y")
        {
            $base = ereg_replace(".jpg", "", $file);

            $url = "/locations/photo.php?"
                . urlenc("name=$state:$location:$seqno");

            if ($line)
                $url = $url . urlenc("&line=$line_state:$line:$segment");

            $t->setCurrentBlock("PHOTO");
            $t->setVariable("PHOTO-URL", $url);
            $t->setVariable("PHOTO-THUMB", "/locations/photos/small/$base.jpg");
            $t->parseCurrentBlock();
        }

        $nphotos++;
    }
    mysql_free_result($stmt);

    if ($nphotos > 0)
    {
        if (auth_priv_admin())
        {
            $url = "edit-photos.php?"
                . urlenc("name=$state:$location");
            if ($line)
                $url = $url . urlenc("&line=$line_state:$line:$segment");

            $t->setCurrentBlock("PHOTO-LIST");
            $t->setVariable("EDIT-PHOTO-URL", $url);
            $t->parseCurrentBlock();
        }
    }
}

/*
 * Add any diagrams for this location to the page
 */
function add_diagram_images($state, $location)
{
    global $db, $t;

    $stmt = mysql_query("
        select
            LD.seqno,
            LD.year,
            LD.file
        from
            r_location_diagram LD
        where
            LD.location_state = '$state'
            and
            LD.location_name = '$location'
        order by
            LD.year
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    while ($row = mysql_fetch_array($stmt))
    {
        list($seqno, $year, $file) = $row;

        $t->setCurrentBlock("DIAGRAM");
        $t->setVariable("DIAGRAM-YEAR", $year);
        $t->setVariable("DIAGRAM-IMG", "/locations/diagrams/$file.gif");
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
}

function add_prev_next_links($state, $location, $line_state, $line, $segment)
{
    global $db, $t;

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
            RLn.line_state = '$line_state'
            and
            RLn.line_name = '$line'
            and
            RLn.segment = '$segment'
            and
            RLn.location_state = '$state'
            and
            RLn.location_name = '$location'
            and
            RL.line_state = RLn.line_state
            and
            RL.line_name = RLn.line_name
            and
            RL.segment = RLn.segment
    ";

    $stmt = mysql_query("
        $sql_base
        and
        RL.seqno = RLn.seqno - 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
        list($prev_state, $prev_location) = $row;

    mysql_free_result($stmt);

    $stmt = mysql_query("
        $sql_base
        and
        RL.seqno = RLn.seqno + 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
        list($next_state, $next_location) = $row;

    mysql_free_result($stmt);

    if ($prev_location)
    {
        $t->setCurrentBlock("PREV-LOCATION");
        $t->setVariable("PREV-URL", "/locations/show.php?"
            . urlenc("name=$prev_state:$prev_location&line=$line_state:$line:$segment"));
        $t->setVariable("PREV-TEXT", $prev_location);
        $t->parseCurrentBlock();
    }
    if ($next_location)
    {
        $t->setCurrentBlock("NEXT-LOCATION");
        $t->setVariable("NEXT-URL", "/locations/show.php?"
            . urlenc("name=$next_state:$next_location&line=$line_state:$line:$segment"));
        $t->setVariable("NEXT-TEXT", $next_location);
        $t->parseCurrentBlock();
    }
    
}

function add_map_sheet_link($state, $location, $l)
{
    global $db, $t;

    $stmt = mysql_query("
        select
            RM.line_state,
            RM.line_name,
            RM.seqno
        from
            r_line_map RM,
            r_location L,
            r_line_location RL
        where
            L.location_state = '$state'
            and
            L.location_name = '$location'
            and
            RL.location_state = L.location_state
            and
            RL.location_name = L.location_name
            and
            RL.mainline = 'Y'
            and
            RM.line_state = RL.line_state
            and
            RM.line_name = RL.line_name
            and
            L.geo_x >= RM.centre_x - RM.width/2
            and
            L.geo_x <= RM.centre_x + RM.width/2
            and
            L.geo_y >= RM.centre_y - RM.height/2
            and
            L.geo_y <= RM.centre_y + RM.height/2
        order by
            (L.geo_x - RM.centre_x) * (L.geo_x - RM.centre_x)
                + (L.geo_y - RM.centre_y) * (L.geo_y - RM.centre_y)
        limit 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
    {
        list($line_state, $line, $sheet) = $row;

        $t->setCurrentBlock("LINE-MAP-LINK");
        $t->setVariable("URL", "/lines/linemap.php?"
            . urlenc("name=$line_state:$line:$sheet"));
        $t->setVariable("TEXT", "line-map");
        $t->parseCurrentBlock();
    }
    mysql_free_result($stmt);
}

?>
