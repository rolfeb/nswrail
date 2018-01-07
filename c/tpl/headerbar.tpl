<table class="simple" width="100%" id="top"><tr>
<td align="left"><img src="/images/logo-medium.png" alt=""></img></td>
<td align="right">
<div id="auth">
<!-- BEGIN AUTHENTICATED-USER -->
<div id="logged_in_ctrl">
{USERNAME} |
<a href="/c/php/auth/settings.php">settings</a> |
<a href="/c/php/auth/logout.php">logout</a>
</div>
<!-- END AUTHENTICATED-USER -->
<!-- BEGIN UNAUTHENTICATED-USER -->
<div>
<a id="login" href="#">login</a> |
<a href="/c/php/register.php">register</a>
</div>
<!-- login dialog -->
<div id="login-form" class="dialog" title="Login">
<form action="/c/php/auth/login.php" method="post">
<table class="simple">
<tr>
<td><label for="id_username">Email:</label></td>
<td><input type="text" size="20" name="username" id="login_username" /></td>
</tr>
<tr>
<td><label for="id_password">Password:</label></td>
<td><input type="password" size="20" name="password" id="login_password" /></td>
</tr>
</table>
</form>
</div>
<!-- END UNAUTHENTICATED-USER -->
</div><!-- auth -->
</td>
</tr></table>
