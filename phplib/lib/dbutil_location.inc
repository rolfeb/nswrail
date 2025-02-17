<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require_once 'dbutil.inc';

/**
 * @param $state
 * @param $location
 * @return bool
 */
function is_valid_location($state, $location)
{
    /** @var mysqli $db */
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
    ");
    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $ok = $stmt->fetch();
    $stmt->close();
    return !!$ok;
}

/**
 * @param $state
 * @param $location
 * @param $type
 * @param $first
 * @return string
 */
function get_location_event_date_str($state, $location, $type, $first)
{
    /** @var mysqli $db */
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
    ");
    $stmt->bind_param("sss", $state, $location, $type);
    $stmt->execute();
    $stmt->bind_result($day, $month, $year, $year_error);

    $found = FALSE;
    $date_str = "";
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

/**
 * @param $state
 * @param $location
 * @return string
 */
function count_photos($state, $location)
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();

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
            LP.hold is null
    ");
    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($count);

    if (!$stmt->fetch())
        $count = "?";

    $stmt->close();

    return $count;
}
