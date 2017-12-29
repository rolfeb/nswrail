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
<li><a href=\"index.php\">This site</a></li>
<li><a href=\"changelog.php\">Change Log</a></li>
<li><a href=\"credits.php\">Credits</a></li>
</ul>
</div>";

    return $html;
}

?>
