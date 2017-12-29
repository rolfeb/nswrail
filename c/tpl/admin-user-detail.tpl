<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>
<!-- BEGIN USER-DETAILS -->
<form method="post" action="user.php" enctype="multipart/form-data">
<table class="clean">
<tr>
<th>UID</th>
<td>{UID}</td>
<td>{UID}</td>
</tr>
<tr>
<th>Email</th>
<td>{EMAIL}</td>
<td><input type="text" name="email" value="{EMAIL}"/></td>
</tr>
<tr>
<th>Fullname</th>
<td>{FULLNAME}</td>
<td><input type="text" name="fullname" value="{FULLNAME}"/></td>
</tr>
<tr>
<th>Anonymous</th>
<td>{ANONYMOUS}</td>
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
<td>******</td>
<td><input type="password" name="password" value=""/></td>
</tr>
<tr>
<th>Role</th>
<td>{ROLE}</td>
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
<td>{STATUS}</td>
<td>
<select name="status">
    <!-- BEGIN STATUS-OPTION -->
    <option {SELECTED}>{VALUE}</option>
    <!-- END STATUS-OPTION -->
</select>
</td>
</tr>
<tr>
<th>Joined</th>
<td>{JOINED}</td>
<td>{JOINED}</td>
</tr>
</table>
<input type="hidden" name="uid" value="{UID}"/>
<input type="hidden" name="version" value="{VERSION}"/>
<input type="submit" name="action" value="Cancel"></input>
<input type="reset" value="Reset"></input>
<input type="submit" name="action" value="Submit"></input>
<form>
<!-- END USER-DETAILS -->
<!-- BEGIN ERROR -->
<div class="message">{TEXT}</div>
<!-- END ERROR -->
<!-- END CONTENT -->
