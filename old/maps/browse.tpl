<!-- BEGIN MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>{TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<style type="text/css">
div.markerTooltip, div.markerDetail {
    color: black;
    background-color: white;
    white-space: nowrap;
    margin: 0;
    padding: 2px 4px;
    border: 1px solid black;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="/lib/ajaxutil.js" type="text/javascript"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={GMAPKEY}"
    type="text/javascript"></script>
<script src="/lib/pdmarker.js" type="text/javascript"></script>
<script src="browse.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
var mode = "{MODE}";
function load()
{
    loadMap({INITIAL-WX}, {INITIAL-WY}, {INITIAL-SCALE});
}
//]]>
</script>
</head>
<body onload="load()" onunload="GUnload()">

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="mbcontent">

<h1>{TITLE}</h1>


<div id="map" style="float: left">
<noscript>
<div class="boxed">
<p>
This page uses Google Maps, which requires JavaScript. It appears that
JavaScript is disabled or not supported in this browser.
</p>
<p>
To view this map, enable JavaScript in your browser and reload the page.
</p>
</div>
</noscript>
</div>

<div id="position">
<span class="parameter">Longitude:</span><br/>
<div class="value" id="lon"></div><br/>
<span class="parameter">Latitude:</span><br/>
<div class="value" id="lat"></div><br/>
</div>

</div>

</body>
</html>
<!-- END MAIN -->
