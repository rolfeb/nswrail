<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

This is a timeline of the opening and closing dates for sections
of lines within NSW.  I also have some
<a href="/trivia/track_changes.php">graphs</a>
of total track length over time.

<table class="clean">
<tr>
<td class="property" align="right">Date</td>
<td class="property">Line</td>
<td class="property">Event</td>
<td class="property">Section</td>
<td class="property" align="right">Length</td>
</tr>

<!-- BEGIN EVENT -->
<tr>
  <!-- BEGIN DATE -->
  <td rowspan="{NUM-LINES}" class="rjust">{DATE}</td>
  <!-- END DATE -->
  <!-- BEGIN LINE -->
  <td rowspan="{NUM-SECTIONS}"><a href="{URL}">{LINE}</a></td>
  <!-- END LINE -->
  <td>{EVENT}</td>
  <td>{SECTION}</td>
  <td class="rjust">{KM} km</td>
</tr>
<!-- END EVENT -->
</table>

<!-- END CONTENT -->
