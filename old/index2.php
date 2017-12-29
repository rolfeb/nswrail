<?php require_once "init.inc"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>NSWrail.net</title>
<link rev="made" href="mailto:nswrail@pobox.com" />
<meta name="keywords" content="NSW railway history photographs maps" />
<link type="image/gif" rel="shortcut icon" href="images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="common.css" />
<link rel="stylesheet" type="text/css" href="main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php print top(); ?>
<?php require "navbar.inc"; ?>

<div id="main">
<div id="column2">
    <?php require "randompics.inc"; ?>
</div>

<div id="column1">
    <?php require "welcome.inc"; ?>
    <?php require "status.inc"; ?>
</div>

<div id="footer">
    <?php require "copyright.inc"; ?>
</div>

</div>

</body>
</html>
