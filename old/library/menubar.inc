<?php

/*
 * Display the menubar
 */
function menubar()
{
    $html = "
<div class=\"menubar\">
<p>Menu</p>
<ul>
<li><a href=\"index.php\">Index</a></li>
<li><a href=\"planned.php\">Planned lines</a></li>
<li><a href=\"station_names.php\">Station Names</a></li>
<li><a href=\"bulletin.php\">ARHS Bulletin index</a></li>
<li><a href=\"signalling.php\">Signalling</a></li>
<li><a href=\"smr.php\">South Maitland Railway</a></li>
<li><a href=\"widemere.php\">Widemere Quarry Line</a></li>
<li><a href=\"lithgow-zigzag.php\">Lithgow Zig Zag</a></li>
</ul>
</div>";

    return $html;
}

?>
