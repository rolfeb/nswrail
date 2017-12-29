<!-- BEGIN MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>{TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
td.line {
    width: 12em;
}

td.height {
    text-align: right;
    width: 6em;
}

td.location {
    width: 20em;
}
-->
</style>
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="mbcontent">

<h1>{TITLE}</h1>

This page details some of the highest and lowest points on the NSW network.

<h2>Highest Points</h2>

<table class="clean">
<tr class="property">
  <td class="line">Line</td>
  <td class="height">Height</td>
  <td class="location">Location</td>
</tr>
<!-- BEGIN HIGH -->
<tr>
  <td class="line"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
  <td class="height">{HEIGHT} m</td>
  <!-- BEGIN HIGH-LOCATION -->
  <td class="location">{LOCATION}</td>
  <!-- END HIGH-LOCATION -->
  <!-- BEGIN HIGH-STATION -->
  <td class="location"><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
  <!-- END HIGH-STATION -->
</tr>
<!-- END HIGH -->
</table>

<h2>Lowest Points</h2>

<table class="clean">
<tr class="property">
  <td class="line">Line</td>
  <td class="height">Height</td>
  <td class="location">Location</td>
</tr>
<!-- BEGIN LOW -->
<tr>
  <td class="line"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
  <td class="height">{HEIGHT} m</td>
  <!-- BEGIN LOW-LOCATION -->
  <td class="location">{LOCATION}</td>
  <!-- END LOW-LOCATION -->
  <!-- BEGIN LOW-STATION -->
  <td class="location"><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
  <!-- END LOW-STATION -->
</tr>
<!-- END LOW -->
</table>

</div>

</body>
</html>
<!-- END MAIN -->

