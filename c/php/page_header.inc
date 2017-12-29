<?php

function page_header($nologin = false)
{
    $html = "
<table class=\"simple\" width=\"100%\" id=\"top\">
<tr>
<td align=\"left\"><img src=\"/images/nswrail-logo-medium.png\" alt=\"\"></img></td>
<td align=\"right\">
<div class=\"search\">
<form action=\"/locations/search.php\" method=\"get\">
<div>
<h2>Location Search:</h2>
<input type=\"text\" name=\"search\" size=\"20\"></input></div>
</form>
</div>
";

    if (!$nologin)
    {
/*
        $html .= "<div id=\"login\">";

        $url = $_SERVER["REQUEST_URI"];

        if (auth_loggedin())
        {
            $user = auth_username();
            $html .= "Hello, $user!&nbsp;&nbsp;&nbsp;";
            $html .= "<a href=\"/logout.php?redirect=$url\">logout</a>\n";
        }
        else
        {
            $html .= "Hello, guest!&nbsp;&nbsp;&nbsp;";
            $html .= "<a href=\"/login.php?redirect=$url\">login</a>\n";
        }

        $html .= "</div>";
*/
    }

    $html .= "
</td>
</tr>
</table>\n";

    return $html;
}

?>
