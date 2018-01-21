<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<table class="table table-sm table-responsive-md">
<thead class="thead-dark">
<tr>
    <th colspan="2">Location</th>
    <th>Type</th>
    <th>Status</th>
    <th align="right">Length</th>
    <th align="right">Photos</th>
    <th align="right">Distance</th>
    <th>Between</th>
</tr>
</thead>
<!-- BEGIN LINE-OR-TUNNEL -->
<!-- BEGIN LINE -->
<tr class="table-info">
    <td colspan="8">{LINE-TEXT}</td>
</tr>
<!-- END LINE -->
<!-- BEGIN TUNNEL -->
<tr>
    <td>&nbsp;</td>
    <td><a href="{TUNNEL-URL}">{TUNNEL-TEXT}</a></td>
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
