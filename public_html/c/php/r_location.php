<?php

require_once 'dbutil.inc';

function is_valid_location($state, $location)
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            1
        from
            r_location
        where
            location_state = ?
            and
            location_name = ?
    ")
        or die("prepare failed: " . $db->error . "\n");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();

    $ok = True;
    if (!$stmt->fetch())
        $ok = False;

    $stmt->close();
    return $ok;
}

function get_location_event_date_str($state, $location, $type, $first)
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            day,
            month,
            year,
            year_error
        from
            r_location_event
        where
            location_state = ?
            and
            location_name = ?
            and
            type = ?
        order by 
            seqno
    ")
        or die("prepare failed: " . $db->error . "\n");

    $stmt->bind_param("sss", $state, $location, $type);
    $stmt->execute();
    $stmt->bind_result($day, $month, $year, $year_error);

    $found = False;
    while ($stmt->fetch())
    {
        $found = True;
        $date_str = date_cpts2text($day, $month, $year, $year_error);

        if ($first)
            break;
    }
    $stmt->close();

    if ($found)
        return $date_str;

    return 'unknown';
}

?>
