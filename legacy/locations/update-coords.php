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

    $stmt = $db->stmt_init();
    $stmt->prepare("
        update
            r_location
        set
            geo_x = ?,
            geo_y = ?,
            geo_exact = 'Y'
        where
            location_state = ?
            and
            location_name = ?
    ")
        or dbi_error_trace("prepare failed");

    $stmt->bind_param("ddss", $wx, $wy, $state, $location);

    if (!$stmt->execute())
    {
        $db->rollback();
        $error = $db->error || "unknown error";
        echo("<update status=\"1\" message=\"$error\" />");
        return;
    }

    $db->commit();
    echo("<update status=\"0\" message=\"\" />");
    return;
}

?>
