<?php

/*
 * Return a number of random photos as stored in the database
 */
function db_random_pics($count)
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            state,
            name,
            seqno,
            file
        from
            r_random_photos
        order by
            id
        limit ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("d", $count);
    $stmt->execute();
    $stmt->bind_result($state, $name, $seqno, $file);

    $photos = array();
    while ($stmt->fetch()) {
        $photos[] = array($state, $name, $seqno, $file);
    }

    $stmt->close();

    return $photos;
}

/*
 * Return the number of unique locations in the database
 */
function db_count_locations()
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select count(*) from r_location
    ")
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $stmt->bind_result($count);

    if (!$stmt->fetch()) {
        $count = 0;
    }

    $stmt->close();

    return $count;
}

/*
 * Return the number of published photographs in the database
 */
function db_count_photographs()
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select count(*) from r_location_photo
        where
            status = 'Y'
            and
            released = 'Y'
    ")
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $stmt->bind_result($count);

    if (!$stmt->fetch()) {
        $count = 0;
    }

    $stmt->close();

    return $count;
}

/*
 * Return the number and age of recently published photographs in the
 * database
 */
function db_count_recent_photographs()
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            DATEDIFF(SYSDATE(), submit_date)    age_in_days,
            count(*)
        from
            r_location_photo
        where
            DATEDIFF(SYSDATE(), submit_date) < 28
            and
            status = 'Y'
            and
            released = 'Y'
        group by
            age_in_days
        order by
            age_in_days asc
        limit
            1
    ")
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $stmt->bind_result($age, $count);

    if (!$stmt->fetch()) {
        $count = 0;
        $age = 0;
    }

    $stmt->close();

    return array($count, $age);
}

?>
