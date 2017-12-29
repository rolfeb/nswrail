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
<li><a href=\"index.php\">General</a></li>
<li><a href=\"locations.php\">Locations</a></li>
<li><a href=\"photos.php\">Photographs</a></li>
<li><a href=\"operators.php\">Operators</a></li>
<li><a href=\"preservation.php\">Preservation</a></li>
<li><a href=\"maps.php\">Maps</a></li>
</ul>
</div>";

    return $html;
}

?>
