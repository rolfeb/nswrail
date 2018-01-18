<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<table class="clean">
<tr class="property">
    <td>Location</td>
    <td>Type</td>
    <td>Status</td>
    <td align="right">Length</td>
    <td align="right">Photos</td>
    <td align="right">Distance</td>
    <td>Between</td>
</tr>
<!-- BEGIN LINE-OR-TUNNEL -->
<!-- BEGIN LINE -->
<tr class="highlight">
    <td colspan="7"><a href="{LINE-URL}">{LINE-TEXT}</a></td>
</tr>
<!-- END LINE -->
<!-- BEGIN TUNNEL -->
<tr class="value">
    <td class="indented"><a href="{TUNNEL-URL}">{TUNNEL-TEXT}</a></td>
    <td>{TYPE}</td>
    <td>{STATUS}</td>
    <td align="right">{LENGTH}</td>
    <td align="right">{PHOTOS}</td>
    <td align="right">{DISTANCE}</td>
    <td>{BETWEEN}</td>
</tr>
<!-- END TUNNEL -->
<!-- END LINE-OR-TUNNEL -->

</table>
<!-- END CONTENT -->
