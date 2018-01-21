<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<p>
This page lists the turntables which have been used in NSW.  With the demise
of steam locomotives turntables have little use, and hence most of the
following have been removed or are no longer functional.
</p>
The "status" values listed have the following meanings:
<table class="table table-sm table-responsive-md table-bordered" style="width: auto !important">
<tr>
    <td>In use</td>
    <td>Sees occasional use, for example when turning heritage steam
        locos.</td>
</tr>
<tr>
    <td>Out of use</td>
    <td>Currently booked out of use.</td>
</tr>
<tr>
    <td>Closed</td>
    <td>No longer used, but still in a reasonably good condition.</td>
</tr>
<tr>
    <td>Derelict</td>
    <td>No longer capable of being used, with some damage or no longer
        connected to the main line.</td>
</tr>
<tr>
    <td>Ruins</td>
    <td>Only the pit or foundations remain.</td>
</tr>
<tr>
    <td>No trace</td>
    <td>It is no longer possible to see where the turntable once resided.</td>
</tr>
</table>

<p/>
<table class="simple" width="100%">
<tr valign="top">
<td>
The "type" values have the following meanings:
<ul>
<li>M = Turntable is/was manually operated</li>
<li>E = Turntable is/was electrically-driven</li>
</ul>
</td>
<td>
For the metrically minded, equivalent sizes are:
<ul>
<li> 50' = 15 metres</li>
<li> 60' = 18 metres</li>
<li> 75' = 23 metres</li>
<li> 90' = 27 metres</li>
<li> 105' = 32 metres</li>
</ul>
</td>
</tr>
</table>

<!-- BEGIN TABLE-CHUNK -->
<!-- BEGIN REGION1 -->
<h2>{REGION}</h2>
<table class="table table-sm table-responsive-md">
<thead class="thead-dark">
<tr>
  <th colspan="2">Location</th>
  <th align="right">Size</th>
  <th align="right">Type</th>
  <th>Status</th>
  <th>Notes</th>
  <th align="center">Photos</th>
</tr>
</thead>
<!-- END REGION1 -->
<!-- BEGIN LINE -->
<tr class="table-info">
  <td colspan="7">{LINE-TEXT}</td>
</tr>
<!-- END LINE -->
<!-- BEGIN LOCATION -->
<tr>
  <td></td>
  <td><a href="{LOCATION-URL}">{LOCATION-TEXT}</a></td>
  <td align="right">{SIZE}</td>
  <td align="center">{TYPE}</td>
  <td>{STATUS}</td>
  <td>{NOTES}</td>
  <td align="center">{PHOTOS}</td>
</tr>
<!-- END LOCATION -->
<!-- BEGIN REGION2 -->
</table>
<!-- END REGION2 -->
<!-- END TABLE-CHUNK -->

<!-- END CONTENT -->
