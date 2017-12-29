<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE} [edit turntables]</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body class="edit-mode">

<!-- BEGIN CONTROLS -->
{TOP}
<!-- END CONTROLS -->

<h1>{TITLE} [edit turntables]</h1>

<form method="post" action="edit-turntables.php" enctype="multipart/form-data">
<table class="simple">
<tr>
    <td class="property"></td>
    <td class="property">Size</td>
    <td class="property">Type</td>
    <td class="property">Status</td>
    <td class="property">Notes</td>
</tr>
<!-- BEGIN LOCATION-TURNTABLE -->
<tr valign="top">
    <td class="property">{SEQ}:</td>
    <td>
        <select name="size{SEQ}">
            <!-- BEGIN SIZE-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END SIZE-OPTION -->
        </select>
        <input type="hidden" name="o_size{SEQ}" value="{SIZE}" />
    </td>
    <td>
        <select name="type{SEQ}">
            <!-- BEGIN TYPE-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END TYPE-OPTION -->
        </select>
        <input type="hidden" name="o_type{SEQ}" value="{TYPE}" />
    </td>
    <td>
        <select name="status{SEQ}">
            <!-- BEGIN STATUS-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END STATUS-OPTION -->
        </select>
        <input type="hidden" name="o_status{SEQ}" value="{STATUS}" />
    </td>
    <td>
        <select name="sellers{SEQ}">
            <!-- BEGIN SELLERS-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END SELLERS-OPTION -->
        </select>
        <input type="hidden" name="o_sellers{SEQ}" value="{SELLERS}" />
    </td>
    <td>
        <textarea name="text{SEQ}" cols="60" rows="5">{TEXT}</textarea>
        <input type="hidden" name="o_text{SEQ}" value="{TEXT}" />
    </td>
</tr>
<!-- END LOCATION-TURNTABLE -->
<tr>
    <td colspan="5" align="right">
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
