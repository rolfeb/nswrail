<?php

require_once "util.inc";

?>

<div id="search">
<form action="/locations/search.php" method="get">
<h2>Location Search:</h2>
<div>
<input type="text" name="search" size="20"></input>
</div>
</form>
</div>

<div id="stats">
<table class="simple">
<tr> <td><b>Locations:</b></td> <td><? print count_locations(); ?></td> </tr>
<tr> <td><b>Photos:</b></td> <td><? print count_photographs(); ?></td> </tr>
<tr> <td><b>Visitors:</b></td> <td><? print count_visitors(); ?></td> </tr>
</table>
</div>

<div id="copyright">
All text, maps and photographs are &copy; 2000-<? print date("Y"); ?> Rolfe
Bozier except where otherwise noted. Please contact me if you would
like to use any of them (I will almost certainly say yes!).
</div>
