<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

Quick Index:
<!-- BEGIN INDEX -->
<a href="#{LETTER}">{LETTER}</a>
<!-- END INDEX -->

<table class="clean">
<tr class="property">
    <td>Location</td>
    <td>Type</td>
    <td>Status</td>
    <td align="right">Distance</td>
    <td>Line</td>
</tr>
<!-- BEGIN LOCATION -->
<tr class="{CLASS}">

<!-- BEGIN LOCATION-LINK-DEST -->
    <td><a name="{INDEX-TARGET}" href="{LOCATION-URL}">{LOCATION}</a></td>
<!-- END LOCATION-LINK-DEST -->
<!-- BEGIN LOCATION-NONLINK-DEST -->
    <td><a href="{LOCATION-URL}">{LOCATION}</a></td>
<!-- END LOCATION-NONLINK-DEST -->
    <td>{TYPE}</td>
    <td>{STATUS}</td>
    <td align="right">{DISTANCE}</td>
    <td><a href="{LINE-URL}">{LINE}</a></td>
</tr>
<!-- END LOCATION -->
</table>

<!-- END CONTENT -->
