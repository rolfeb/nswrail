<?php
/**
 * Copyright (c) 2019 Rolfe Bozier
 */

require "site.inc";

function dump_json()
{
    global $db;

    $stmt1 = $db->stmt_init();
    $stmt1->prepare("
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
                LP.hold is null
        where
            L.geo_x is not null
            and
            L.geo_y is not null
        group by
            L.location_state,
            L.location_name
    ");

    $stmt2 = $db->stmt_init();
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
    ");

    $stmt3 = $db->stmt_init();
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
            LP.hold is null
            and
            LP.width > LP.height
        order by
            LP.year desc,
            LP.seqno
        limit
            1
    ");

    $stmt1->execute();
    $stmt1->bind_result($state, $name, $type, $status, $distance, $geo_x,
        $geo_y, $geo_exact, $nphotos);
    $stmt1->store_result();

    $locations = array();
    while ($stmt1->fetch()) {
        if ($distance)
            $distance = sprintf("%.3f", $distance);
        if ($geo_x)
            $geo_x = sprintf("%.6f", $geo_x);
        if ($geo_y)
            $geo_y = sprintf("%.6f", $geo_y);
        $type = locn_type2text($type);
        $status = locn_status2text($status);

        $item = array(
            'state' => $state,
            'name' => $name,
            'type' => $type,
            'status' => $status,
            'distance' => $distance,
            'geo_x' => $geo_x,
            'geo_y' => $geo_y,
            'geo_exact' => $geo_exact,
            'nphotos' => $nphotos,
        );
        $locations[] = $item;
    }
    $stmt1->close();

    foreach ($locations as &$item) {
        /* get the most recent event for the location, if there is one */
        $stmt2->bind_param("ss", $item['state'], $item['name']);
        $stmt2->execute();
        $stmt2->bind_result($evtype, $day, $month, $year, $year_error, $current_name);

        if ($stmt2->fetch()) {
            if ($year > 0) {
                $date_str = date_cpts2text($day, $month, $year, $year_error);
                if ($evtype == 'ON' or $evtype == 'OT')
                    $item['status'] = "Opened $date_str";
                else
                    $item['status'] = "Closed $date_str";
            }
        }
    }
    $stmt2->close();

    foreach ($locations as &$item) {
        /* find a representative phoptograph for the location */
        $stmt3->bind_param("ss", $item['state'], $item['name']);
        $stmt3->execute();
        $stmt3->bind_result($thumbnail);

        if (!$stmt3->fetch())
            $thumbnail = '';
        $item['thumbnail'] = $thumbnail;

    }
    $stmt3->close();

    /*
     * Send uncompressed output, as negotiated with the browser
     */
    ob_start("ob_gzhandler");

    /*
     * Write out data in JSON format
     */
    print(json_encode($locations));
    /*
    print "[\n";
    foreach ($locations as $item) {
        $state = $item['state'];
        $name = $item['name'];
        $type = $item['type'];
        $status = $item['status'];
        $distance = $item['distance'];
        $geo_x = $item['geo_x'];
        $geo_y = $item['geo_y'];
        $geo_exact = $item['geo_exact'];
        $nphotos = $item['nphotos'];
        $thumbnail = $item['thumbnail'];

        print "    [ \"$state\", \"$name\", \"$type\", \"$status\", \"$distance\", $geo_x, $geo_y, \"$geo_exact\", $nphotos, \"$thumbnail\" ],\n";
    }
    print "]\n";
    */
}

try {
    dump_json();

} catch (\Exception $e) {
    report_error($e);
}
