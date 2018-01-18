<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<p>
<a href="/c/php/admin/user.php?mode=add">Create new user</a>
</p>

<table class="clean" width="100%">
<tr>
<th>UID</th>
<th>Email</th>
<th>Fullname</th>
<th>Alias</th>
<th>Password</th>
<th>Role</th>
<th>Status</th>
<th>Joined</th>
</tr>
<!-- BEGIN USER-ENTRY -->
<tr>
<td>{UID}</td>
<td><a href="/c/php/admin/user.php?mode=mod&uid={UID}">{EMAIL}</a></td>
<td>{FULLNAME}</td>
<td>{ALIAS}</td>
<td>{PASSWORD}</td>
<td>{ROLE}</td>
<td>{STATUS}</td>
<td>{JOINED}</td>
</tr>
<!-- END USER-ENTRY -->
</table>
<!-- END CONTENT -->
