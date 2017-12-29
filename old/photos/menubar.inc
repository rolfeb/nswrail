<?php

function menubar()
{
    $pending = "";

    if (auth_priv_admin())
    {
        $incoming = "<li><a href=\"incoming.php\">Incoming</a></li>";
    }

    $html = "
<div class=\"menubar\">
<p>Menu</p>
<ul>
<li><a href=\"index.php\">NSW photos</a></li>
<li><a href=\"recent.php\">Newly added</a></li>
<li><a href=\"historic.php\">Historic</a></li>
<li><a href=\"signal_box.php\">Signal boxes</a></li>
<li><a href=\"safeworking.php\">Safeworking</a></li>
<li><a href=\"diagram.php\">Diagrams</a></li>
<li><a href=\"turntable.php\">Turntables</a></li>
<li><a href=\"tunnel.php\">Tunnels</a></li>
<li><a href=\"night.php\">Night</a></li>
<li><a href=\"submissions.php\">Submissions</a></li>
$incoming
</ul>
</div>";

    return $html;
}

?>
