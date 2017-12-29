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

<p>
This page lists the turntables which have been used in NSW.  With the demise
of steam locomotives turntables have little use, and hence most of the
following have been removed or are no longer functional.
</p>
<p>
The "status" values listed have the following meanings:
</p>

<table class="clean">
<tr>
    <td>In use</td>
    <td>Sees occasional use, for example when turning heritage steam
        locos.</td>
</tr>
<tr>
    <td>Out of use</td>
    <td>Currently booked out of use.</td>
</tr>
<tr>
    <td>Closed</td>
    <td>No longer used, but still in a reasonably good condition.</td>
</tr>
<tr>
    <td>Derelict</td>
    <td>No longer capable of being used, with some damage or no longer
        connected to the main line.</td>
</tr>
<tr>
    <td>Ruins</td>
    <td>Only the pit or foundations remain.</td>
</tr>
<tr>
    <td>No trace</td>
    <td>It is no longer possible to see where the turntable once resided.</td>
</tr>
</table>

<p>
The "type" values have the following meanings:
</p>

<table class="clean">
<tr>
    <td align="center">M</td>
    <td>Turntable is manually operated</td>
</tr>
<tr>
    <td>E</td>
    <td>Turntable is electrically-driven</td>
</tr>
</table>

<p>
For the metrically minded, equivalent sizes are:
</p>

<table class="clean">
<tr> <td align="right">50'</td> <td>= 15 metres</td> </tr>
<tr> <td align="right">60'</td> <td>= 18 metres</td> </tr>
<tr> <td align="right">75'</td> <td>= 23 metres</td> </tr>
<tr> <td align="right">90'</td> <td>= 27 metres</td> </tr>
<tr> <td align="right">105'</td> <td>= 32 metres</td> </tr>
</table>

<table class="clean">
<!-- BEGIN TABLE-ROW -->
<!-- BEGIN REGION -->
<tr>
  <td class="heading" colspan="6"><h2>{REGION}</h2></td>
</tr>
<tr class="property">
  <td>Location</td>
  <td align="right">Size</td>
  <td align="right">Type</td>
  <td>Status</td>
  <td>Notes</td>
  <td><img src="/images/camera-icon.gif" alt="" /></td>
</tr>
<!-- END REGION -->

<!-- BEGIN LINE -->
<tr>
  <td colspan="6"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
</tr>
<!-- END LINE -->

<!-- BEGIN LOCATION -->
<tr>
  <td class="indented"><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
  <td align="right">{SIZE}</td>
  <td align="center">{TYPE}</td>
  <td>{STATUS}</td>
  <td>{NOTES}</td>
  <td align="right">{PHOTOS}</td>
</tr>
<!-- END LOCATION -->
<!-- END TABLE-ROW -->
</table>
</div>

</body>
</html>
<!-- END MAIN -->
