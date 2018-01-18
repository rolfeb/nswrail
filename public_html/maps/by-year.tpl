<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<p>
This page shows the changes made on the {REGION} network in 5-year blocks.
Drag the slider to change the date of the map.
</p>

<div id="year-control">
<div><div id="slider"></div></div>
<div><span id="year"></span></div>
</div>
<br clear="all"/>
<br />

<div style="float: left; border: 1px solid black;">
<div id="loading">Loading images...</div>
<!-- BEGIN YEAR -->
<div id="{MAP-YEAR}" style="display:none; padding: 0px;"><img src="{MAP-IMAGE}"/></div>
<!-- END YEAR -->
</div>

<br clear="all"/>
<div>
<img src="/c/images/bullet_green.png" />&nbsp;Open&nbsp;&nbsp;
<img src="/c/images/bullet_red.png" />&nbsp;Closed&nbsp;&nbsp;
<img src="/c/images/bullet_blue.png" />&nbsp;Tourist&nbsp;&nbsp;
<img src="/c/images/bullet_grey.png" />&nbsp;Lifted&nbsp;&nbsp;
<img src="/c/images/bullet_light_grey.png" />&nbsp;Uncompleted&nbsp;&nbsp;
<br/>
Bold lines changed status in the last 5 years.
</div>
<!-- END CONTENT -->
