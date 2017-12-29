<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<!-- BEGIN MAIN -->
<head>
<title>NSWrail.net | {TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<link rel="stylesheet" type="text/css" href="local.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="description" content="Information about {TITLE}: history, description, current status, diagrams and photographs" />
<script src="/lib/ajaxutil.js" type="text/javascript"></script>
<!-- BEGIN GOOGLE-INCLUDE1 -->
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={GMAPKEY}"
    type="text/javascript"></script>
<!-- END GOOGLE-INCLUDE1 -->
<script src="locations.js" type="text/javascript" ></script>
<style type="text/css">
<!--
table#navlayout td#location {
    width: 60%;
    text-align: left;
}

table#navlayout td#nextprev {
    width: 40%;
    text-align: center;
}

span.alert {
    color: #f00;
}
-->
</style>
</head>
<body <!-- BEGIN GOOGLE-INCLUDE2 --> onload="load({LOCATION-WX}, {LOCATION-WY});" onunload="GUnload()" <!-- END GOOGLE-INCLUDE2 --> >
<!-- BEGIN CONTROLS -->
{TOP}
{MENU}
<!-- END CONTROLS -->

<div class="content">

<!-- BEGIN CONTENT -->
<table id="navlayout" class="simple" width="100%">
<tr>
  <td id="location"><h1>{LOCATION}</h1></td>
  <td id="nextprev">
    <!-- BEGIN NEXT-PREV-SECTION -->
    <!-- BEGIN PREV-LOCATION -->
    <a href="{PREV-URL}">{PREV-TEXT}</a>
    <!-- END PREV-LOCATION -->
    <img src="images/nextprev.gif" alt=""></img>
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
<span id="message2">{MAP-MESSAGE-2}</span>
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
        <!-- BEGIN LINE-MAP-LINK -->
        <b><a href="{URL}">{TEXT}</a></b>
        <!-- END LINE-MAP-LINK -->
        &nbsp;
        &nbsp;
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

<!-- END CONTENT -->

<!-- BEGIN PHOTO-LIST -->
<h2>Photographs</h2>
<!-- BEGIN EDIT-PHOTO-BLOCK -->
Edit:
[<a href="{EDIT-PHOTO-URL}">photos</a>]
<br/>
<!-- END EDIT-PHOTO-BLOCK -->
<!-- BEGIN PHOTO -->
<a href="{PHOTO-URL}"><img class="thumbnail" src="{PHOTO-THUMB}" alt="photograph thumbnail"></img></a>
&nbsp;
<!-- END PHOTO -->
<!-- END PHOTO-LIST -->

<!-- BEGIN DIAGRAM-LIST -->
<h2>Diagrams</h2>
<!-- BEGIN DIAGRAM -->
<div>
{DIAGRAM-YEAR}:<br/>
<img src="{DIAGRAM-IMG}" border="1" alt="location diagram"></img>
&nbsp;
</div>
<!-- END DIAGRAM -->
<!-- END DIAGRAM-LIST -->

<!-- BEGIN ERROR -->
<h1>Error</h1>

No such location "{LOCATION}" in state "{STATE}".
<!-- END ERROR -->

</div>

</body>
</html>
<!-- END MAIN -->
