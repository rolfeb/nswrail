<?php

/*
 * Get the line details
 */
function get_line_details($state, $line)
{
    global $db;


    $stmt = mysql_query("
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
            R.line_state = '$state'
            and
            R.line_name = '$line'
            and
            RS.line_state = R.line_state
            and
            RS.line_name = R.line_name
        group by
            RS.line_state,
            RS.line_name
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
        list($fullname, $region, $traffic, $maxsegment, $version) = $row;

    mysql_free_result($stmt);

    $stmt = mysql_query("
        select
            RT.text
        from
            r_line_text RT
        where
            RT.line_state = '$state'
            and
            RT.line_name = '$line'
            and
            RT.type = 'DESC'
            and
            RT.status != 'N'
        order by
            RT.seqno desc
        limit 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    if ($row = mysql_fetch_array($stmt))
        list($desc) = $row;
    else
        $desc = "";

    mysql_free_result($stmt);

    return array($fullname, $region, $traffic, $maxsegment, $desc, $version);
}

/*
 * Given a line, work out its current overall status
 */
function get_line_status_class($state, $line)
{
    global $db;

    $stmt = mysql_query("
        select distinct
            SEV.segment,
            SEV.start_state,
            SEV.start_name,
            SEV.end_state,
            SEV.end_name
        from
            r_section_event SEV
        where
            SEV.line_state = '$state'
            and
            SEV.line_name = '$line'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $status = "lifted";

    $sections = array(
        "ON"    => 0,
        "CN"    => 0,
        "OT"    => 0,
        "CT"    => 0,
        "LI"    => 0,
        "NO"    => 0,
    );

    while ($row = mysql_fetch_array($stmt))
    {
        list($segment, $start_state, $start_name, $end_state, $end_name)
            = $row;

        $stmt2 = mysql_query("
            select
                SEV.type
            from
                r_section_event SEV
            where
                SEV.line_state = '$state'
                and
                SEV.line_name = '$line'
                and
                SEV.segment = $segment
                and
                SEV.start_state = '$start_state'
                and
                SEV.start_name = '$start_name'
                and
                SEV.end_state = '$end_state'
                and
                SEV.end_name = '$end_name'
                and
                SEV.type in ( 'ON', 'CN', 'OT', 'CT', 'LI', 'NO' )
            order by
                SEV.seqno desc
            limit 1
        ", $db)
            or die("prepare failed: " . mysql_error() . "\n");

        list($type) = mysql_fetch_array($stmt2);

        mysql_free_result($stmt2);

        $sections[$type]++;

        if ($type == 'CN')
            $status = "closed";
        if ($type == 'ON')
            $status = "open";
        if ($type == 'OT')
            $status = "open";
    }
    mysql_free_result($stmt);

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

    return $status;
}

function get_linemap_count($state, $line)
{
    global $db;

    $stmt = mysql_query("
        select
            count(*)
        from
            r_line_map RM
        where
            RM.line_state = '$state'
            and
            RM.line_name = '$line'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $count = 0;
    if ($row = mysql_fetch_array($stmt))
        $count = $row[0];

    mysql_free_result($stmt);

    return $count;
}

?>
