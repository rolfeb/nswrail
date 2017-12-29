<?php

function get_location_icon($db, $line_state, $line, $segment, $location_state,
    $location, $seqno, $maxseqno, $type, $status)
{
    $status_map = array(
        ""              => "u",
        "ON"            => "i",
        "CN"            => "a",
        "OT"            => "t",
        "CT"            => "a",
        "LI"            => "l",
        "not opened"    => "u",
        "in use"        => "i",
        "closed"        => "a",
        "reused"        => "i",
        "unknown"       => "u",
    );

    $icon = "";

    $status = $status_map[$status];

    /*
     * Work out the icon for this location
     */
    if ($type == "halt" or $type == "crossing" or $type == "border"
        or $type == "dead end" or $type == "tunnel" or $type == "tank")
    {
        if ($type == "dead end")
            $base = "de1";
        elseif ($type == "tunnel")
            $base = "tnl";
        elseif ($type == "tank")
            $base = "halt";
        else
            $base = $type;

        /*
         * These locations are always intermediate, and have the same
         * up and down status
         */
        $target_up = get_target_up_status($db, $line_state, $line,
            $segment, $location_state, $location);
        $target_up = $status_map[$target_up];

        $icon = "${base}_${target_up}";
    }
    elseif ($type == "loop")
    {
        $target_up = get_target_up_status($db, $line_state, $line,
            $segment, $location_state, $location);
        $target_up = $status_map[$target_up];

        $target_down = get_target_down_status($db, $line_state, $line,
            $segment, $location_state, $location);
        $target_down = $status_map[$target_down];

        $icon = "loop_${status}_${target_up}${target_down}";
    }
    elseif ($type == "siding" or $type == "yard")
    {
        $base = ($type == "siding" ? "sdg" : "yard");

        if ($seqno == $maxseqno)
        {
            /* terminal siding/yard */
            $icon = "${base}1_${status}";
        }
        else
        {
            /* intermediate siding/yard */
            $target_up = get_target_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_up = $status_map[$target_up];

            $target_down = get_target_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_down = $status_map[$target_down];

            $icon = "${base}_${status}_${target_up}${target_down}";
        }
    }
    elseif ($type == "junction")
    {
        if ($seqno == 1)
        {
            /* starting junction */
            $target_down = get_target_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_down = $status_map[$target_down];

            $other_up = get_other_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_up = $status_map[$other_up];

            $other_down = get_other_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_down = $status_map[$other_down];

            $icon = "jct0_${target_down}_${other_up}${other_down}";
        }
        elseif ($seqno == $maxseqno)
        {
            /* terminal junction */
            $target_up = get_target_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_up = $status_map[$target_up];

            $other_up = get_other_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_up = $status_map[$other_up];

            $other_down = get_other_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_down = $status_map[$other_down];

            $icon = "jct1_${target_up}_${other_up}${other_down}";
        }
        else
        {
            /* intermediate junction */
            $target_up = get_target_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_up = $status_map[$target_up];

            $target_down = get_target_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_down = $status_map[$target_down];

            $other_up = get_other_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_down = get_other_down_status($db, $line_state, $line,
                $segment, $location_state, $location);

            $other = $other_up != "" ? $other_up : $other_down;
            $other = $status_map[$other];

            $icon = "jct_${other}_${target_up}${target_down}";
        }
    }
    elseif ($type == "station" or $type == "platform" or $type == "mine"
        or $type == "colliery" or $type == "other" or $type == "unknown")
    {
        if ($seqno == 1)
        {
            /* starting [junction] station */
            $target_down = get_target_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_down = $status_map[$target_down];

            $other_up = get_other_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_up = $status_map[$other_up];

            $other_down = get_other_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_down = $status_map[$other_down];

            $icon = "jct_stn0_${target_down}_${other_up}${other_down}";
        }
        elseif ($seqno == $maxseqno)
        {
            /* terminal station - may also be a junction */
            $target_up = get_target_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_up = $status_map[$target_up];

            $other_up = get_other_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_down = get_other_down_status($db, $line_state, $line,
                $segment, $location_state, $location);

            if ($other_up != "" and $other_down != "")
            {
                /* must be a terminal junction station */
                $other_up = $status_map[$other_up];
                $other_down = $status_map[$other_down];

                $icon = "jct_stn1_${target_up}_${other_up}${other_down}";
            }
            else
            {
                /* must be a terminal station */
                $icon = "stn1_${target_up}";
            }
        }
        else
        {
            /* intermediate station - may also be a junction */
            $target_up = get_target_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_up = $status_map[$target_up];

            $target_down = get_target_down_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $target_down = $status_map[$target_down];

            $other_up = get_other_up_status($db, $line_state, $line,
                $segment, $location_state, $location);
            $other_down = get_other_down_status($db, $line_state, $line,
                $segment, $location_state, $location);

            if ($other_up != "" or $other_down != "")
            {
                /* must be an intermediate junction station */
                $other = $other_up != "" ? $other_up : $other_down;
                $other = $status_map[$other];

                $icon = "jct_stn_${other}_${target_up}${target_down}";
            }
            else
            {
                /* must be an intermediate station */
                $icon = "stn_${target_up}${target_down}";
            }
        }
    }

    return "/lines/icons/$icon.gif";
}

