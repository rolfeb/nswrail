<?php

require_once "util.inc";

$log = changelog("changes.dat");
$last = count($log) - 1;

$date = $log[$last]['date'];
$text = $log[$last]['text'];

print "<div id=\"date\">\n";
print "Last change: $date\n";
print "</div>\n";
print "<div id=\"log\">\n";
print "$text\n";
print "</div>\n";


?>
