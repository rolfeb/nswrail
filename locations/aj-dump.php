<?php
/*
 * Send a JSON dump of the location data.
 */
require_once "site.inc";

global $dbi;

$stmt = $dbi->stmt_init();
$stmt->prepare("
    select
        L.location_state,
        L.location_name,
        L.type,
        L.status,
        L.distance,
        L.geo_x,
        L.geo_y,
        L.geo_exact,
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
")
    or dbi_error_trace("prepare failed");

$stmt2 = $dbi->stmt_init();
$stmt2->prepare("
    select
        LEV.type,
        LEV.day,
        LEV.month,
        LEV.year,
        LEV.year_error,
        LEV.current_name
    from
        r_location_event LEV
    where
        LEV.location_state = ?
        and
        LEV.location_name = ?
        and
        LEV.type in ( 'ON', 'CN', 'OT', 'CT' )
    order by
        LEV.seqno desc
    limit
        1
")
    or dbi_error_trace("prepare failed");

$stmt3 = $dbi->stmt_init();
$stmt3->prepare("
    select
        LP.file
    from
        r_location_photo LP
    where
        LP.location_state = ?
        and
        LP.location_name = ?
        and
        LP.status = 'Y'
        and
        LP.released = 'Y'
        and
        LP.width > LP.height
    order by
        LP.year desc,
        LP.seqno
    limit
        1
")
    or dbi_error_trace("prepare failed");


$stmt->execute();
$stmt->bind_result($state, $name, $type, $status, $distance, $geo_x,
    $geo_y, $geo_exact, $nphotos);
$stmt->store_result();


/*
 * Send uncompressed output, as negotiated with the browser
 */
ob_start("ob_gzhandler");

/* 
 * Write out data in JSON format
 */
print "[\n";
while ($stmt->fetch())
{
    $stmt2->bind_param("ss", $state, $name);
    $stmt2->execute();
    $stmt2->bind_result($evtype, $day, $month, $year, $year_error, $current_name);
    #$stmt2->store_result();

    $stmt3->bind_param("ss", $state, $name);
    $stmt3->execute();
    $stmt3->bind_result($thumbnail);

    $type = locn_type2text($type);
    $status = locn_status2text($status);

    if ($stmt2->fetch())
    {
        if ($year > 0)
        {
            $date_str = date_cpts2text($day, $month, $year, $year_error);
            if ($evtype == 'ON' or $evtype == 'OT')
                $status = "Opened $date_str";
            else
                $status = "Closed $date_str";
        }
    }

    if (!$stmt3->fetch())
        $thumbnail = '';

    if ($distance)
        $distance = sprintf("%.3f", $distance);
    if ($geo_x)
        $geo_x = sprintf("%.6f", $geo_x);
    if ($geo_y)
        $geo_y = sprintf("%.6f", $geo_y);

    print "    [ \"$state\", \"$name\", \"$type\", \"$status\", \"$distance\", $geo_x, $geo_y, \"$geo_exact\", $nphotos, \"$thumbnail\" ],\n";
}
print "]\n";

$stmt->close();
$stmt2->close();
$stmt3->close();

?>
