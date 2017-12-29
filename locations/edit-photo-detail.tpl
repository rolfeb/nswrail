<!-- BEGIN CONTENT -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;">
</div>

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

<!-- END CONTENT -->
