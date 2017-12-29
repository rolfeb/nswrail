<?php

require_once "../init.inc";
require_once "../util.inc";

$name = quote_external(get_post("name"));           /* mandatory */
$wx = quote_external(get_post("wx"));               /* mandatory */
$wy = quote_external(get_post("wy"));               /* mandatory */

list($state, $location) = explode(":", $name);

Header("Content-type: text/xml", 1);

update_location($state, $location, $wx, $wy);

function update_location($state, $location, $wx, $wy)
{
    global $db;

    $wx = sprintf("%.6f", $wx);
    $wy = sprintf("%.6f", $wy);

    if (!mysql_query("
        update
            r_location
        set
            geo_x = $wx,
            geo_y = $wy,
            geo_authority = 'google'
        where
            location_state = '$state'
            and
            location_name = '$location'
    ", $db))
    {
        rollback();
        $error = mysql_error() || "unknown error";
        echo("<update status=\"1\" message=\"$error\" />");
        return;
    }

    commit();
    echo("<update status=\"0\" message=\"\" />");
    return;
}

?>
