<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<!-- BEGIN SUMMARY -->
<div class="lineinfo">
<div class="card" style="width:150px">
  <img class="card-img-top" src="/maps/images/ovmaps/{OVMAP}.png">
  <div class="card-body">
    <h2>Summary</h2>
    <table class="simple">
    <tr> <td><b>Track:</b></td> <td>{LINE-LENGTH}</td> </tr>
    <tr> <td><b>Stations:</b></td> <td><i>{LINE-STN-OPEN} / {LINE-STN-COUNT} in use</i></td> </tr>
    </table>
  </div>
</div>
</div>
<!-- END SUMMARY -->

<div style="float: both">
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a data-toggle="tab" class="nav-link active" href="#tab-desc">Description</a>
  </li>
  <li class="nav-item">
    <a data-toggle="tab" class="nav-link" href="#tab-hist">History</a>
  </li>
</ul>

<div class="tab-content">
<div class="tab-pane active container" id="tab-desc">
  <!-- BEGIN DESCRIPTION-MODE -->
  <div>
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
  <table class="clean" id="locations">
  <tr class="property">
  <!-- BEGIN ICON-HEADING -->
  <td class="icon"></td>
  <!-- END ICON-HEADING -->
  <td class="name">Name</td>
  <td class="facility">Facility</td>
  <td class="status">Status</td>
  <td class="opened">Opened</td>
  <td class="closed">Closed</td>
  <td class="distance">km</td>
  <td class="photos"><img src="/c/images/camera-icon.gif" alt="Photos" /></td>
  <td class="links">Links</td>
  </tr>
  <!-- BEGIN TABLE-ENTRY -->
  <!-- BEGIN LOCATION -->
  <tr class="value">
  <!-- BEGIN ICON-DATA -->
  <td class="icon">
  <!-- BEGIN ICON-DATA-FILLED -->
  <img src="{ICON}" alt="icon"></img>
  <!-- END ICON-DATA-FILLED -->
  </td>
  <!-- END ICON-DATA -->
  <td class="name"><span><a href="{URL}">{NAME}</a></span></td>
  <td class="facility"><span>{FACILITY}</span></td>
  <td class="status"><span>{STATUS}</span></td>
  <td class="opened"><span>{OPENED}</span></td>
  <td class="closed"><span>{CLOSED}</span></td>
  <td class="distance"><span>{LOCATION}</span></td>
  <td class="photos"><span>{PHOTOS}</span></td>
  <td class="links"><span>{LINKS}</span></td>
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
