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

The following table lists the {TOPN} shortest-lived sections of line in
NSW:

<table class="clean">
<tr class="property">
  <td>Line</td>
  <td>Section</td>
  <td>Opened</td>
  <td>Closed</td>
  <td align="right">Lifetime</td>
  <td align="right">length</td>
</tr>
<!-- BEGIN SECTION -->
<tr>
  <td><a href="{URL}">{TEXT}</a></td>
  <td>{START} to {END}</td>
  <td align="right">{OPENED}</td>
  <td align="right">{CLOSED}</td>
  <td align="right">{YEARS}y&nbsp;{MONTHS}m</td>
  <td align="right">{LENGTH}&nbsp;km</td>
</tr>
<!-- END SECTION -->
</table>

</div>

</body>
</html>
<!-- END MAIN -->

