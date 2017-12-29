<!-- BEGIN CONTENT -->

<h1>{LOCATION}</h1>

<div id="location-tabs">
<ul>
<li><a href="#tabs-1">Description</a></li>
<!-- BEGIN PHOTO-TAB1 -->
<li><a href="#tabs-2">Photographs</a></li>
<!-- END PHOTO-TAB1 -->
<!-- BEGIN DIAGRAM-TAB1 -->
<li><a href="#tabs-3">Diagrams</a></li>
<!-- END DIAGRAM-TAB1 -->
</ul>

<div id="tabs-1">

<table id="navlayout" class="simple" width="100%">
<tr>
  <td id="location"></td>
  <td id="nextprev">
    <!-- BEGIN NEXT-PREV-SECTION -->
    <!-- BEGIN PREV-LOCATION -->
    <a href="{PREV-URL}">{PREV-TEXT}</a>
    <!-- END PREV-LOCATION -->
    <img src="/c/images/{PREV-NEXT-ICON}" alt=""></img>
    <!-- BEGIN NEXT-LOCATION -->
    <a href="{NEXT-URL}">{NEXT-TEXT}</a>
    <!-- END NEXT-LOCATION -->
    <!-- END NEXT-PREV-SECTION -->
  </td>
</tr>
</table>

<!-- BEGIN EDIT-BLOCK -->
Edit:
[<a href="{EDIT-TURNTABLES-URL}" target="_wedit">turntables</a>]
[<a href="{EDIT-LINKS-URL}" target="_wedit">links</a>]
<!-- END EDIT-BLOCK -->

<div class="location-map">
<span id="message1">{MAP-MESSAGE-1}</span>
<div id="map">
<div id="message2">{MAP-MESSAGE-2}</div>
</div>
<span id="message3">{MAP-MESSAGE-3}</span>
</div>

<table class="clean">
<tr>
    <td class="property">Main&nbsp;facility:</td>
    <td class="value">
        <span id="v-facility-type">{FACILITY}</span> (<span id="v-facility-status">{STATUS}</span>)

        {EDIT-FACILITY-PRE}
        <!-- BEGIN EDIT-FACILITY-DATA -->
        <div>
        <select class="edit" id="ev-facility-type">
            <!-- BEGIN TYPE-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END TYPE-OPTION -->
        </select>
        <select class="edit" id="ev-facility-status">
            <!-- BEGIN STATUS-OPTION -->
            <option {SELECTED} value="{VALUE}">{LABEL}</option>
            <!-- END STATUS-OPTION -->
        </select>
        </div>
        <!-- END EDIT-FACILITY-DATA -->
        {EDIT-FACILITY-POST}
    </td>
</tr>
<!-- BEGIN LINES -->
<tr>
    <td class="property">Lines:</td>
    <td class="value">
        <!-- BEGIN LINE-DETAILS -->
        <a href="{URL}">{TEXT}</a><br/>
        <!-- END LINE-DETAILS -->
    </td>
</tr>
<!-- END LINES -->
<tr>
    <td class="property">Location:</td>
    <td class="value">
        <span id="v-location-xy">{LATLONG}</span> GDA94

        {EDIT-LOCATION-PRE}
        <!-- BEGIN EDIT-LOCATION-DATA -->
        <div>
        <input class="edit" size="10" id="ev-location-x" value="{GEO_X}"/>
        <input class="edit" size="10" id="ev-location-y" value="{GEO_Y}"/>
        </div>
        <!-- END EDIT-LOCATION-DATA -->
        {EDIT-LOCATION-POST}
    </td>
</tr>
<tr>
    <td class="property">Distance:</td>
    <td class="value">
        <span id="v-distance">{DISTANCE}</span> km from {ORIGIN}<!-- BEGIN ALT-DIST --><br/>{DISTANCE2} km from {ORIGIN} (via {VIA-LOCATION})<!-- END ALT-DIST -->

        {EDIT-DISTANCE-PRE}
        <!-- BEGIN EDIT-DISTANCE-DATA -->
        <div>
        <input class="edit" size="7" id="ev-distance" value="{E-DISTANCE}"/>
        </div>
        <!-- END EDIT-DISTANCE-DATA -->
        {EDIT-DISTANCE-POST}
    </td>
