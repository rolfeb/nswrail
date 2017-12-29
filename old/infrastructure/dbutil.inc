<?php

function count_photos($state, $location)
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
            LP.status != 'N'
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $count = "?";
    if ($row = mysql_fetch_array($stmt))
        list($count) = $row;

    mysql_free_result($stmt);

    return $count;
}

?>
