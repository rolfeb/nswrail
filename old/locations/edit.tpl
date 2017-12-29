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
    <td class="property">Type:</td>
    <td>
        <select name="type">
            <!-- BEGIN TYPE-OPTION -->
            <option {SELECTED}>{VALUE}</option>
            <!-- END TYPE-OPTION -->
        </select>
        <input type="hidden" name="o_type" value="{TYPE}" />
    </td>
</tr>
<tr>
    <td class="property">Status:</td>
    <td>
        <select name="status">
            <!-- BEGIN STATUS-OPTION -->
            <option {SELECTED}>{VALUE}</option>
            <!-- END STATUS-OPTION -->
        </select>
        <input type="hidden" name="o_status" value="{STATUS}" />
    </td>
</tr>
<tr>
    <td class="property">Distance:</td>
    <td>
        <input type="text" name="distance" value="{DISTANCE}" size="8" />
        <input type="hidden" name="o_distance" value="{DISTANCE}" />
    </td>
</tr>
<tr>
    <td class="property">Geo X/Y:</td>
    <td>
        (
        <input type="text" name="geox" value="{GEOX}" size="10" />
        ,
        <input type="text" name="geoy" value="{GEOY}" size="10" />
        )
        <input type="hidden" name="o_geox" value="{GEOX}" />
        <input type="hidden" name="o_geoy" value="{GEOY}" />
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
<tr>
    <td class="property">Current status:</td>
    <td>
        <textarea name="curr" cols="80" rows="12">{CURR}</textarea>
        <input type="hidden" name="o_curr" value="{CURR}" />
    </td>
</tr>
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
<input type="hidden" name="location" value="{LOCATION}" />
<input type="hidden" name="version" value="{VERSION}" />

</form>

</body>
<!-- END MAIN -->
</html>
