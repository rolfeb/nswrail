<!-- BEGIN CONTENT -->
<h1>{LOCATION}</h1>

<div class="d-flex justify-content-center">
  <!-- BEGIN NEXT-PREV-SECTION -->
  <div style="flex: 9; display: flex; justify-content: center;">
  <!-- BEGIN PREV-LOCATION -->
  <span style="margin-left: auto;"><a href="{PREV-URL}">{PREV-TEXT}</a></span>
  <!-- END PREV-LOCATION -->
  </div>
  <div style="flex: 1; display: flex; justify-content: center;">
  <span><img src="/c/images/{PREV-NEXT-ICON}" alt=""></span>
  </div>
  <div style="flex: 9; display: flex; justify-content: center;">
  <!-- BEGIN NEXT-LOCATION -->
  <span style="margin-right: auto;"><a href="{NEXT-URL}">{NEXT-TEXT}</a></span>
  <!-- END NEXT-LOCATION -->
  </div>
  <!-- END NEXT-PREV-SECTION -->
</div>

<ul class="nav nav-tabs">
  <li class="nav-item">
    <a data-toggle="tab" class="nav-link active" href="#tab-desc">Description</a>
  </li>
<!-- BEGIN PHOTO-TAB1 -->
  <li class="nav-item">
    <a data-toggle="tab" class="nav-link" href="#tab-photo">Photographs</a>
  </li>
<!-- END PHOTO-TAB1 -->
<!-- BEGIN DIAGRAM-TAB1 -->
  <li class="nav-item">
    <a data-toggle="tab" class="nav-link" href="#tab-diag">Diagrams</a>
  </li>
<!-- END DIAGRAM-TAB1 -->
</ul>


<div class="tab-content">
<!-- Description tab -->
<div class="tab-pane active container" id="tab-desc">
  <div class="location-map float-lg-right">
    <span id="message1">{MAP-MESSAGE-1}</span>
    <div style="width: 400px; height: 400px; border: 1px solid black;" id="googleMap">
    <div id="message2">{MAP-MESSAGE-2}</div>
    </div>
    <span id="message3">{MAP-MESSAGE-3}</span>
  </div>
  
  <table class="table table-sm table-nonfluid mt-3">
  <thead class="thead-light">
  <tr>
      <th>Main&nbsp;facility:</th>
      <td>
          <span id="v-facility-type">{FACILITY}</span> (<span id="v-facility-status">{STATUS}</span>)
      </td>
  </tr>
  <!-- BEGIN LINES -->
  <tr>
      <th>Lines:</th>
      <td>
          <!-- BEGIN LINE-DETAILS -->
          <a href="{URL}">{TEXT}</a><br/>
          <!-- END LINE-DETAILS -->
      </td>
  </tr>
  <!-- END LINES -->
  <tr>
      <th>Location:</th>
      <td>
          <span id="v-location-xy">{LATLONG}</span> [<span id="v-location-exact">{POSACCURACY}</span>] GDA94
      </td>
  </tr>
  <tr>
      <th>Distance:</th>
      <td>
          <span id="v-distance">{DISTANCE}</span> km from {ORIGIN}<!-- BEGIN ALT-DIST --><br/>{DISTANCE2} km from {ORIGIN} (via {VIA-LOCATION})<!-- END ALT-DIST -->
      </td>
  </tr>
  <!-- BEGIN HISTORY -->
  <tr>
      <th>History:</th>
      <td>
          <!-- BEGIN HISTORY-DETAILS-SECTION -->
          <table class="table-clean">
              <!-- BEGIN HISTORY-DETAILS -->
              <tr><td align="right">{DATE}</td> <td>{EVENT}</td></tr>
              <!-- END HISTORY-DETAILS -->
          </table>
          <!-- END HISTORY-DETAILS-SECTION -->
      </td>
  </tr>
  <!-- END HISTORY -->
  
  <!-- BEGIN OPT-STATION-DETAILS -->
  <tr>
      <th>Station:</th>
      <td>{STATION-DETAILS}</td>
  </tr>
  <!-- END OPT-STATION-DETAILS -->
  <!-- BEGIN OPT-GOODS-DETAILS -->
  <tr>
      <th>Freight&nbsp;facilities:</th>
      <td>{GOODS-DETAILS}</td>
  </tr>
  <!-- END OPT-GOODS-DETAILS -->
  <!-- BEGIN OPT-INFRA-DETAILS -->
  <tr>
      <th>Other&nbsp;facilities:</th>
      <td>{INFRA-DETAILS}</td>
  </tr>
  <!-- END OPT-INFRA-DETAILS -->
  <!-- BEGIN PHOTO-DETAILS -->
  <tr>
      <th>Photos:</th>
      <td>{PHOTO-YEARS}</td>
  </tr>
  <!-- END PHOTO-DETAILS -->
  <tr>
      <th>Description:</th>
      <td>
          <i><span id="v-desc">{DESC}</span></i>
      </td>
  </tr>
  <tr>
      <th>Current&nbsp;status:</th>
      <td>
          <i><span id="v-curr">{CURR}</span></i>
      </td>
  </tr>
  <!-- BEGIN URLS -->
  <tr>
      <th>Links:</th>
      <td>
          <!-- BEGIN URL-DETAILS -->
          <a href="{LINK-URL}">{LINK-TEXT}</a><br/>
          <!-- END URL-DETAILS -->
      </td>
  </tr>
  <!-- END URLS -->
  </table>
  
  <!-- BEGIN ERROR -->
  <h1>Error</h1>
  
  No such location "{LOCATION}" in state "{STATE}".
  <!-- END ERROR -->