</tr>
<!-- BEGIN HISTORY -->
<tr>
    <td class="property">History:</td>
    <td class="value">
        <!-- BEGIN HISTORY-DETAILS-SECTION -->
        <table class="simple">
            <!-- BEGIN HISTORY-DETAILS -->
            <tr><td align="right">{DATE}</td> <td>{EVENT}</td></tr>
            <!-- END HISTORY-DETAILS -->
        </table>
        <!-- END HISTORY-DETAILS-SECTION -->

        {EDIT-HISTORY-PRE}
        <!-- BEGIN EDIT-HISTORY-DATA -->
        <table class="edit simple">
            <tr>
                <td class="property">Type</td>
                <td class="property">DD</td>
                <td class="property">MM</td>
                <td class="property">YYYY</td>
                <td class="property">Error</td>
                <td class="property">New Name</td>
            </tr>
            <!-- BEGIN E-HISTORY-DETAILS -->
            <tr>
                <td>
                <select class="edit" id="ev-type{SEQ}">
                    <!-- BEGIN E-HIST-TYPE-OPTION -->
                    <option {SELECTED}>{VALUE}</option>
                    <!-- END E-HIST-TYPE-OPTION -->
                </select>
                </td>
                <td><input class="edit" size="2" id="ev-day{SEQ}" value="{DAY}"/></td>
                <td><input class="edit" size="2" id="ev-month{SEQ}" value="{MONTH}"/></td>
                <td><input class="edit" size="4" id="ev-year{SEQ}" value="{YEAR}"/></td>
                <td>
                <select class="edit" id="ev-error{SEQ}">
                    <!-- BEGIN E-HIST-ERROR-OPTION -->
                    <option {SELECTED} value="{VALUE}">{LABEL}</option>
                    <!-- END E-HIST-ERROR-OPTION -->
                </select>
                </td>
                <td>
                <input class="edit" size="20" maxlength="128" id="ev-name{SEQ}" value="{NAME}" />
                </td>
            </tr>
            <!-- END E-HISTORY-DETAILS -->
        </table>
        <!-- END EDIT-HISTORY-DATA -->
        {EDIT-HISTORY-POST}
    </td>
</tr>
<!-- END HISTORY -->

<!-- BEGIN OPT-STATION-DETAILS -->
<tr>
    <td class="property">Station:</td>
    <td class="value">{STATION-DETAILS}</td>
</tr>
<!-- END OPT-STATION-DETAILS -->
<!-- BEGIN OPT-GOODS-DETAILS -->
<tr>
    <td class="property">Freight&nbsp;facilities:</td>
    <td class="value">{GOODS-DETAILS}</td>
</tr>
<!-- END OPT-GOODS-DETAILS -->
<!-- BEGIN OPT-INFRA-DETAILS -->
<tr>
    <td class="property">Other&nbsp;facilities:</td>
    <td class="value">{INFRA-DETAILS}</td>
</tr>
<!-- END OPT-INFRA-DETAILS -->
<!-- BEGIN PHOTO-DETAILS -->
<tr>
    <td class="property">Photos:</td>
    <td class="value">{PHOTO-YEARS}</td>
</tr>
<!-- END PHOTO-DETAILS -->
<tr>
    <td class="property">Description:</td>
    <td class="value">
        <i><span id="v-desc">{DESC}</span></i>

        {EDIT-DESC-PRE}
        <!-- BEGIN EDIT-DESC-DATA -->
        <div>
        <textarea class="edit" rows="6" cols="28" id="ev-desc">{DESC}</textarea>
        </div>
        <!-- END EDIT-DESC-DATA -->
        {EDIT-DESC-POST}
    </td>
</tr>
<tr>
    <td class="property">Current&nbsp;status:</td>
    <td class="value">
        <i><span id="v-curr">{CURR}</span></i>

        {EDIT-CURR-PRE}
        <!-- BEGIN EDIT-CURR-DATA -->
        <div>
        <textarea class="edit" rows="6" cols="28" id="ev-curr">{CURR}</textarea>
        </div>
        <!-- END EDIT-CURR-DATA -->
        {EDIT-CURR-POST}
    </td>
</tr>
<!-- BEGIN URLS -->
<tr>
    <td class="property">Links:</td>
    <td class="value">
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
<br clear="all"/>
</div>

<!-- BEGIN PHOTO-TAB2 -->
<div id="tabs-2">
<!-- BEGIN PHOTO-LIST -->
<!-- BEGIN EDIT-PHOTO-BLOCK -->
Edit:
[<a href="{EDIT-PHOTO-URL}">photos</a>]
<br/>
<!-- END EDIT-PHOTO-BLOCK -->
<!-- BEGIN PHOTO-DECADE -->
<h2>{DECADE}</h2>
<!-- END PHOTO-DECADE -->
<!-- BEGIN PHOTO -->
<a href="{PHOTO-URL}?iframe=true&width=800&height=600" rel="prettyPhoto[iframes]"><img class="thumbnail" src="{PHOTO-THUMB}" alt="photograph thumbnail"></img></a>
&nbsp;
<!-- END PHOTO -->
<!-- BEGIN NO-PHOTOS -->
<div class="msg-missing">
There are no photographs for this location.
</div>
<!-- END NO-PHOTOS -->
<!-- END PHOTO-LIST -->

<br clear="all"/>
</div>
<!-- END PHOTO-TAB2 -->

<!-- BEGIN DIAGRAM-TAB2 -->
<div id="tabs-3">
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
<br clear="all"/>
</div>
<!-- END DIAGRAM-TAB2 -->

<!-- END CONTENT -->
