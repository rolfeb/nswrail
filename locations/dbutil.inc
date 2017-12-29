<?php

/*
 * Return general details for a location
 */
function get_location_details($state, $location)
{
    global $dbi;

    $result = null;

    /*
     * Get the location details
     */
    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

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
            "geoexact"  => $geo_exact,
            "desc"      => $desc,
            "curr"      => $curr,
            "nphoto"    => $nphoto,
            "ndiagram"  => $ndiagram,
            "version"   => $version,
        );
    }
    $stmt->close();

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

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
function get_location_history($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

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

function get_location_text($state, $location, $type)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("sss", $state, $location, $type);
    $stmt->execute();
    $stmt->bind_result($text);

    if (!$stmt->fetch())
        $text = "";

    $stmt->close();

    return $text;
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

function get_location_ndiagrams($state, $location)
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

/*
 * Return location turntable information
 */
function get_location_turntables($state, $location)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    ")
        or dbi_error_trace("prepare failed");

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

?>
