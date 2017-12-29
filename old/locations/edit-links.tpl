<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE} [edit links]</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body class="edit-mode">

<!-- BEGIN CONTROLS -->
{TOP}
<!-- END CONTROLS -->

<h1>{TITLE} [edit links]</h1>

<form method="post" action="edit-links.php" enctype="multipart/form-data">
<table class="simple">
<tr>
    <td class="property"></td>
    <td class="property">URL</td>
    <td class="property">Text</td>
</tr>
<!-- BEGIN LOCATION-URL -->
<tr>
    <td class="property">URL {SEQ}:</td>
    <td><input type="text" name="url{SEQ}" value="{LINK-URL}" size="50" /></td>
    <td><input type="text" name="text{SEQ}" value="{LINK-TEXT}" size="40" /></td>
</tr>
<!-- END LOCATION-URL -->
<tr>
    <td colspan="3" align="right">
        <br/>
        <input type="submit" name="action" value="Cancel" />
        <input type="reset" value="Reset" />
        <input type="submit" name="action" value="Save" />
    </td>
</tr>
</table>
<input type="hidden" name="mode" value="submit" />
<input type="hidden" name="return-url" value="{RETURN-URL}" />
<input type="hidden" name="state" value="{STATE}" />
<input type="hidden" name="location" value="{LOCATION}" />
<input type="hidden" name="version" value="{VERSION}" />

</form>

</body>
<!-- END MAIN -->
</html>
