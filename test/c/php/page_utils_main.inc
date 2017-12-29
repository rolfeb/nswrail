<?php

/*
 * Return a number of random photos as stored in the database
 */
function db_random_pics($count)
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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
    while ($stmt->fetch())
    {
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
    global $dbi;

    $stmt = $dbi->stmt_init();
    $stmt->prepare("
        select count(*) from r_location
    ")
        or dbi_error_trace("prepare failed");

    $stmt->execute();
    $stmt->bind_result($count);

    if (!$stmt->fetch())
        $count = 0;

    $stmt->close();

    return $count;
}

/*
 * Return the number of published photographs in the database
 */
function db_count_photographs()
{
    global $dbi;

    $stmt = $dbi->stmt_init();
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

    if (!$stmt->fetch())
        $count = 0;

    $stmt->close();

    return $count;
}

/*
 * Count the number of visitors in the counter file
 */
function count_visitors()
{
    $file = "visitors.dat";

    if (!file_exists($file) or !($fp = fopen($file, "r+")))
        return "???";

    $line = fgets($fp, 256);
    list ($date, $count) = explode(",", $line);
    $count += 1;
    rewind($fp);
    $str = sprintf("%s,%d\n", $date, $count);
    fputs($fp, $str);
    fclose($fp);

    return $count;
}

?>
