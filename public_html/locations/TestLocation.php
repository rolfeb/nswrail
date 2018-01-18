<?php

require_once "../init.inc";
require_once "Location.php";

$l = new Location;

try {
    $type = $l->type;
}
catch (Exception $e)
{
    print "{$e->getFile()}[{$e->getLine()}]: {$e->getMessage()}\n";
};

if ($l->retrieve($db, "NSW", "Lithgow") != 0)
    die("retrieve method failed\n");

print "type=[$l->type], status=[$l->status]\n";


exit;

?>
