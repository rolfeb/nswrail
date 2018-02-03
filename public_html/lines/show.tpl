<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<!-- BEGIN SUMMARY -->
<div class="float-right d-none d-lg-block">
  <div class="card" style="width:180px">
    <img class="card-img-top" src="/maps/images/ovmaps/{OVMAP}.png">
    <div class="card-body">
      <h3>Summary</h3>
      <table class="simple">
      <tr> <th>Track:</th> <td>{LINE-LENGTH}</td> </tr>
      <tr> <th>Stations:</th> <td><i>{LINE-STN-OPEN} / {LINE-STN-COUNT} in use</i></td> </tr>
      </table>
    </div>
  </div>
</div>
<!-- END SUMMARY -->

<div class="float-both">
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#tab-desc">Description</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#tab-hist">History</a>
  </li>
</ul>

<div class="tab-content">
<div class="tab-pane active container" id="tab-desc">
  <!-- BEGIN DESCRIPTION-MODE -->
  <div class="text">
  {DESCRIPTION}
  <!-- BEGIN EDIT-BLOCK -->
  Edit:
  [<a href="{EDIT-URL}">details</a>]
  <!-- END EDIT-BLOCK -->
  
  <!-- BEGIN EXT-LINK-BLOCK -->
  <h2>Links</h2>
  <ul>
  <!-- BEGIN EXT-LINK-URL -->
  <li><a class="offhost" href="{EXT-URL}">{EXT-TEXT}</a></li>
  <!-- END EXT-LINK-URL -->
  </ul>
  <!-- END EXT-LINK-BLOCK -->
  </div>

  <table class="clean" id="locations" width="100%">
  <thead>
  <tr>
    <!-- BEGIN ICON-HEADING -->
    <th class="icon" style="width: 22px"></td>
    <!-- END ICON-HEADING -->
    <th class="name">Name</th>
    <th class="facility">Facility</th>
    <th class="status">Status</th>
    <th style="text-align: right">Opened</th>
    <th style="text-align: right">Closed</th>
    <th style="text-align: right">km</th>
    <th style="text-align: right"><img src="/c/images/camera-icon.gif" alt="Photos"></th>
  </tr>
  </thead>
  <!-- BEGIN TABLE-ENTRY -->
  <!-- BEGIN LOCATION -->
  <tr style="margin:0; line-height: 1.4;">
    <!-- BEGIN ICON-DATA -->
    <td style="padding:0; width: 22px;">
    <!-- BEGIN ICON-DATA-FILLED -->
      <img src="{ICON}" alt="icon"></img>
    <!-- END ICON-DATA-FILLED -->
    </td>
    <!-- END ICON-DATA -->
    <td class="name"><span><a href="{URL}">{NAME}</a></span></td>
    <td class="facility"><span>{FACILITY}</span></td>
    <td class="status"><span>{STATUS}</span></td>
    <td class="text-right"><span>{OPENED}</span></td>
    <td class="text-right"><span>{CLOSED}</span></td>
    <td class="text-right"><span>{LOCATION}</span></td>
    <td class="text-right"><span>{PHOTOS}</span></td>
  </tr>
  <!-- END LOCATION -->
  <!-- BEGIN SEGMENT -->
  <tr>
  <td class="heading" colspan="8"><h2>{SEGMENT-TEXT}</h2></td>
  </tr>
  <!-- END SEGMENT -->
  <!-- END TABLE-ENTRY -->
  </table>
  <!-- END DESCRIPTION-MODE -->
</div>

<div class="tab-pane container" id="tab-hist">
  <!-- BEGIN HISTORY-MODE -->
  <table class="clean">
  <tr class="property">
  <td>Section</td>
  <td class="date-footnote">Opened</td>
  <td class="footnote"></td>
  <td class="date-footnote">Closed</td>
  <td class="footnote"></td>
  <td>Usage</td>
  </tr>
  <!-- BEGIN SEGMENT2-OR-SECTION -->
  <!-- BEGIN SEGMENT2 -->
  <tr class="value">
  <td class="heading" colspan="6"><h2>{SEGMENT-TEXT}</h2></td>
  </tr>
  <!-- END SEGMENT2 -->
  <!-- BEGIN SECTION -->
  <tr class="value">
  <td>{SECTION}</td>
  <td class="date-footnote">{OPENED}</td>
  <td class="footnote"><sup>&nbsp;{OPENFN}</sup></td>
  <td class="date-footnote">{CLOSED}</td>
  <td class="footnote"><sup>&nbsp;{CLOSEFN}</sup></td>
  <td>{USAGE}</td>
  </tr>
  <!-- END SECTION -->
  <!-- END SEGMENT2-OR-SECTION -->
  </table>
  <!-- BEGIN FOOTNOTES -->
  <sup>{XREF-SEQ}</sup> {XREF-TEXT}<br />
  <!-- END FOOTNOTES -->
  <!-- END HISTORY-MODE -->
</div>
</div>
</div>

<!-- END CONTENT -->