</div>

<!-- BEGIN PHOTO-TAB2 -->
<!-- Photographs tab -->
<div class="tab-pane container" id="tab-photo">
  <!-- BEGIN PHOTO-LIST -->
  <!-- BEGIN EDIT-PHOTO-BLOCK -->
  Edit:
  [<a href="{EDIT-PHOTO-URL}">photos</a>]
  <br/>
  <!-- END EDIT-PHOTO-BLOCK -->
  <!-- BEGIN PHOTO-DECADE -->
  <h3>{DECADE}</h3>
  <!-- END PHOTO-DECADE -->
  <!-- BEGIN PHOTO -->
  <a data-toggle="modal" data-target="#imageDisplay" data-photo="{PHOTO-IMG}" data-location="{LOCATION} ({DATE})" data-text="{TEXT}" data-id="{UID}" data-fullname="{FULLNAME}" href="#"><img class="img-thumbnail" src="{THUMB-IMG}"></a>
  &nbsp;
  <!-- END PHOTO -->
  <!-- BEGIN NO-PHOTOS -->
  <div class="msg-missing">
  There are no photographs for this location.
  </div>
  <!-- END NO-PHOTOS -->
  <!-- END PHOTO-LIST -->
</div>
<!-- END PHOTO-TAB2 -->

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
        <a id="modal-contact" data-toggle="tooltip" title="Please contact if you wish to use this photograph" href="/c/lib/mailer.php?uid=tbd"><span class="material-icons" style="vertical-align: bottom;">email</span></a>
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


<!-- BEGIN DIAGRAM-TAB2 -->
<!-- Diagrams tab -->
<div class="tab-pane container" id="tab-photo">
  <!-- BEGIN DIAGRAM-LIST -->
  <!-- BEGIN DIAGRAM -->
  <div>
  {DIAGRAM-YEAR}:<br/>
  <img src="{DIAGRAM-IMG}" border="1" alt="location diagram"></img>
  &nbsp;
  </div>
  <!-- END DIAGRAM -->
  <!-- BEGIN NO-DIAGRAMS -->
  <div class="msg-missing">
  There are no diagrams for this location.
  </div>
  <!-- END NO-DIAGRAMS -->
  <!-- END DIAGRAM-LIST -->
</div>
<!-- END DIAGRAM-TAB2 -->

<script>
var map;
function initMapCB() {
  var latlong = new google.maps.LatLng({MAP-GEOY}, {MAP-GEOX});

  // instantiate the map
  map = new google.maps.Map(document.getElementById('googleMap'), {
    center: latlong,
    zoom: {MAP-SCALE}
  });

  // custom icon for the location
  var icon = new google.maps.MarkerImage(
    'images/crosshair.png',
    new google.maps.Size(63, 63),   // size
    new google.maps.Point(0, 0),    // origin
    new google.maps.Point(32, 32)   // anchor
  );

  // add marker for this location
  var marker = new google.maps.Marker({
    map: map,
    position: latlong,
    icon: icon,
  });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={MAP-APIKEY}&callback=initMapCB" async defer></script>
<!-- END CONTENT -->
