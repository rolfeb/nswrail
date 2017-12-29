<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="content">

<h1>{TITLE}</h1>

<!-- BEGIN TRUNCATED-WARNING -->
<p>
<b>Too many matches - results truncated.</b>
</p>
<!-- END TRUNCATED-WARNING -->

<table class="clean">
<tr class="property">
    <td>Location</td>
    <td>Line</td>
</tr>
<!-- BEGIN RESULT -->
<tr class="value">
    <td><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
    <td><a href="{LINE-URL}">{LINE-TEXT}</a></td>
</tr>
<!-- END RESULT -->
</table>

<h2>Search:</h2>
<form action="/locations/search.php" method="get">
<input name="search" cols="20">
</form>

</div>
</body>
<!-- END MAIN -->
</html>
