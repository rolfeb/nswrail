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

Quick Index:
<!-- BEGIN INDEX -->
<a href="#{LETTER}">{LETTER}</a>
<!-- END INDEX -->

<table class="clean">
<tr class="property">
    <td>Location</td>
    <td>Type</td>
    <td>Status</td>
    <td align="right">Distance</td>
    <td>Line</td>
</tr>
<!-- BEGIN LOCATION -->
<tr class="{CLASS}">

<!-- BEGIN LOCATION-LINK-DEST -->
    <td><a name="{INDEX-TARGET}" href="{LOCATION-URL}">{LOCATION}</a></td>
<!-- END LOCATION-LINK-DEST -->
<!-- BEGIN LOCATION-NONLINK-DEST -->
    <td><a href="{LOCATION-URL}">{LOCATION}</a></td>
<!-- END LOCATION-NONLINK-DEST -->
    <td>{TYPE}</td>
    <td>{STATUS}</td>
    <td align="right">{DISTANCE}</td>
    <td><a href="{LINE-URL}">{LINE}</a></td>
</tr>
<!-- END LOCATION -->

</table>

</div>

</body>
</html>
<!-- END MAIN -->
