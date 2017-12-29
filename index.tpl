<!-- BEGIN CONTENT -->
<div id="main">

<!-- COLUMN 1 -->
<div id="column_left">

<div id="search">
<form action="/search/location.php" method="get">
<h2>Location Search:</h2>
<div>
<input type="text" name="search" size="20"></input>
</div>
</form>
</div>

<div id="stats">
<table class="simple">
<tr> <td><b>Locations:</b></td> <td>{COUNT-LOCATIONS}</td> </tr>
<tr> <td><b>Photos:</b></td> <td>{COUNT-PHOTOS}</td> </tr>
</table>
<!-- BEGIN RECENT-PHOTO-BLOCK -->
<div id="recent_photos">
{RECENT-PHOTO-COUNT} photos added {RECENT-PHOTO-AGE}
</div>
<!-- END RECENT-PHOTO-BLOCK -->
</div>

<div id="copyright" class="ui-corner-all">
All text, maps and photographs are &copy; 2000-{COPYRIGHT-YEAR} Rolfe
Bozier except where otherwise noted. Please contact me if you would
like to use any of them. Where necessary, I can put you in touch with the
individual copyright owners.
</div>

</div>

<!-- COLUMN 3 -->
<div id="column_right">

<div id="status"  class="ui-corner-all">
<div id="date">Last change: {LAST-CHANGE_DATE}</div>
<div id="log">{LAST-CHANGE-LOG}</div>
</div>

</div>

<!-- COLUMN 2 -->
<div id="column_centre">


<div id="text">
These pages contain a variety of information about the NSW railway network,
both historical and current. The data can be broken down into four
categories: non-spatial (plain data), spatial (map data), photographic
and hypertext (links to related data sources). The intention of these pages
to make as much information as possible available on the net. Although
you will find pictures of trains within these pages, I have chosen to
concentrate on recording the infrastructure of the state, especially
on the abandoned branch lines.
</p>
<div class="boxed"><b>
You will probably have noticed that this site has not been updated for a
while...
Don't worry, it is not going to disappear, but I probably won't be
in a position to make any changes for a while.
</p>
For more details, see <a href="about/updates.txt">here</a>.
</b></div>
<p>
Much as I would like to, I probably won't be able to visit every
location in the state, taking notes and photographs! If you want to
contribute information or photos, then please contact me at
<a href="mailto:nswrail@pobox.com">nswrail@pobox.com</a>.
</p>
<p>
Much of the information and photos in these pages has been generously
provided by a lot of people. Their work has contributed immeasureably to
the success of these pages.
</p>
<p>
<b>Rolfe Bozier</b>
</p>
<img src="/images/logo-medium.png" alt="NSWrail.net logo" />
</div>

<div id="randompics">
<ul>
<!-- BEGIN RANDOM-PIC -->
<li><a href="{URL}"><img src="{IMG}" alt=""></img></a></li>
<!-- END RANDOM-PIC -->
</ul>
</div>

</div>

<!-- FOOTER -->
<div id="footer"></div>

<!-- END CONTENT -->
