<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

This page details some of the highest and lowest points on the NSW network.

<h2>Highest Points</h2>

<table class="table table-sm table-responsive-md">
<thead class="thead-dark">
<tr>
  <th>Line</th>
  <th style="text-align:right">Height</th>
  <th>Location</th>
</tr>
</thead>
<!-- BEGIN HIGH -->
<tr>
  <td><a href="{LINE-URL}">{LINE-TEXT}</a></td>
  <td align="right">{HEIGHT} m</td>
  <!-- BEGIN HIGH-LOCATION -->
  <td>{LOCATION}</td>
  <!-- END HIGH-LOCATION -->
  <!-- BEGIN HIGH-STATION -->
  <td><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
  <!-- END HIGH-STATION -->
</tr>
<!-- END HIGH -->
</table>

<h2>Lowest Points</h2>

<table class="table table-sm table-responsive-md">
<thead class="thead-dark">
<tr>
  <th>Line</th>
  <th style="text-align:right">Height</th>
  <th>Location</th>
</tr>
</thead>
<!-- BEGIN LOW -->
<tr>
  <td><a href="{LINE-URL}">{LINE-TEXT}</a></td>
  <td align="right">{HEIGHT} m</td>
  <!-- BEGIN LOW-LOCATION -->
  <td>{LOCATION}</td>
  <!-- END LOW-LOCATION -->
  <!-- BEGIN LOW-STATION -->
  <td><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
  <!-- END LOW-STATION -->
</tr>
<!-- END LOW -->
</table>

<!-- END CONTENT -->
