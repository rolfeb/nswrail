<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>
<p>
{INTRODUCTION}
</p>

<div style="float: left">
[Display as: <a href="{ALT-DISPLAY-URL}">{ALT-DISPLAY}</a>
Order by: <a href="{ALT-ORDER-URL}">{ALT-ORDER}</a>]
</div>
<div id="top-page-select" style="text-align: right;">
<!-- BEGIN PAGE-SELECT1 -->
Page: 
<!-- BEGIN PAGE-PREV-ACTIVE1 -->
<a href={FIRST-URL1}><img src="/c/images/button-arrow-full-left.png" alt="first" /></a>
<a href={PREV-URL1}><img src="/c/images/button-arrow-left.png" alt="prev" /></a>
<!-- END PAGE-PREV-ACTIVE1 -->
<!-- BEGIN PAGE-PREV-INACTIVE1 -->
<img src="/c/images/button-arrow-left-ghosted.png" alt="first" />
<img src="/c/images/button-arrow-full-left-ghosted.png" alt="first" />
<!-- END PAGE-PREV-INACTIVE1 -->
{PAGE1} of {NPAGES1}
<!-- BEGIN PAGE-NEXT-ACTIVE1 -->
<a href={NEXT-URL1}><img src="/c/images/button-arrow-right.png" alt="next" /></a>
<a href={LAST-URL1}><img src="/c/images/button-arrow-full-right.png" alt="last" /></a>
<!-- END PAGE-NEXT-ACTIVE1 -->
<!-- BEGIN PAGE-NEXT-INACTIVE1 -->
<img src="/c/images/button-arrow-right-ghosted.png" alt="next" />
<img src="/c/images/button-arrow-full-right-ghosted.png" alt="last" />
<!-- END PAGE-NEXT-INACTIVE1 -->
<!-- END PAGE-SELECT1 -->
</div>
<br clear="all"/>

<table class="clean" width="100%">
<!-- BEGIN LISTING -->
<tr>
<td class="location"><a href="{HREF}">{NAME}</a></td>
<td>{DATE}</td>
<td class="caption">{TEXT}</td>
</tr>
<!-- END LISTING -->

<!-- BEGIN THUMBNAIL-ROW -->
<tr>
<!-- BEGIN CELL -->
<td class="pl_thumbnail">
<a href="{PHOTO-URL}?iframe=true&width=800&height=600" rel="prettyPhoto[iframes]"><img src="{THUMB-IMG}" alt="thumbnail"/></a>
<br>
{LOCATION} ({DATE})
</td>
<!-- END CELL -->
</tr>
<!-- END THUMBNAIL-ROW -->
</table>

<div id="bottom-page-select" style="text-align: right;">
<!-- BEGIN PAGE-SELECT2 -->
Page: 
<!-- BEGIN PAGE-PREV-ACTIVE2 -->
<a href={FIRST-URL2}><img src="/c/images/button-arrow-full-left.png" alt="first" /></a>
<a href={PREV-URL2}><img src="/c/images/button-arrow-left.png" alt="prev" /></a>
<!-- END PAGE-PREV-ACTIVE2 -->
<!-- BEGIN PAGE-PREV-INACTIVE2 -->
<img src="/c/images/button-arrow-left-ghosted.png" alt="first" />
<img src="/c/images/button-arrow-full-left-ghosted.png" alt="first" />
<!-- END PAGE-PREV-INACTIVE2 -->
{PAGE2} of {NPAGES2}
<!-- BEGIN PAGE-NEXT-ACTIVE2 -->
<a href={NEXT-URL2}><img src="/c/images/button-arrow-right.png" alt="next" /></a>
<a href={LAST-URL2}><img src="/c/images/button-arrow-full-right.png" alt="last" /></a>
<!-- END PAGE-NEXT-ACTIVE2 -->
<!-- BEGIN PAGE-NEXT-INACTIVE2 -->
<img src="/c/images/button-arrow-right-ghosted.png" alt="next" />
<img src="/c/images/button-arrow-full-right-ghosted.png" alt="last" />
<!-- END PAGE-NEXT-INACTIVE2 -->
<!-- END PAGE-SELECT2 -->
</div>
<!-- END CONTENT -->
