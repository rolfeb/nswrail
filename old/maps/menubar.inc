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
<li><a href=\"browse.php\">Browse NSW</a></li>
<li><a href=\"nsw-network.php\">NSW by year</a></li>
<li><a href=\"sydney-network.php\">Sydney by year</a></li>
<li><a href=\"newcastle-network.php\">Newcastle by year</a></li>
<li><a href=\"nsw-growth.php\">NSW growth</a></li>
<li><a href=\"sydney-growth.php\">Sydney growth</a></li>
<li><a href=\"newcastle-growth.php\">Newcastle growth</a></li>
<li><a href=\"nsw-1933.php\">NSW map (1933)</a></li>
<li><a href=\"images/syd-network1969.gif\">Sydney map (1969)</a></li>
<li><a href=\"sydney-1974.php\">Sydney map (1974)</a></li>
<li><a href=\"images/newcastle1950.gif\">Newcastle map (1950)</a></li>
<li><a href=\"turning-facilities.php\">NSW turning facilities</a></li>
</ul>
</div>";

    return $html;
}

?>
