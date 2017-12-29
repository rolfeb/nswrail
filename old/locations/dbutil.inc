<?php

/*
 * Return general details for a location
 */
function get_location_details($state, $location)
{
    global $db;

    $result = null;

    /*
     * Get the location details
     */
    $stmt = mysql_query("
        select
            RL.line_state,
            RL.line_name,
            L.type,
            L.status,
            L.distance,
            L.geo_x,
            L.geo_y,
            L.geo_authority,
            L.version
        from
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
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
    {
        list ($line_state, $line_name, $type, $status, $distance, $geo_x,
            $geo_y, $geo_authority, $version) = $row;

        if (!$type)
            $type = "unknown";

        if (!$status)
            $status = "unknown";

        $desc = get_location_text($state, $location, 'DESC');
        $curr = get_location_text($state, $location, 'CURR');
        $nphoto = get_location_nphotos($state, $location);
        $ndiagram = get_location_ndiagrams($state, $location);

        $result = array(
            "line_state" => $line_state,
            "line_name" => $line_name,
            "type"      => $type,
            "status"    => $status,
            "distance"  => $distance,
            "geox"      => $geo_x,
            "geoy"      => $geo_y,
            "geoauth"   => $geo_authority,
            "desc"      => $desc,
            "curr"      => $curr,
            "nphoto"    => $nphoto,
            "ndiagram"  => $ndiagram,
            "version"   => $version,
        );
    }
    mysql_free_result($stmt);

    $stmt = mysql_query("
        select
            LAT.distance,
            LAT.via_location
        from
            r_location_altdist LAT
        where
            LAT.location_state = '$state'
            and
            LAT.location_name = '$location'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
    {
        list ($altdist, $via_location) = $row;

        $result["altdist"] = $altdist;
        $result["altvia"] = $via_location;
    }
    mysql_free_result($stmt);

    return $result;
}

/*
 * Return location history information
 */
function get_location_history($state, $location)
{
    global $db;

    $stmt = mysql_query("
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
            LEV.location_state = '$state'
            and
            LEV.location_name = '$location'
        order by
            LEV.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $i = 0;
    $result = array();
    while ($row = mysql_fetch_array($stmt))
    {
        list ($type, $day, $month, $year, $year_error, $name, $text) = $row;

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

    return $result;
}

function get_location_text($state, $location, $type)
{
    global $db;

    $stmt = mysql_query("
        select
            LT.text
        from
            r_location_text LT
        where
            LT.location_state = '$state'
            and
            LT.location_name = '$location'
            and
            LT.type = '$type'
        order by
            LT.seqno desc
        limit 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $text = "";
    if ($row = mysql_fetch_array($stmt))
        list($text) = $row;

    mysql_free_result($stmt);

    return $text;
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

function get_location_ndiagrams($state, $location)
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

/*
 * Return location turntable information
 */
function get_location_turntables($state, $location)
{
    global $db;

    $stmt = mysql_query("
        select
            LTT.type,
            LTT.size_ft,
            LTT.status,
            LTT.sellers,
            LTT.text
        from
            r_location_turntable LTT
        where
            LTT.location_state = '$state'
            and
            LTT.location_name = '$location'
        order by
            LTT.seqno
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $results = array();
    while ($row = mysql_fetch_array($stmt))
    {
        list ($type, $size, $status, $sellers, $text) = $row;

        $data = array(
            "type"      => $type,
            "size"      => $size,
            "status"    => $status,
            "sellers"   => $sellers,
            "text"      => $text,
        );

        array_push($results, $data);
    }

    if (count($results) == 0)
        return null;

    return $results;
}

?>
