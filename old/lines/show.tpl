<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {LINE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="{LINE}: history, description, information about all stations and facilities along the line" />
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<h1>{LINE}</h1>

<div class="hlinks">
<ul>
<li><a href="{MAIN-URL}">Description</a></li>
<li><a href="{HISTORY-URL}">History</a></li>
<li><a href="{LINEMAP-URL}">Line Maps</a></li>
</ul>
</div>

<div class="mbcontent">

<!-- BEGIN DESCRIPTION-MODE -->
{DESCRIPTION}

<!-- BEGIN LINK-SECTION -->
<ul class="links">
<!-- BEGIN LINK-URL -->
<li><a href="{URL}">{TEXT}</a></li>
<!-- END LINK-URL -->
</ul>
<!-- END LINK-SECTION -->
<!-- BEGIN EDIT-BLOCK -->
Edit:
[<a href="{EDIT-URL}">details</a>]
<!-- END EDIT-BLOCK -->

<table class="clean" id="locations">
<tr class="property">
<!-- BEGIN ICON-HEADING -->
<td class="icon"></td>
<!-- END ICON-HEADING -->
<td class="name">Name</td>
<td class="facility">Facility</td>
<td class="status">Status</td>
<td class="opened">Opened</td>
<td class="closed">Closed</td>
<td class="distance">km</td>
<td class="photos"><img src="/images/camera-icon.gif" alt="Photos" /></td>
<td class="links">Links</td>
</tr>
<!-- BEGIN TABLE-ENTRY -->
<!-- BEGIN LOCATION -->
<tr class="value">
<!-- BEGIN ICON-DATA -->
<td class="icon">
<!-- BEGIN ICON-DATA-FILLED -->
<img src="{ICON}" alt="icon"></img>
<!-- END ICON-DATA-FILLED -->
</td>
<!-- END ICON-DATA -->
<td class="name"><span><a href="{URL}">{NAME}</a></span></td>
<td class="facility"><span>{FACILITY}</span></td>
<td class="status"><span>{STATUS}</span></td>
<td class="opened"><span>{OPENED}</span></td>
<td class="closed"><span>{CLOSED}</span></td>
<td class="distance"><span>{LOCATION}</span></td>
<td class="photos"><span>{PHOTOS}</span></td>
<td class="links"><span>{LINKS}</span></td>
</tr>
<!-- END LOCATION -->
<!-- BEGIN SEGMENT -->
<tr>
<td class="heading" colspan="8"><h2>{SEGMENT-TEXT}</h2></td>
</tr>
<!-- END SEGMENT -->
<!-- END TABLE-ENTRY -->
</table>
<!-- END DESCRIPTION-MODE -->

<!-- BEGIN HISTORY-MODE -->
<table class="clean">
<tr class="property">
<td>Section</td>
<td class="date-footnote">Opened</td>
<td class="footnote"></td>
<td class="date-footnote">Closed</td>
<td class="footnote"></td>
<td>Usage</td>
</tr>
<!-- BEGIN SEGMENT2-OR-SECTION -->
<!-- BEGIN SEGMENT2 -->
<tr class="value">
<td class="heading" colspan="6"><h2>{SEGMENT-TEXT}</h2></td>
</tr>
<!-- END SEGMENT2 -->
<!-- BEGIN SECTION -->
<tr class="value">
<td>{SECTION}</td>
<td class="date-footnote">{OPENED}</td>
<td class="footnote"><sup>&nbsp;{OPENFN}</sup></td>
<td class="date-footnote">{CLOSED}</td>
<td class="footnote"><sup>&nbsp;{CLOSEFN}</sup></td>
<td>{USAGE}</td>
</tr>
<!-- END SECTION -->
<!-- END SEGMENT2-OR-SECTION -->
</table>
<!-- BEGIN FOOTNOTES -->
<sup>{XREF-SEQ}</sup> {XREF-TEXT}<br />
<!-- END FOOTNOTES -->

<!-- END HISTORY-MODE -->

</div>

</body>
<!-- END MAIN -->
</html>
