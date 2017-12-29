<?php

function commit()
{
    global $db;

    mysql_query("commit", $db) or die("commit failed");
}

function rollback()
{
    global $db;

    mysql_query("rollback", $db) or die("rollback failed");
}

function count_locations()
{
    global $db;

    $stmt = mysql_query("
        select count(*) from r_location
    ", $db);
    if (!$stmt)
        die("prepare failed: " . mysql_error() . "\n");

    list($count) = mysql_fetch_array($stmt);

    mysql_free_result($stmt);

    return $count;
}

function count_photographs()
{
    global $db;

    $stmt = mysql_query("
        select count(*) from r_location_photo
        where
            status = 'Y'
            and
            released = 'Y'
    ", $db);
    if (!$stmt)
        die("prepare failed: " . mysql_error() . "\n");

    list($count) = mysql_fetch_array($stmt);

    mysql_free_result($stmt);

    return $count;
}

?>
