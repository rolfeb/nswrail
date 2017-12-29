<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/site.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="/c/js/overlib.js">
<!-- overLIB (c) Erik Bosrup -->
</script>
</head>
<body>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;">
</div>

<div id="page-content">

<!-- BEGIN CONTROLS -->
{PAGE-HEADER}
{PAGE-MENU}
<!-- END CONTROLS -->


<div id="navigate">
<!-- BEGIN NAV-PREV-ENABLED -->
<a href="{FIRST-URL}"><img src="/images/button-arrow-full-left.png" /></a>
<a href="{PREV-URL}"><img src="/images/button-arrow-left.png" /></a>
<!-- END NAV-PREV-ENABLED -->
<!-- BEGIN NAV-PREV-DISABLED -->
<img src="/images/button-arrow-full-left-ghosted.png" />
<img src="/images/button-arrow-left-ghosted.png" />
<!-- END NAV-PREV-DISABLED -->
<span>{SHEET-SEQ} of {SHEET-COUNT}</span>
<!-- BEGIN NAV-NEXT-ENABLED -->
<a href="{NEXT-URL}"><img src="/images/button-arrow-right.png" /></a>
<a href="{LAST-URL}"><img src="/images/button-arrow-full-right.png" /></a>
<!-- END NAV-NEXT-ENABLED -->
<!-- BEGIN NAV-NEXT-DISABLED -->
<img src="/images/button-arrow-right-ghosted.png" />
<img src="/images/button-arrow-full-right-ghosted.png" />
<!-- END NAV-NEXT-DISABLED -->
</div>

<h1>{TITLE} - map {SHEET-SEQ} of {SHEET-COUNT}</h1>

<!-- BEGIN SHEET -->
<div id="linemap">
<img src="{SHEET-URL}" usemap="#imap" alt="{TITLE} line map" />
</div>
<map name="#imap">
{IMAGEMAP}
</map>
<!-- END SHEET -->
(Click on a location for more details)

</div>
</body>
<!-- END MAIN -->
</html>
