<?php

/*
 * Display the menubar
 */
function menubar($state = "", $line = "", $mode = "")
{
    $html = "
<div class=\"menubar\">
<p>Menu</p>
<ul>
<li><a href=\"/lines/nsw-map.html\">NSW map</a></li>
<li><a href=\"/lines/sydney-map.html\">Sydney map</a></li>
<li><a href=\"/lines/newcastle-map.html\">Newcastle map</a></li>
<li><a href=\"/lines/nsw-lines.html\">NSW lines</a></li>
<li><a href=\"/lines/sydney-lines.html\">Sydney lines</a></li>
<li><a href=\"/lines/newcastle-lines.html\">Newcastle lines</a></li>
";

    if ($state != "" and $line != "")
    {
        $html .= "
<br>
<li><a href=\"/lines/show.html?state=$state&line=$line\">Description</a></li>
<li><a href=\"/lines/show.html?state=$state&line=$line&mode=history\">History</a></li>
<li><a href=\"/lines/show.html?state=$state&line=$line&mode=maps\">Maps</a></li>
        ";
    }

    $html .= "
</ul>
</div>";

    return $html;
}

?>
