<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<table class="clean">
<tr class="property">
    <td>Location</td>
    <td align="center">Type</td>
    <td align="center">Status</td>
    <td align="center">Distance</td>
    <td align="center">Photos</td>
</tr>
<!-- BEGIN LINE-OR-LOCATION -->
<!-- BEGIN LINE -->
<tr class="value">
    <td colspan="5"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
</tr>
<!-- END LINE -->
<!-- BEGIN LOCATION -->
<tr class="value">
    <td class="indented"><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
    <td align="center">{TYPE}</td>
    <td align="center">{STATUS}</td>
    <td align="center">{DISTANCE}</td>
    <td align="center">{PHOTOS}</td>
</tr>
<!-- END LOCATION -->
<!-- END LINE-OR-LOCATION -->
</table>

<!-- END CONTENT -->
