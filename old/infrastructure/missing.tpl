<!-- BEGIN MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>{TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="mbcontent">

<h1>{TITLE}</h1>

<table class="clean">
<tr class="property">
    <td>Location</td>
    <td align="center">Type</td>
    <td align="center">Status</td>
    <td align="center">Distance</td>
    <td align="center">Photos</td>
</tr>
<!-- BEGIN LINE-OR-LOCATION -->
<!-- BEGIN LINE -->
<tr class="value">
    <td colspan="5"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
</tr>
<!-- END LINE -->
<!-- BEGIN LOCATION -->
<tr class="value">
    <td class="indented"><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
    <td align="center">{TYPE}</td>
    <td align="center">{STATUS}</td>
    <td align="center">{DISTANCE}</td>
    <td align="center">{PHOTOS}</td>
</tr>
<!-- END LOCATION -->
<!-- END LINE-OR-LOCATION -->
</table>

</div>

</body>
</html>
<!-- END MAIN -->