function get_target_up_status($db, $line_state, $line, $segment, $location_state, $location)
{
    static $stmt = false;
    static $l_line_state;
    static $l_line;
    static $l_segment;
    static $l_location_state;
    static $l_location;

    if (!$stmt)
    {
        $stmt = $db->prepare("
            -- target line, up status
            select
                SEV.type
            from
                r_section_event SEV,
                r_line_location RL0,
                r_line_location RL1,
                r_line_location RL2
            where
                SEV.line_state = ?
                and
                SEV.line_name = ?
                and
                SEV.segment = ?
                and
                RL1.location_state = ?
                and
                RL1.location_name = ?
                and
                RL1.line_state = SEV.line_state
                and
                RL1.line_name = SEV.line_name
                and
                RL1.segment = SEV.segment
                and
                -- only consider open/close/lift events
                SEV.type in ( 'ON', 'CN', 'OT', 'CT', 'LI' )
                and
                -- join to location at start of section:
                RL0.line_state = SEV.line_state
                and
                RL0.line_name = SEV.line_name
                and
                RL0.segment = SEV.segment
                and
                RL0.location_state = SEV.start_state
                and
                RL0.location_name = SEV.start_name
                and
                RL0.seqno < RL1.seqno   -- section before this location
                and
                -- join to location at end of section:
                RL2.line_state = SEV.line_state
                and
                RL2.line_name = SEV.line_name
                and
                RL2.segment = SEV.segment
                and
                RL2.location_state = SEV.end_state
                and
                RL2.location_name = SEV.end_name
                and
                RL2.seqno >= RL1.seqno
            order by
                -- get most recent event
                SEV.year desc,
                SEV.month desc,
                SEV.day desc
            limit
                1
        ");
        $stmt->bind_param("ssiss", $l_line_state, $l_line, $l_segment,
            $l_location_state, $l_location);
    }

    $l_line_state = $line_state;
    $l_line = $line;
    $l_segment = $segment;
    $l_location_state = $location_state;
    $l_location = $location;

    $stmt->execute();
    $stmt->bind_result($type);

    if ($stmt->fetch())
    {
        $result = $type;
        while ($stmt->fetch()) { }  /* flush result set */
    }
    else
        $result = "";

    return $result;
}

function get_target_down_status($db, $line_state, $line, $segment, $location_state, $location)
{
    static $stmt = false;
    static $l_line_state;
    static $l_line;
    static $l_segment;
    static $l_location_state;
    static $l_location;

    if (!$stmt)
    {
        $stmt = $db->prepare("
            -- target line, down status
            select
                SEV.type
            from
                r_section_event SEV,
                r_line_location RL0,
                r_line_location RL1,
                r_line_location RL2
            where
                SEV.line_state = ?
                and
                SEV.line_name = ?
                and
                SEV.segment = ?
                and
                RL1.location_state = ?
                and
                RL1.location_name = ?
                and
                RL1.line_state = SEV.line_state
                and
                RL1.line_name = SEV.line_name
                and
                RL1.segment = SEV.segment
                and
                -- only consider open/close/lift events
                SEV.type in ( 'ON', 'CN', 'OT', 'CT', 'LI' )
                and
                -- join to location at start of section:
                RL0.line_state = SEV.line_state
                and
                RL0.line_name = SEV.line_name
                and
                RL0.segment = SEV.segment
                and
                RL0.location_state = SEV.start_state
                and
                RL0.location_name = SEV.start_name
                and
                RL0.seqno <= RL1.seqno
                and
                -- join to location at end of section:
                RL2.line_state = SEV.line_state
                and
                RL2.line_name = SEV.line_name
                and
                RL2.segment = SEV.segment
                and
                RL2.location_state = SEV.end_state
                and
                RL2.location_name = SEV.end_name
                and
                RL2.seqno > RL1.seqno  -- section after this location
            order by
                -- get most recent event
                SEV.year desc,
                SEV.month desc,
                SEV.day desc
            limit
                1
        ");
        $stmt->bind_param("ssiss", $l_line_state, $l_line, $l_segment,
            $l_location_state, $l_location);
    }

    $l_line_state = $line_state;
    $l_line = $line;
    $l_segment = $segment;
    $l_location_state = $location_state;
    $l_location = $location;

    $stmt->execute();
    $stmt->bind_result($type);

    if ($stmt->fetch())
    {
        $result = $type;
        while ($stmt->fetch()) { }  /* flush result set */
    }
    else
        $result = "";

    return $result;
}

function get_other_up_status($db, $line_state, $line, $segment, $location_state, $location)
{
    static $stmt = false;
    static $l_line_state;
    static $l_line;
    static $l_segment;
    static $l_location_state;
    static $l_location;

    if (!$stmt)
    {
        $stmt = $db->prepare("
            -- target line, up status
            select
                SEV.type
            from
                r_section_event SEV,
                r_line_location RL0,
                r_line_location RL1,
                r_line_location RL2
            where
                (
                    SEV.line_state != ?
                    or
                    SEV.line_name != ?
                    or
                    SEV.segment != ?
                )
                and
                RL1.location_state = ?
                and
                RL1.location_name = ?
                and
                RL1.line_state = SEV.line_state
                and
                RL1.line_name = SEV.line_name
                and
                RL1.segment = SEV.segment
                and
                -- only consider open/close/lift events
                SEV.type in ( 'ON', 'CN', 'OT', 'CT', 'LI' )
                and
                -- join to location at start of section:
                RL0.line_state = SEV.line_state
                and
                RL0.line_name = SEV.line_name
                and
                RL0.segment = SEV.segment
                and
                RL0.location_state = SEV.start_state
                and
                RL0.location_name = SEV.start_name
                and
                RL0.seqno < RL1.seqno   -- section before this location
                and
                -- join to location at end of section:
                RL2.line_state = SEV.line_state
                and
                RL2.line_name = SEV.line_name
                and
                RL2.segment = SEV.segment
                and
                RL2.location_state = SEV.end_state
                and
                RL2.location_name = SEV.end_name
                and
                RL2.seqno >= RL1.seqno
            order by
                -- get most recent event
                SEV.year desc,
                SEV.month desc,
                SEV.day desc
            limit
                1
        ");
        $stmt->bind_param("ssiss", $l_line_state, $l_line, $l_segment,
            $l_location_state, $l_location);
    }

    $l_line_state = $line_state;
    $l_line = $line;
    $l_segment = $segment;
    $l_location_state = $location_state;
    $l_location = $location;

    $stmt->execute();
    $stmt->bind_result($type);

    if ($stmt->fetch())
    {
        $result = $type;
        while ($stmt->fetch()) { }  /* flush result set */
    }
    else
        $result = "";

    return $result;
}

function get_other_down_status($db, $line_state, $line, $segment, $location_state, $location)
{
    static $stmt = false;
    static $l_line_state;
    static $l_line;
    static $l_segment;
    static $l_location_state;
    static $l_location;

    if (!$stmt)
    {
        $stmt = $db->prepare("
            -- target line, down status
            select
                SEV.type
            from
                r_section_event SEV,
                r_line_location RL0,
                r_line_location RL1,
                r_line_location RL2
            where
                (
                    SEV.line_state != ?
                    or
                    SEV.line_name != ?
                    or
                    SEV.segment != ?
                )
                and
                RL1.location_state = ?
                and
                RL1.location_name = ?
                and
                RL1.line_state = SEV.line_state
                and
                RL1.line_name = SEV.line_name
                and
                RL1.segment = SEV.segment
                and
                -- only consider open/close/lift events
                SEV.type in ( 'ON', 'CN', 'OT', 'CT', 'LI' )
                and
                -- join to location at start of section:
                RL0.line_state = SEV.line_state
                and
                RL0.line_name = SEV.line_name
                and
                RL0.segment = SEV.segment
                and
                RL0.location_state = SEV.start_state
                and
                RL0.location_name = SEV.start_name
                and
                RL0.seqno <= RL1.seqno
                and
                -- join to location at end of section:
                RL2.line_state = SEV.line_state
                and
                RL2.line_name = SEV.line_name
                and
                RL2.segment = SEV.segment
                and
                RL2.location_state = SEV.end_state
                and
                RL2.location_name = SEV.end_name
                and
                RL2.seqno > RL1.seqno  -- section after this location
            order by
                -- get most recent event
                SEV.year desc,
                SEV.month desc,
                SEV.day desc
            limit
                1
        ");
        $stmt->bind_param("ssiss", $l_line_state, $l_line, $l_segment,
            $l_location_state, $l_location);
    }

    $l_line_state = $line_state;
    $l_line = $line;
    $l_segment = $segment;
    $l_location_state = $location_state;
    $l_location = $location;

    $stmt->execute();
    $stmt->bind_result($type);

    if ($stmt->fetch())
    {
        $result = $type;
        while ($stmt->fetch()) { }  /* flush result set */
    }
    else
        $result = "";

    return $result;
}

?>
