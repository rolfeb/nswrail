<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<script type="text/javascript" src="../lib/overlib.js">
<!-- overLIB (c) Erik Bosrup -->
</script>
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;">
</div>

<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="mbcontent">

<h1>{TITLE}</h1>

<!-- BEGIN MAP -->
<img src="{IMAGE}" usemap="#imagemap" class="imagemap" alt="image map"></img>
<map name="imagemap" id="imagemap0">
{IMAGEMAP}
</map>
<!-- END MAP -->

<img src="images/bullet_green.png" />&nbsp;Open&nbsp;&nbsp;
<img src="images/bullet_red.png" />&nbsp;Closed&nbsp;&nbsp;
<img src="images/bullet_blue.png" />&nbsp;Tourist&nbsp;&nbsp;
<img src="images/bullet_grey.png" />&nbsp;Lifted&nbsp;&nbsp;
<img src="images/bullet_light_grey.png" />&nbsp;Uncompleted&nbsp;&nbsp;


</div>

</body>
<!-- END MAIN -->
</html>
