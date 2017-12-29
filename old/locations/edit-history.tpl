<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE} [edit history]</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body class="edit-mode">

<!-- BEGIN CONTROLS -->
{TOP}
<!-- END CONTROLS -->

<h1>{TITLE} [edit history]</h1>

<form method="post" action="edit-history.php" enctype="multipart/form-data">

<table class="simple">
<tr>
    <td class="property"></td>
    <td class="property">Type</td>
    <td class="property">Date</td>
    <td class="property">Error</td>
    <td class="property">New Name</td>
    <td class="property">Notes</td>
</tr>
<!-- BEGIN LOCATION-HISTORY -->
<tr>
    <td class="property">Event {SEQ}:</td>
    <td>
        <select name="type{SEQ}">
            <!-- BEGIN TYPE-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END TYPE-OPTION -->
        </select>
        <input type="hidden" name="o_type{SEQ}" value="{TYPE}" />
    </td>
    <td>
        <input type="text" name="day{SEQ}" value="{DAY}" size="2" />
        <input type="hidden" name="o_day{SEQ}" value="{DAY}" />
        /
        <input type="text" name="month{SEQ}" value="{MONTH}" size="2" />
        <input type="hidden" name="o_month{SEQ}" value="{MONTH}" />
        /
        <input type="text" name="year{SEQ}" value="{YEAR}" size="4" />
        <input type="hidden" name="o_year{SEQ}" value="{YEAR}" />
    </td>
    <td>
        <select name="error{SEQ}">
            <!-- BEGIN ERROR-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END ERROR-OPTION -->
        </select>
        <input type="hidden" name="o_error{SEQ}" value="{ERROR}" />
    </td>
    <td>
        <input type="text" name="name{SEQ}" value="{NAME}" size="24" maxsize="128"/>
        <input type="hidden" name="o_name{SEQ}" value="{NAME}" />
    </td>
    <td>
        <input type="text" name="text{SEQ}" value="{TEXT}" size="32" maxsize="128"/>
        <input type="hidden" name="o_text{SEQ}" value="{TEXT}" />
    </td>
</tr>
<!-- END LOCATION-HISTORY -->
<tr>
    <td colspan="8" align="right">
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
