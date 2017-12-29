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

<!-- BEGIN YEAR-FORM -->
<form id="year-select" action="{URL}" method="get">
<div>
<select name="year" onchange="document.getElementById('year-select').submit();">
<!-- BEGIN OPTION-YEAR -->
<option {SELECTED}>{YEAR}</option>
<!-- END OPTION-YEAR -->
</select>
<input type="submit" value="go" />
</div>
</form>
<!-- END YEAR-FORM -->

<!-- BEGIN MAP -->
<img class="map" src="{IMAGE}" alt="" />
<!-- END MAP -->

<p/>
<ul>
<li>
Key to lines status:
<span class="inuse">in use</span>,
<span class="outofuse">out of use</span>,
<span class="tourist">tourist</span>,
<span class="lifted">lifted</span>.
Dotted lines were never completed.
</li>
<li><b>Bold</b> lines changed status in the last 5 years. </li>
</ul>

</div>

</body>
</html>
<!-- END MAIN -->
