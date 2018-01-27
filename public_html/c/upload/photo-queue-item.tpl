<!-- BEGIN CONTENT -->
<div class="row">
  <form method="post" onSubmit="return false;">
  <div class="photo-holder">
    <img src="/c/upload/aj-view.php?image={IMAGE-NAME}">
    <br/>
    <span><small>{IMAGE-NAME}</small></span>
  </div>
  <div class="button-holder">
    <div>
      <button name="delete" class="btn btn-sm btn-danger" onClick="do_delete_photo(this);">Delete</button>
      <button type="publish" class="btn btn-sm btn-success" onClick="do_publish_photo(this);">Publish</button>
    </div>
    <input type="hidden" class="image" name="image" value="{IMAGE-NAME}">
    <input type="hidden" class="tagnames" name="tagnames" value="{ALL-TAG-NAMES}">
  </div>
  <div class="detail-holder">
    <table style="clean" width="100%">
    <tr>
      <th>Location:</th>
      <td>
      <select class="state" name="state">
      <option selected>NSW</option>
          <option>VIC</option>
          <option>QLD</option>
          <option>SA</option>
        </select>
        <input type="text" class="location" name="location" list="all-locations">
        <datalist id="all-locations">
          <!-- BEGIN LOCATION --><option value="{LOCATION-NAME}">
          <!-- END LOCATION -->
        </datalist>
      </td>
    </tr>
    <tr>
      <th>Date:</th>
      <td>
        <select class="daterange" name="daterange">
          <option selected/>exact</option>
          <option>circa</option>
          <option>before</option>
          <option>after</option>
          <option>decade</option>
        </select>
        <input type="text" size="2" class="day" name="day" value="">
        -
        <select class="month" name="month">
          <option value="0" selected></option>
          <option value="1">Jan</option>
          <option value="2">Feb</option>
          <option value="3">Mar</option>
          <option value="4">Apr</option>
          <option value="5">May</option>
          <option value="6">Jun</option>
          <option value="7">Jul</option>
          <option value="8">Aug</option>
          <option value="9">Sep</option>
          <option value="10">Oct</option>
          <option value="11">Nov</option>
          <option value="12">Dec</option>
        </select>
        -
        <input type="text" size="4" class="year" name="year" value="{THIS-YEAR}">
      </td>
    </tr>
    <tr>
      <th>Caption:</th>
      <td><textarea cols="60" rows="4" spellcheck class="caption" name="caption"></textarea></td>
    </tr>
    <tr>
      <th>Tags:</th>
      <td>
          <!-- BEGIN PHOTO-TAG -->
          <span class="tag"><input type="checkbox" class="tag-{NAME}" name="tag-{NAME}"><label for="{NAME}">{TEXT}</label></span>
          <!-- END PHOTO-TAG -->
      </td>
    </tr>
    <tr>
      <th></th>
      <td><span class="error" name="error"></span></td>
    </tr>
    </table>
  </div>
  </form>
</div>
<!-- END CONTENT -->
