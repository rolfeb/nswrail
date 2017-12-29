<ul>

<?php

require_once "init.inc";
require_once "util.inc";


$stmt = mysql_query("
    select
        state,
        name,
        seqno,
        file
    from
        r_random_photos
    order by
        id
    limit 3
", $db);
if (!$stmt)
    die("prepare failed: " . mysql_error() . "\n");

while ($row = mysql_fetch_array($stmt))
{
    list($state, $name, $seqno, $file) = $row;

    $url = "/locations/photo.php?" . urlenc("name=$state:$name:$seqno");
    $img = "/locations/photos/small/$file";

    print "<li><a href=\"$url\"><img src=\"$img\" alt=\"\"></img></a></li>\n";
};

mysql_free_result($stmt);

?>

</ul>
