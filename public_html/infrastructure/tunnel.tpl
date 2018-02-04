<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<table class="table table-sm">
<thead class="thead-dark">
<tr>
    <th colspan="2">Location</th>
    <th>Type</th>
    <th>Status</th>
    <th style="text-align: right;">Length</th>
    <th style="text-align: right;">
      <span class="material-icons" style="vertical-align: bottom;">photo_camera</span>
    </th>
    <th class="d-none d-lg-table-cell" style="text-align: right;">Distance</th>
    <th class="d-none d-lg-table-cell">Between</th>
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
    <td class="d-none d-lg-table-cell" align="right">{DISTANCE}</td>
    <td class="d-none d-lg-table-cell">{BETWEEN}</td>
</tr>
<!-- END TUNNEL -->
<!-- END LINE-OR-TUNNEL -->

</table>
<!-- END CONTENT -->
