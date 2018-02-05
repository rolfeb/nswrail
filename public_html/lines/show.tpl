<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>


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
  <!-- BEGIN SUMMARY -->
  <div class="float-right d-none d-lg-block my-1">
    <div class="card" style="width: 15rem">
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

  <table class="table table-sm table-hover" id="locations">
  <thead class="thead-dark">
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
    <th style="text-align: right;">
      <span class="material-icons" style="vertical-align: bottom;">photo_camera</span>
    </th>
  </tr>
  </thead>
  <!-- BEGIN TABLE-ENTRY -->
  <!-- BEGIN LOCATION -->
  <tr style="margin:0; line-height: 1.4;">
    <!-- BEGIN ICON-DATA -->
    <td style="background-image: url({ICON}); background-position: center center; background-repeat: no-repeat; min-width: 22px;"></td>
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
  <table class="table table-sm table-hover">
  <thead class="thead-dark">
  <tr>
    <th colspan="2">Section</th>
    <th style="text-align: right">Opened</th>
    <th style="text-align: right">Closed</th>
    <th>Usage</th>
  </tr>
  <!-- BEGIN SEGMENT2-OR-SECTION -->
  <!-- BEGIN SEGMENT2 -->
  <tr class="table-info">
    <td colspan="5">{SEGMENT-TEXT}</td>
  </tr>
  <!-- END SEGMENT2 -->
  <!-- BEGIN SECTION -->
  <tr>
    <td>&nbsp;</td>
    <td>{SECTION}</td>
    <td class="text-right">{OPENED}<sup>{OPENFN}</sup></td>
    <td class="text-right">{CLOSED}<sup>{CLOSEFN}</sup></td>
    <td>{USAGE}</td>
  </tr>
  <!-- END SECTION -->
  <!-- END SEGMENT2-OR-SECTION -->
  </table>
  <!-- BEGIN FOOTNOTES -->
  <hr/>
  <!-- BEGIN FOOTNOTE -->
  <sup>{XREF-SEQ}</sup> {XREF-TEXT}<br />
  <!-- END FOOTNOTE -->
  <!-- END FOOTNOTES -->
  <!-- END HISTORY-MODE -->
</div>
</div>
</div>

<!-- END CONTENT -->
