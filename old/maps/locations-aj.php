<?php

require_once "../init.inc";
require_once "../util.inc";

/*
 * Get location details
 */
$stmt = mysql_query("
    select
        L.location_state,
        L.location_name,
        L.type,
        L.status,
        L.distance,
        L.geo_x,
        L.geo_y,
        L.geo_authority,
        count(LP.seqno)
    from
        r_location L
        left join r_location_photo LP on
            LP.location_state = L.location_state
            and
            LP.location_name = L.location_name
            and
            LP.status = 'Y'
    where
        L.geo_x is not null
        and
        L.geo_y is not null
    group by
        L.location_state,
        L.location_name
", $db)
    or die("prepare failed: " . mysql_error() . "\n");

while ($row = mysql_fetch_array($stmt))
{
    list($state, $location, $type, $status, $distance, $wx, $wy, $authority, $nphotos) = $row;

    $photo = get_representative_photo($state, $location);

    if ($distance)
        $distance = sprintf("%.3f km", $distance);
    else
        $distance = "unknown";

    $type = locn_type2text($type);
    $status = locn_status2text($status);

    echo $state, ",", $location, ",", $type, ",", $status, ",", $distance, ",", $wx, ",", $wy, ",", $authority, ",", $nphotos . "," . $photo . "\n";
}
mysql_free_result($stmt);

/*
 * Get a representative photo for the location
 */
function get_representative_photo($state, $name)
{
    global $db;

    $stmt = mysql_query("
        select
            LP.file
        from
            r_location_photo LP
        where
            LP.location_state = '$state'
            and
            LP.location_name = '$name'
            and
            LP.status = 'Y'
            and
            LP.width > LP.height
        order by
            LP.seqno
        limit 1
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $photo = "";
    if ($row = mysql_fetch_array($stmt))
    {
        list($photo) = $row;
    }
    mysql_free_result($stmt);

    return $photo;
}

?>
