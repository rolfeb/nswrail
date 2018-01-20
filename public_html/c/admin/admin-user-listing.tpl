<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<p>
<a href="/c/admin/usermaint.php?mode=add">Create new user</a>
</p>

<table class="table table-sm table-responsive-md">
<thead class="thead-light">
<tr>
<th>UID</th>
<th>Username</th>
<th>Full name</th>
<th>Role</th>
<th>Status</th>
<th>Joined</th>
<th>Last login</th>
</tr>
</head>
<!-- BEGIN USER-ENTRY -->
<tr class="{ROWCLASS}">
<td>{UID}</td>
<td><a href="/c/admin/usermaint.php?mode=modify&uid={UID}">{USERNAME}</a></td>
<td>{FULLNAME}</td>
<td>{ROLE}</td>
<td>{STATUS}</td>
<td>{JOINED}</td>
<td>{LOGIN}</td>
</tr>
<!-- END USER-ENTRY -->
</table>
<!-- END CONTENT -->
