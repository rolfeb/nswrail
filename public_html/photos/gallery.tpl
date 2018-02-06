<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<p class="text">{INTRODUCTION}</p>

<div style="float: left">
[Display as: <a href="{ALT-DISPLAY-URL}">{ALT-DISPLAY}</a>
Order by: <a href="{ALT-ORDER-URL}">{ALT-ORDER}</a>]
</div>
<div id="top-page-select" style="text-align: right;">
  <!-- BEGIN PAGE-SELECT1 -->
  Page: 
  <!-- BEGIN PAGE-PREV-ACTIVE1 -->
  <a href={FIRST-URL1}><img src="/c/images/button-arrow-full-left.png" alt="first"></a>
  <a href={PREV-URL1}><img src="/c/images/button-arrow-left.png" alt="prev"></a>
  <!-- END PAGE-PREV-ACTIVE1 -->
  <!-- BEGIN PAGE-PREV-INACTIVE1 -->
  <img src="/c/images/button-arrow-left-ghosted.png" alt="first">
  <img src="/c/images/button-arrow-full-left-ghosted.png" alt="first">
  <!-- END PAGE-PREV-INACTIVE1 -->
  {PAGE1} of {NPAGES1}
  <!-- BEGIN PAGE-NEXT-ACTIVE1 -->
  <a href={NEXT-URL1}><img src="/c/images/button-arrow-right.png" alt="next"></a>
  <a href={LAST-URL1}><img src="/c/images/button-arrow-full-right.png" alt="last"></a>
  <!-- END PAGE-NEXT-ACTIVE1 -->
  <!-- BEGIN PAGE-NEXT-INACTIVE1 -->
  <img src="/c/images/button-arrow-right-ghosted.png" alt="next">
  <img src="/c/images/button-arrow-full-right-ghosted.png" alt="last">
  <!-- END PAGE-NEXT-INACTIVE1 -->
  <!-- END PAGE-SELECT1 -->
</div>
<br clear="all"/>
<!-- BEGIN LISTING -->
<table class="table table-lg">
<!-- BEGIN LISTING-ROW -->
<tr>
  <td>
    <a data-toggle="modal" data-target="#imageDisplay" data-photo="{PHOTO-IMG}" data-location="{LOCATION} ({DATE})" data-text="{TEXT}" data-id="{UID}" data-fullname="{FULLNAME}" href="#">{LOCATION}</a>
  </td>
  <td>{DATE}</td>
  <td class="text">{TEXT}</td>
</tr>
<!-- END LISTING-ROW -->
</table>
<!-- END LISTING -->
<!-- BEGIN GALLERY -->
<table class="clean" width="100%">
<!-- BEGIN ROW -->
<tr>
<!-- BEGIN CELL -->
<td class="pl_thumbnail">
  <a data-toggle="modal" data-target="#imageDisplay" data-photo="{PHOTO-IMG}" data-location="{LOCATION} ({DATE})" data-text="{TEXT}" data-id="{UID}" data-fullname="{FULLNAME}" href="#"><img class="img-thumbnail" src="{THUMB-IMG}"></a>
  <br/>{LOCATION} ({DATE})
</td>
<!-- END CELL -->
</tr>
<!-- END ROW -->
</table>
<!-- END GALLERY -->

<!-- Modal -->
<div class="modal fade" id="imageDisplay" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-location"></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="overflow: auto">
          <img id="modal-photo" style="display: block; margin: 0 auto;">
      </div>
      <div class="modal-footer justify-content-start">
        <div>
        <p class="text" id="modal-text"></p>
        Copyright: <span id="modal-owner"></span> 
        <a id="modal-contact" href="/c/lib/mailer.php?uid=tbd"><span class="material-icons" style="vertical-align: bottom;">email</span></a>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$('#imageDisplay').on('show.bs.modal', function(e) {
  var m_photo = e.relatedTarget.dataset.photo;
  var m_location = e.relatedTarget.dataset.location;
  var m_text = e.relatedTarget.dataset.text;
  var m_uid = e.relatedTarget.dataset.uid;
  var m_fullname = e.relatedTarget.dataset.fullname;
  document.getElementById('modal-location').innerHTML = m_location;
  document.getElementById('modal-photo').src = m_photo;
  document.getElementById('modal-text').innerHTML = m_text;
  document.getElementById('modal-owner').innerHTML = m_fullname;
  document.getElementById('modal-contact').href = '/c/lib/mailer.php?uid=' + m_uid;
});
</script>

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
