<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<table class="clean">
<tr class="property">
    <td class="location">Location</td>
    <td class="distance">Distance</td>
    <td class="longitude">Longitude</td>
    <td class="latitude">Latitude</td>
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
    <td class="distance">{DISTANCE}</td>
    <td class="longitude"></td>
    <td class="latitude"></td>
</tr>
<!-- END LOCATION -->
<!-- END LINE-OR-LOCATION -->
</table>

<!-- END CONTENT -->
