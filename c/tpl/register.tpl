<!-- BEGIN CONTENT -->
<h1>Registration</h1>
<div id="register-form" class="dialog" title="Register">
<p>
This form lets you register to be a user on this site. Registered users can
upload photos, and in some circumstances edit content.

If you don't want to do either of those, then there is no need to register;
feel free to use the site as a guest!
</p>
<p>
Please note that if you register, then I require that you provide your real
full name. This is mainly for copyright reasons. If you are uploading
photographs everything works better if they are associated with your real
name.
</p>
<p>
If you really need to be anonymous in some way (for example, if you work in
the rail industry), then <a href="mailto:{ADMIN-EMAIL}">email me</a>
after you have registered and I will set your account up be anonymised.
</p>
<form action="/c/php/register.php" method="post" onSubmit="return validate_register_form();">
<table class="simple">
<tr>
<td><label for="id_username">Email:</label></td>
<td><input type="text" size="40" name="username" id="register_username"></td>
</tr>
<tr>
<td><label for="id_fullname">Full name:</label></td>
<td><input type="text" size="40" name="fullname" id="register_fullname" /></td>
</tr>
<tr>
<td><label for="id_password">Password:</label></td>
<td><input type="password" size="20" name="password1" id="register_password1" /></td>
</tr>
<tr>
<td><label for="id_password">Confirm password:</label></td>
<td><input type="password" size="20" name="password2" id="register_password2" /></td>
</tr>
<tr><td colspan="2">
<p class="errormsg" id="register_error"></p>
</td></tr>
<tr><td colspan="2"><div align="right">
<input type="submit" value="Register">
</div></td></tr>
</table>
<input type="hidden" name="referrer" value="{REFERRER}">
</form>
</div>
<!-- END CONTENT -->
