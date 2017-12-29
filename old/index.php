<?php require_once "init.inc"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>NSWrail.net</title>
<link rev="made" href="mailto:nswrail@pobox.com" />
<meta name="keywords" content="NSW railway history photographs maps" />
<link type="image/gif" rel="shortcut icon" href="images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="common.css" />
<link rel="stylesheet" type="text/css" href="main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/lib/menu.js"></script>
</head>
<body onload="fixmenu()">

<div id="banner">
</div>

<?php print menu(); ?>

<div id="main">
    <div id="column_left">
        <div id="quick">
            <?php require "quick.inc"; ?>
        </div>
    </div>

    <div id="column_right">

        <div id="status">
            <?php require "status.inc"; ?>
        </div>

    </div>

    <div id="column_centre">
        <?php require "welcome.inc"; ?>

        <div id="randompics">
            <?php require "randompics.inc"; ?>
        </div>

        <?php
            /*
            if (auth_priv_admin())
                require "googlesearch.inc";
            */
        ?>


    </div>

    <div id="footer">
    </div>

</div>

</body>
</html>
