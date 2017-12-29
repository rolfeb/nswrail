<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE} [edit photo detail]</title>
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

<h1>{TITLE} [edit photos detail]</h1>

<form method="post" action="edit-photo-detail.php" enctype="multipart/form-data">
<table class="simple">
<tr>
    <td>{DATE} ({OWNER})</td>
</tr>
<tr valign="top">
    <td>
        <input type="hidden" name="caption_orig" value="{CAPTION}" />
        <textarea name="caption" rows="8" cols="80">{CAPTION}</textarea>
    </td>
    <td>
        <input type="checkbox" name="theme_box" {CHECKED_box}>signal box</input>
        <br />
        <input type="checkbox" name="theme_diagram" {CHECKED_diagram}>diagram</input>
        <br />
        <input type="checkbox" name="theme_safeworking" value="safeworking" {CHECKED_safeworking}>safeworking</input>
        <br />
        <input type="checkbox" name="theme_night" value="night" {CHECKED_night}>night</input>
        <br />
        <input type="checkbox" name="theme_turntable" value="turntable" {CHECKED_turntable}>turntable</input>
    </td>
</tr>
<tr>
    <td>
        <input type="submit" name="action" value="Cancel" />
        <input type="reset" name="reset" value="Reset" />
        <input type="submit" name="action" value="Save" />
    </td>
</tr>
</table>

<input type="hidden" name="state" value="{STATE}">
<input type="hidden" name="location" value="{LOCATION}">
<input type="hidden" name="seqno" value="{SEQNO}">
<input type="hidden" name="line" value="{LINE}">
</form>

<img src="{IMAGE-URL}">

</body>
<!-- END MAIN -->
</html>
