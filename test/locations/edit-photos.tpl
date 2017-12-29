<!-- BEGIN CONTENT -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;">
</div>

<h1>{TITLE} [edit photos]</h1>

<a href="{RETURN-URL}">Return from edit-mode</a>

<table class="simple" width="100%">
<!-- BEGIN ROW -->
<tr valign="top">
    <!-- BEGIN PHOTO -->
    <td width="25%" align="center">
        <table class="photo-ops">
            <tr height="17px">
                <td>
                    <!-- BEGIN UP-ARROW -->
                    <a href="{MOVE-UP-URL}"><img src="/c/images/button-arrow-up.png"></a>
                    <!-- END UP-ARROW -->
                </td>
                <td rowspan="5">
                    <img src="{THUMBNAIL}" id="{STATUS}" onmouseover="return overlib('{OVERLIB-TEXT}', HAUTO, WIDTH, 400);" onmouseout="nd();">
                    <br>
                    <span id="photo-date">{DATE}</span>
                </td>
                <td><a href="{DETAILS-URL}"><img src="/c/images/button-text.png"></a></td>
            </tr>
            <tr height="17px">
                <td>
                    <!-- BEGIN LEFT-ARROW -->
                    <a href="{MOVE-LEFT-URL}"><img src="/c/images/button-arrow-left.png"></a>
                    <!-- END LEFT-ARROW -->
                </td>
                <td><a href="{ENABLE-URL}"><img src="/c/images/button-enable.png"></a></td>
            </tr>
            <tr height="17px">
                <td>
                    <!-- BEGIN RIGHT-ARROW -->
                    <a href="{MOVE-RIGHT-URL}"><img src="/c/images/button-arrow-right.png"></a>
                    <!-- END RIGHT-ARROW -->
                </td>
                <td><a href="{DISABLE-URL}"><img src="/c/images/button-disable.png"></a></td>
            </tr>
            <tr height="17px">
                <td>
                    <!-- BEGIN DOWN-ARROW -->
                    <a href="{MOVE-DOWN-URL}"><img src="/c/images/button-arrow-down.png"></a>
                    <!-- END DOWN-ARROW -->
                </td>
                <td></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
    <!-- END PHOTO -->
</tr>
<!-- END ROW -->
</table>
<a href="{RETURN-URL}">Return from edit-mode</a>

<!-- END CONTENT -->
