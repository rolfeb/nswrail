<!-- BEGIN CONTENT -->

<div id="dialog-form" title="Upload a photograph">
    <p class="validateTips">All form fields are required.</p>

    <form action="/c/php/photos/submit.php" method="post" enctype="multipart/form-data">
    <table class="clean">
    <tr>
        <td><label for="image">Image File:</label></td>
        <td><input type="file" size="50" name="image" id="image" class="text ui-widget-content ui-corner-all""/></td>

    </tr><tr>
        <td><label for="location">Location:</label></td>
        <td><input type="text" name="location" id="location" class="text ui-widget-content ui-corner-all" /></td>

    </tr><tr>
        <td><label for="date">Date:</label></td>
        <td>
        <input type="text" name="date" id="date" class="text ui-widget-content ui-corner-all" size="12" />
        <br/>
        or
        <br/>
        <select name="date_error" id="date_error" class="ui-widget-content ui-corner-all" >
        <option></option>
        <option>before</option>
        <option>after</option>
        <option>circa</option>
        <option>decade</option>
        </select>
        <div style="display: inline">
        <input type="text" size="2" name="month" id="month" class="text ui-widget-content ui-corner-all" /> (MM)
        <input type="text" size="4" name="year" id="year" class="text ui-widget-content ui-corner-all" /> (YYYY)
        </div>
        </td>

    </tr><tr>
        <td><label for="description">Description:</label></td>
        <td><textarea cols="60" rows="5" name="description" id="description" class="text ui-widget-content ui-corner-all" ></textarea></td>
    </tr>
    </table>
    </form>
</div>

<h1>{TITLE}</h1>

<button id="upload-photo">Upload a photograph</button>

<table id="up_header" class="clean" width="100%">
<thead>
<tr>
<th class="up_location">State</td>
<th class="up_location">Location</td>
<th class="up_date">Date</td>
<th class="up_size">Size</td>
<th class="up_uploaded">Uploaded</td>
<th class="up_status">Status</td>
<th class="up_description">Description</td>
</tr>
</thead>
<!-- BEGIN PHOTO -->
<tbody>
<tr>
<td class="up_state">{STATE}</td>
<td class="up_location">{LOCATION}</td>
<td class="up_date">{DATE}</td>
<td class="up_size">{SIZE}</td>
<td class="up_uploaded">{UPLOADED}</td>
<td class="up_status">{STATUS}</td>
<td class="up_description">{DESCRIPTION}</td>
</tr>
</tbody>
<!-- END PHOTO -->
</table>


<!-- END CONTENT -->
