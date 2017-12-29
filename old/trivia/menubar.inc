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
<li><a href=\"short_lived_sections.php\">Short-lived Sections</a></li>
<li><a href=\"never_completed.php\">Lines not Completed</a></li>
<li><a href=\"other_gauge.php\">Non-Standard Gauge</a></li>
<li><a href=\"spirals.php\">Railway Spirals</a></li>
<li><a href=\"altitude.php\">Highs and Lows</a></li>
<li><a href=\"formally_closed.php\">Formally Closed Lines</a></li>
<li><a href=\"closed_sydney_stations.php\">Closed Sydney Stations</a></li>
<li><a href=\"track_changes.php\">Track Length Changes</a></li>
</ul>
</div>";

    return $html;
}

?>
