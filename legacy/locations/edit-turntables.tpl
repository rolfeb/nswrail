<!-- BEGIN CONTENT -->
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

<!-- END CONTENT -->
