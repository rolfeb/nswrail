<?php

require_once "dbutil.inc";

/*
 * Display the menubar
 */
function menubar($state = "", $line = "", $mode = "")
{
    $break1 = "";
    if ($state != "" and $line != "")
        $break1 = "<br />";

    $missing = "";
    /* if (auth_priv_admin()) */
        $missing = "<li><a href=\"missing.php?name=$state:$line\">Missing Data</a></li>";

    $maps = "";
    $num_maps = get_linemap_count($state, $line);
    if ($num_maps > 0)
        $maps = "<li><a href=\"linemap.php?name=$state:$line:1\">Line Maps</a></li>";


    $html = "
<div class=\"menubar\">
<p>Menu</p>
<ul>
<li><a href=\"/lines/nsw-map.php\">NSW map</a></li>
<li><a href=\"/lines/sydney-map.php\">Sydney map</a></li>
<li><a href=\"/lines/newcastle-map.php\">Newcastle map</a></li>
<li><a href=\"/lines/nsw-lines.php\">NSW lines</a></li>
<li><a href=\"/lines/sydney-lines.php\">Sydney lines</a></li>
<li><a href=\"/lines/newcastle-lines.php\">Newcastle lines</a>$break1</li>
";

    if ($state != "" and $line != "")
    {
        $html .= "
<li><a href=\"/lines/show.php?name=$state:$line\">Description</a></li>
<li><a href=\"/lines/show.php?name=$state:$line&amp;mode=history\">History</a></li>
$maps
$missing
        ";
    }

    $html .= "
</ul>
</div>";

    return $html;
}

?>
