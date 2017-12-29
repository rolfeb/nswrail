<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>
<!-- BEGIN USER-DETAILS -->
<form method="post" action="user.php" enctype="multipart/form-data">
<table class="clean">
<tr>
<th>Email</th>
<td><input type="text" name="email" value="{EMAIL}"/></td>
</tr>
<tr>
<th>Fullname</th>
<td><input type="text" name="fullname" value="{FULLNAME}"/></td>
</tr>
<tr>
<th>Anonymous</th>
<td>
<select name="anonymous">
    <!-- BEGIN ANONYMOUS-OPTION -->
    <option {SELECTED}>{VALUE}</option>
    <!-- END ANONYMOUS-OPTION -->
</select>
</td>
</tr>
<tr>
<th>Password</th>
<td><input type="password" name="password" value=""/></td>
</tr>
<tr>
<th>Role</th>
<td>
<select name="role">
    <!-- BEGIN ROLE-OPTION -->
    <option {SELECTED}>{VALUE}</option>
    <!-- END ROLE-OPTION -->
</select>
</td>
</tr>
<tr>
<th>Status</th>
<td>
<select name="status">
    <!-- BEGIN STATUS-OPTION -->
    <option {SELECTED}>{VALUE}</option>
    <!-- END STATUS-OPTION -->
</select>
</td>
</tr>
</table>
<input type="hidden" name="mode" value="add"/>
<input type="hidden" name="uid" value="-1"/>
<input type="submit" name="action" value="Cancel"></input>
<input type="reset" value="Reset"></input>
<input type="submit" name="action" value="Submit"></input>
<form>
<!-- END USER-DETAILS -->
<!-- BEGIN ERROR -->
<div class="message">{TEXT}</div>
<!-- END ERROR -->
<!-- END CONTENT -->
