<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE} [edit]</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body class="edit-mode">

<!-- BEGIN CONTROLS -->
{TOP}
<!-- END CONTROLS -->

<h1>{TITLE} [edit]</h1>

<form method="post" action="edit.php" enctype="multipart/form-data">

<table class="simple">
<!-- BEGIN ADMIN-BLOCK1 -->
<tr>
    <td class="property">Name:</td>
    <td>
        <input type="text" name="fullname" value="{NAME}" size="32"/>
        <input type="hidden" name="o_fullname" value="{NAME}" />
    </td>
</tr>
<tr>
    <td class="property">Region:</td>
    <td>
        <select name="region">
            <!-- BEGIN REGION-OPTION -->
            <option {SELECTED}>{VALUE}</option>
            <!-- END REGION-OPTION -->
        </select>
        <input type="hidden" name="o_region" value="{REGION}" />
    </td>
</tr>
<tr>
    <td class="property">Traffic:</td>
    <td>
        <select name="traffic">
            <!-- BEGIN TRAFFIC-OPTION -->
            <option {SELECTED}>{VALUE}</option>
            <!-- END TRAFFIC-OPTION -->
        </select>
        <input type="hidden" name="o_traffic" value="{TRAFFIC}" />
    </td>
</tr>
<!-- END ADMIN-BLOCK1 -->
<tr>
    <td class="property">Description:</td>
    <td>
        <textarea name="desc" cols="80" rows="12">{DESC}</textarea>
        <input type="hidden" name="o_desc" value="{DESC}" />
    </td>
</tr>
<!-- BEGIN ADMIN-BLOCK2 -->
<!-- BEGIN LINE-URL -->
<tr>
    <td class="property">URL {SEQ}:</td>
    <td>
        <input type="text" name="text{SEQ}" value="{LINK-TEXT1}" size="40" />
        <input type="text" name="url{SEQ}" value="{LINK-URL1}" size="60" />
    </td>
</tr>
<!-- END LINE-URL -->
<!-- END ADMIN-BLOCK2 -->
<tr>
    <td colspan="2" align="right">
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
<input type="hidden" name="line" value="{LINE}" />
<input type="hidden" name="version" value="{VERSION}" />
</form>

</body>
<!-- END MAIN -->
</html>
