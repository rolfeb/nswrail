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
    <td>Type</td>
    <td>Status</td>
    <td align="right">Length</td>
    <td align="right">Photos</td>
    <td align="right">Distance</td>
    <td>Between</td>
</tr>
<!-- BEGIN LINE-OR-TUNNEL -->
<!-- BEGIN LINE -->
<tr class="highlight">
    <td colspan="7"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
</tr>
<!-- END LINE -->
<!-- BEGIN TUNNEL -->
<tr class="value">
    <td class="indented"><a href="{TUNNEL-URL}">{TUNNEL-TEXT}</a></td>
    <td>{TYPE}</td>
    <td>{STATUS}</td>
    <td align="right">{LENGTH}</td>
    <td align="right">{PHOTOS}</td>
    <td align="right">{DISTANCE}</td>
    <td>{BETWEEN}</td>
</tr>
<!-- END TUNNEL -->
<!-- END LINE-OR-TUNNEL -->

</table>

</div>

</body>
</html>
<!-- END MAIN -->
