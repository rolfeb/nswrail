<!-- BEGIN MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>{TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
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

<!-- BEGIN RESULTS -->

<!-- BEGIN LINE -->
<h2>{LINE}</h2>
<ul>
<!-- BEGIN LOCATION -->
<li><a href="{URL}">{NAME}</a> ({COUNT})</li>
<!-- END LOCATION -->
</ul>
<!-- END LINE -->

<!-- END RESULTS -->

<!-- BEGIN ERROR -->
<div class="message" align="center">
{MESSAGE}
</div>
<!-- END ERROR -->

</div>
</body>
</html>
<!-- END MAIN -->
