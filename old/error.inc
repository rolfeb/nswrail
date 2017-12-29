<?php

function error_page($message, $url)
{
    print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
         \"http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en-US\" xml:lang=\"en-US\">
<head>
<title>Error</title>
<link type=\"image/gif\" rel=\"shortcut icon\" href=\"/images/railicon.gif\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"/common.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"local.css\" />
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
</head>
<body>

<div align=\"center\">

<div class=\"message\">
$message
<p>
<a href=\"$url\">Continue</a>
</div>
</div>

</body>
</html>\n";

    exit;
}

?>
