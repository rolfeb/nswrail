<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<p>
This page enables you to search for keywords that are contained in various
text information within this site.
</p>

<!-- BEGIN RESULTS -->
<h2>Results</h2>

<table class="clean">
<!-- BEGIN RESULTS-LINE -->
<tr>
<td style="white-space: nowrap;"><a href="{REF-URL}" {REF-REL}>{REF-TEXT}</a>
<!-- BEGIN CAMERA-ICON -->
<img src="/media/images/camera-icon.gif"/>
<!-- END CAMERA-ICON -->
</td>
<td>{CONTEXT}</td>
</tr>
<!-- END RESULTS-LINE -->
</table>

<!-- BEGIN WARNING -->
<p><b>{MSG}</b></p>
<!-- END WARNING -->
<br/><hr/><br/>
<!-- END RESULTS -->

<form method="post" action="text.php" enctype="multipart/form-data">
<table class="simple">
<tr>
    <td><b>Keywords:</b></td>
    <td>
        <input type="text" name="keywords" tabindex="1"  size="60" maxlength="60" />
        <select name="keywordjoin" tabindex="2">
            <option selected="selected" value="all">all of</option>
            <option value="any">any of</option>
        </select>
    </td>
</tr>
<tr>
    <td>
    <b>Match against:</b></td>
    <td>
        <input type="checkbox" name="matchlocnname" checked />
        Location names
        <br/>
        <input type="checkbox" name="matchlocndesc" checked />
        Location descriptions
        <br/>
        <input type="checkbox" name="matchphotos" checked />
        Photo descriptions
    </td>
</tr>
</table>
<p/>
<div>
<input type="reset" tabindex="11" name="Reset Form" value="Reset Form" />
<input type="submit" tabindex="12" name="Perform Search" value="Perform Search" />
<input type="hidden" name="searchmode" value="1"  />
</div>
</form>

<!-- END CONTENT -->
