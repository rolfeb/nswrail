<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>
<!-- BEGIN USER-DETAILS -->
<form method="post" action="/c/admin/usermaint.php" enctype="multipart/form-data">
<table class="table table-sm table-responsive-md">
<tr>
  <th>User name:</th>
  <td><input type="email" class="form-control" name="username" value="{USERNAME}"></td>
</tr>
<tr>
  <th>Fullname:</th>
  <td><input type="text" class="form-control" name="fullname" value="{FULLNAME}"></td>
</tr>
<tr>
  <th>Password:</th>
  <td><input type="password" class="form-control" name="password1" value=""></td>
</tr>
<tr>
  <th>Password (confirm):</th>
  <td><input type="password" class="form-control" name="password2" value=""></td>
</tr>
<tr>
  <th>Role:</th>
  <td>
  <!-- BEGIN ROLE-OPTION -->
  <div class="form-check form-check-inline">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input" name="{NAME}">{VALUE}
    </label>
  </div>
  <!-- END ROLE-OPTION -->
  </td>
</tr>
<tr>
  <th>Status:</th>
  <td>
  <!-- BEGIN STATUS-OPTION -->
  <div class="form-check form-check-inline">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input" name="{NAME}">{VALUE}
    </label>
  </div>
  <!-- END STATUS-OPTION -->
  </td>
</tr>
</table>

<input type="hidden" name="uid" value="{UID}"/>
<input type="hidden" name="mode" value="add"/>
<input type="submit" class="btn btn-secondary" name="action" value="Cancel">
<input type="reset" class="btn btn-secondary" value="Reset">
<input type="submit" class="btn btn-primary" name="action" value="Submit">
</form>
<!-- END USER-DETAILS -->
<!-- BEGIN ERROR -->
<div class="message">{TEXT}</div>
<!-- END ERROR -->
<!-- END CONTENT -->
