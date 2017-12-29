<?php

/*
 * Display the menubar
 */
function menubar()
{
    $missing = "";
    $database = "";

    if (auth_priv_editor())
    {
        $missing = "<li><a href=\"missing.php\">Missing data</a></li>";
        $database = "<li><a href=\"rpt-no_location.php\">Missing X/Y</a></li>";
    }

    $html = "
<div class=\"menubar\">
<p>Menu</p>
<ul>
<li><a href=\"index.php\">Index</a></li>
<li><a href=\"timeline.php\">Timeline</a></li>
<li><a href=\"triangle.php\">Triangles</a></li>
<li><a href=\"tunnel.php\">Tunnels</a></li>
<li><a href=\"turntable.php\">Turntables</a></li>
$missing
$database
</ul>
</div>";

    return $html;
}

?>
