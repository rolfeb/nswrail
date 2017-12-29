<?php

/*
 * Get the line details
 */
function dbline_get_details($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            R.description,
            R.region,
            R.traffic,
            max(RS.segment),
            R.version
        from
            r_line R,
            r_line_segment RS
        where
            R.line_state = ?
            and
            R.line_name = ?
            and
            RS.line_state = R.line_state
            and
            RS.line_name = R.line_name
        group by
            RS.line_state,
            RS.line_name
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($fullname, $region, $traffic, $maxsegment, $version);

    $stmt->fetch();

    $stmt->close();

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            RT.text
        from
            r_line_text RT
        where
            RT.line_state = ?
            and
            RT.line_name = ?
            and
            RT.type = 'DESC'
            and
            RT.status != 'N'
        order by
            RT.seqno desc
        limit 1
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($desc);

    if (!$stmt->fetch())
        $desc = '';

    $stmt->close();

    return array($fullname, $region, $traffic, $maxsegment, $desc, $version);
}

/*
 * Given a line, work out its current overall status
 */
function get_line_status_class($state, $line)
{
    global $dbi;

    $stmt1 = $dbi->stmt_init();
    $stmt1->prepare("
        select distinct
            SEV.segment,
            SEV.start_state,
            SEV.start_name,
            SEV.end_state,
            SEV.end_name
        from
            r_section_event SEV
        where
            SEV.line_state = ?
            and
            SEV.line_name = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt1->bind_param("ss", $state, $line);
    $stmt1->execute();
    $stmt1->store_result();
    $stmt1->bind_result($segment, $start_state, $start_name, $end_state,
        $end_name);

    $stmt2 = $dbi->stmt_init();
    $stmt2->prepare("
        select
            SEV.type
        from
            r_section_event SEV
        where
            SEV.line_state = ?
            and
            SEV.line_name = ?
            and
            SEV.segment = ?
            and
            SEV.start_state = ?
            and
            SEV.start_name = ?
            and
            SEV.end_state = ?
            and
            SEV.end_name = ?
            and
            SEV.type in ( 'ON', 'CN', 'OT', 'CT', 'LI', 'NO' )
        order by
            SEV.seqno desc
        limit 1
    ")
        or dbi_error_trace("prepare failed");

    $sections = array(
        "ON"    => 0,
        "CN"    => 0,
        "OT"    => 0,
        "CT"    => 0,
        "LI"    => 0,
        "NO"    => 0,
    );

    while ($stmt1->fetch())
    {
        $stmt2->bind_param("ssissss", $state, $line, $segment, $start_state,
            $start_name, $end_state, $end_name);

        $stmt2->execute();
        $stmt2->bind_result($type);

        $stmt2->fetch();

        $sections[$type]++;
    }
    $stmt2->close();
    $stmt1->close();

    $ON = $sections["ON"];
    $OT = $sections["OT"];
    $CX = $sections["CN"] + $sections["CT"];
    $LI = $sections["LI"];
    $NO = $sections["NO"];

    #
    # Determine an overall status based on the sections
    #
    if ($ON == 0 && $CX == 0 && $OT == 0)
    {
        return $NO == 0 ? 'lifted' : 'not-opened';
    }
    else
    if ($OT > 0 && $ON == 0)
    {
        return "tourist";
    }
    else
    if ($ON > 0)
    {
        return "open";
    }
    else
    {
        return "closed";
    }
}

function get_linemap_count($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            count(*)
        from
            r_line_map RM
        where
            RM.line_state = ?
            and
            RM.line_name = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($count);

    if (!$stmt->fetch())
        $count = 0;

    $stmt->close();

    return $count;
}

/**
 * dbline_get_length
 *
 * Given a line, return the length of all track in km.
 *
 * Returns an array of:
 *  - total length in km
 *  - flag that is true if some segment lengths are unknown
 */
function dbline_get_length($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select
            abs(L1.distance - L2.distance)
        from
            r_line R,
            r_line_segment RS,
            r_line_location RL1,
            r_location L1,
            r_line_location RL2,
            r_location L2
        where
            R.line_state = ?
            and
            R.line_name = ?
            and
            RS.line_state = R.line_state
            and
            RS.line_name = R.line_name
            and
            RL1.line_state = RS.line_state
            and
            RL1.line_name = RS.line_name
            and
            RL1.segment = RS.segment
            and
            RL1.seqno = 1
            and
            L1.location_state = RL1.location_state
            and
            L1.location_name = RL1.location_name
            and
            RL2.line_state = RS.line_state
            and
            RL2.line_name = RS.line_name
            and
            RL2.segment = RS.segment
            and
            RL2.seqno = (
                select
                    max(RLX.seqno)
                from
                    r_line_location RLX
                where
                    RLX.line_state = RS.line_state
                    and
                    RLX.line_name = RS.line_name
                    and
                    RLX.segment = RS.segment
            )
            and
            L2.location_state = RL2.location_state
            and
            L2.location_name = RL2.location_name
        order by
            R.line_state,
            R.line_name,
            RS.segment
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($length);

    $total_length = 0;
    $over_flag = 0;

    while ($stmt->fetch())
    {
        if ($length != null)
            $total_length += $length;
        else
            $over_flag = 1;
    }

    $stmt->close();

    $length = sprintf("%.02f", $total_length);

    return array($length, $over_flag);
}

/**
 * dbline_get_station_count
 *
 * Given a line, return the number of active and closed stations
 *
 * Returns an array of:
 *  - number of stations in use
 *  - total number of stations on the line
 */
function dbline_get_station_count($state, $line)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select distinct
            RL.location_state,
            RL.location_name,
            L.status
        from
            r_line_location RL,
            r_location L
        where
            RL.line_state = ?
            and
            RL.line_name = ?
            and
            L.location_state = RL.location_state
            and
            L.location_name = RL.location_name
            and
            L.type in ('station', 'platform', 'halt')
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $line);
    $stmt->execute();
    $stmt->bind_result($location_state, $location_name, $status);

    $total_count = 0;
    $active_count = 0;

    while ($stmt->fetch())
    {
        $total_count++;
        if ($status == 'in use')
            $active_count++;
    }

    $stmt->close();

    return array($active_count, $total_count);
}

?>
