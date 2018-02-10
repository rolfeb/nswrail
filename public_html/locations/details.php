<?php

require "site.inc";

function add_location_history($tp, $state, $location)
{
    $history = get_location_history($state, $location);

    $count = 0;
    foreach ($history as $h)
    {
        $count++;

        $name = $h["name"];
        $date = date_cpts2html($h["day"], $h["month"], $h["year"],
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
            'ne_date' => $date,
            'event' => $text,
        ];
    }

    return $tp;
}

function add_location_lines($tp, $state, $location)
{
    global $db;

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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($line_state, $line, $description);

    $count = 0;
    while ($stmt->fetch()) {
        $tp['lines'][] = [
            'nc_url' => '/lines/details.php?' . urlenc("name=$line_state:$line"),
            'text' => $description,
        ];
    }
    $stmt->close();

    return $tp;
}

function add_station_details($tp, $state, $location)
{
    /* TODO: implement station details? */

    /*
    $t->setCurrentBlock("OPT-STATION-DETAILS");
    $t->setVariable("STATION-DETAILS", "88m island platform, Pc3 building<br><i>A footbridge allows access to the platform.</i>");
    */
    return $tp;
}

function add_goods_details($tp, $state, $location)
{
    /* TODO: implement goods details? */

    /*
    $t->setCurrentBlock("OPT-GOODS-DETAILS");
    $t->setVariable("GOODS-DETAILS", "Goods shed, Gantry crane, Loading bank");
    */
    return $tp;
}

function add_infra_details($tp, $state, $location)
{
    $infra = [];

    /*
     * Turntable details
     */
    $list = get_location_turntables($state, $location);
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
                $text = " (<i>" . $x["text"] . "</i>)";
            }

            array_push($entries, sprintf("%s %s, %s%s", $size, $type, $status, $text));
        }

        array_push($infra, $label . implode("; ", $entries));
    }

    if (count($infra)) {
        $tp['other_details'] = implode("<br>", $infra);
    }

    return $tp;
}

function add_link_details($tp, $state, $location)
{
    global $db;

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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($text, $url);

    while ($stmt->fetch()) {
        $tp['links'][] = [
            'nc_url' => $url,
            'text' => $text,
        ];
    }
    $stmt->close();

    return $tp;
}

function add_photo_details($tp, $state, $location)
{
    global $db;

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
    ")
        or dbi_error_trace("prepare failed");

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
function add_photo_thumbnails($tp, $state, $location, $line_state, $line, $segment)
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            LP.seqno,
            LP.file,
            LP.hold,
            LP.year,
            IFNULL(U.fullname, LP.legacy_owner) as fullname,
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
    ")
        or dbi_error_trace("prepare failed");

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

/*
 * Add any diagrams for this location to the page
 */
function add_diagram_images($tp, $state, $location)
{
    global $db;

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
    ")
        or dbi_error_trace("prepare failed");

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

function add_prev_next_links($tp, $state, $location, $line_state, $line, $segment)
{
    global $db;

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
    ")
        or dbi_error_trace("prepare failed");

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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ssiss", $line_state, $line, $segment, $state, $location);
    $stmt->execute();
    $stmt->bind_result($next_state, $next_location);

    $stmt->fetch();
    $stmt->close();

    if ($prev_location) {
        $tp['ne_line_prev_url'] = "/locations/details.php?"
            . urlenc("name=$prev_state:$prev_location&line=$line_state:$line:$segment");
        $tp['line_prev_name'] = $prev_location;
    }

    if ($next_location) {
        $tp['ne_line_next_url'] = '/locations/details.php?'
            . urlenc("name=$next_state:$next_location&line=$line_state:$line:$segment");
        $tp['line_next_name'] = $next_location;
    }

    if ($prev_location) {
        if ($next_location) {
            $tp['prev_next_icon'] = "/c/images/locn_nav_np.png";
        }
        else {
            $tp['prev_next_icon'] = "/c/images/locn_nav_p.png";
        }
    } else {
        $tp['prev_next_icon'] = "/c/images/locn_nav_n.png";
    }
    
    return $tp;
}

$name = quote_external(get_post("name"));           /* mandatory */
$state = quote_external(get_post("state"));         /* obsolete */
$location = quote_external(get_post("location"));   /* obsolete */
$line = quote_external(get_post("line"));           /* optional */
$mode = quote_external(get_post("mode"));           /* optional */

if ($name)
    list($state,$location) = explode(":", $name);

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

$line_state = null;
$line_name = null;
$segment = null;
if ($line)
    list($line_state, $line_name, $segment) = explode(":", $line);

$l = get_location_details($state, $location);

$head_extra = '';

$version = $l["version"];

if ($l["geox"] and $l["geoy"])
{
    $latlong = sprintf("(%.4f&deg;, %.4f&deg;)",
                    $l["geox"], $l["geoy"]);
}
else
    $latlong = "unknown";
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

$tp = add_location_history($tp, $state, $location);
$tp = add_location_lines($tp, $state, $location);
$tp = add_station_details($tp, $state, $location);
$tp = add_goods_details($tp, $state, $location);
$tp = add_infra_details($tp, $state, $location);
$tp = add_link_details($tp, $state, $location);
$tp = add_photo_details($tp, $state, $location);

if ($line_state != "" and $line_name != "")
    $tp = add_prev_next_links($tp, $state, $location, $line_state, $line_name, $segment);

if (array_key_exists("altdist", $l))
{
    $tp['alt_distance'] = sprintf("%.3f", $l["altdist"]);
    $tp['alt_origin'] = $origin;
    $tp['alt_via'] = $l["altvia"];
}

/*
 * Add any photos or diagrams to the page
 */
$tp = add_photo_thumbnails($tp, $state, $location, $line_state, $line_name, $segment);
$tp = add_diagram_images($tp, $state, $location);

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
$tp['ne_latlong'] = $latlong;
$tp['posaccuracy'] = $posaccuracy;
$tp['distance'] = $km_syd;
$tp['origin'] = $origin;

$tp['desc_text'] = $l["desc"];
$tp['curr_text'] = $l["curr"];

$head_extra .= '<script src="/c/js/ajaxutil.js" type="text/javascript"></script>';
$head_extra .= '<script src="locations.js" type="text/javascript" ></script>';
$head_extra .= "\n" . implode(file('show.hdr'), "");

$args = array();
if ($head_extra)
    $args['HEAD-EXTRA'] = $head_extra;
$latte = new Latte\Engine;
display_page($title, $latte->renderToString('details.latte', $tp), $args);

?>
