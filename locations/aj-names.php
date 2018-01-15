<?php
/*
 * Send a JSON dump of the location data.
 */
require_once "site.inc";

$stmt = $db->stmt_init();
$stmt->prepare("
    select
        L.location_state,
        L.location_name
    from
        r_location L
")
    or dbi_error_trace("prepare failed");


$stmt->execute();
$stmt->bind_result($state, $name);


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
    print "    [ \"$state\", \"$name\" ],\n";
}
print "]\n";

$stmt->close();

?>
