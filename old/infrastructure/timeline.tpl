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

This is a timeline of the opening and closing dates for sections
of lines within NSW.  I also have some
<a href="/trivia/track_changes.php">graphs</a>
of total track length over time.

<table class="clean">
<tr>
<td class="property" align="right">Date</td>
<td class="property">Line</td>
<td class="property">Event</td>
<td class="property">Section</td>
<td class="property" align="right">Length</td>
</tr>

<!-- BEGIN EVENT -->
<tr>
<td class="rjust">{DATE}</td>
<td>
    <!-- BEGIN LINE -->
    <a href="{URL}">{LINE}</a>
    <!-- END LINE -->
</td>
<td>{EVENT}</td>
<td>{SECTION}</td>
<td class="rjust">{KM} km</td>
</tr>
<!-- END EVENT -->
</table>

</div>

</body>
</html>
<!-- END MAIN -->
