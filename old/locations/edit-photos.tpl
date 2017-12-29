<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE} [edit photos]</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../lib/overlib.js">
<!-- overLIB (c) Erik Bosrup -->
</script>
</head>
<body class="edit-mode">

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;">
</div>

<!-- BEGIN CONTROLS -->
{TOP}
<!-- END CONTROLS -->

<h1>{TITLE} [edit photos]</h1>

<a href="{RETURN-URL}">Return from edit-mode</a>

<table class="simple" width="100%">
<!-- BEGIN ROW -->
<tr valign="top">
    <!-- BEGIN PHOTO -->
    <td width="25%" align="center">
        <table class="photo-ops">
            <tr height="17px">
                <td>
                    <!-- BEGIN UP-ARROW -->
                    <a href="{MOVE-UP-URL}"><img src="/images/button-arrow-up.png"></a>
                    <!-- END UP-ARROW -->
                </td>
                <td rowspan="5">
                    <img src="{THUMBNAIL}" id="{STATUS}" onmouseover="return overlib('{OVERLIB-TEXT}', HAUTO, WIDTH, 400);" onmouseout="nd();">
                    <br>
                    <span id="photo-date">{DATE}</span>
                </td>
                <td><a href="{DETAILS-URL}"><img src="/images/button-text.png"></a></td>
            </tr>
            <tr height="17px">
                <td>
                    <!-- BEGIN LEFT-ARROW -->
                    <a href="{MOVE-LEFT-URL}"><img src="/images/button-arrow-left.png"></a>
                    <!-- END LEFT-ARROW -->
                </td>
                <td><a href="{ENABLE-URL}"><img src="/images/button-enable.png"></a></td>
            </tr>
            <tr height="17px">
                <td>
                    <!-- BEGIN RIGHT-ARROW -->
                    <a href="{MOVE-RIGHT-URL}"><img src="/images/button-arrow-right.png"></a>
                    <!-- END RIGHT-ARROW -->
                </td>
                <td><a href="{DISABLE-URL}"><img src="/images/button-disable.png"></a></td>
            </tr>
            <tr height="17px">
                <td>
                    <!-- BEGIN DOWN-ARROW -->
                    <a href="{MOVE-DOWN-URL}"><img src="/images/button-arrow-down.png"></a>
                    <!-- END DOWN-ARROW -->
                </td>
                <td></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
    <!-- END PHOTO -->
</tr>
<!-- END ROW -->
</table>
<a href="{RETURN-URL}">Return from edit-mode</a>

</body>
<!-- END MAIN -->
</html>
