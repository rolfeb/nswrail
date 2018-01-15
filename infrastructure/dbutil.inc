<?php

function count_photos($state, $location)
{
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
            LP.status != 'N'
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ss", $state, $location);
    $stmt->execute();
    $stmt->bind_result($count);

    if (!$stmt->fetch())
        $count = "?";

    $stmt->close();

    return $count;
}

?>
