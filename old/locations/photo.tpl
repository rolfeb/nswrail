<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE}</title>
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

<div class="content">

<div class="navigate">
<!-- BEGIN NAV-PREV-ENABLED -->
<a href="{FIRST-URL}"><img src="/images/button-arrow-full-left.png" alt="start" /></a>
<a href="{PREV-URL}"><img src="/images/button-arrow-left.png" alt="previous" /></a>
<!-- END NAV-PREV-ENABLED -->
<!-- BEGIN NAV-PREV-DISABLED -->
<img src="/images/button-arrow-full-left-ghosted.png" alt="first photo" />
<img src="/images/button-arrow-left-ghosted.png" alt="previous photo" />
<!-- END NAV-PREV-DISABLED -->
<span>{PHOTO-SEQ} of {PHOTO-COUNT}</span>
<!-- BEGIN NAV-NEXT-ENABLED -->
<a href="{NEXT-URL}"><img src="/images/button-arrow-right.png" alt="next photo" /></a>
<!-- END NAV-NEXT-ENABLED -->
<!-- BEGIN NAV-NEXT-DISABLED -->
<img src="/images/button-arrow-right-ghosted.png" alt="last photo" />
<!-- END NAV-NEXT-DISABLED -->
</div>

<h1>{TITLE}</h1>

<div>
{TEXT} ({DATE})
<br/>
[Copyright: {OWNER}
<!-- BEGIN OWNER-URL-BLOCK -->
(<a href="{OWNER-URL}">link</a>)
<!-- END OWNER-URL-BLOCK -->
]
</div>

<br/>
<img src="{IMAGE}" alt="{IMG-ALT-TEXT}" />
<br/>
[back to <a href="{LOCATION-URL}">{LOCATION-TEXT}</a>]

</div>
</body>
<!-- END MAIN -->
</html>
