<!-- BEGIN CONTENT -->
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

<!-- END CONTENT -->
