<table class="simple" width="100%" id="top"><tr>
<td align="left"><img src="/images/logo-medium.png" alt=""></img></td>
<td align="right">
<div id="auth">
<!-- BEGIN AUTHENTICATED-USER -->
<div id="logged_in_ctrl">
<span class="text-primary">{USERNAME}</span>
<a href="/c/php/auth/settings.php" class="btn btn-info">settings</a>
<a href="/c/login/logout.php" class="btn btn-info">logout</a>
</div>
<!-- END AUTHENTICATED-USER -->
<!-- BEGIN UNAUTHENTICATED-USER -->
<div>
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#login-dialog">login</button>
<a href="/c/register/register.php" class="btn btn-info">register</a>
</div>
<!-- login dialog -->
<div class="modal fade" id="login-dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <form action="/c/login/login.php" method="post">
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
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
      </div>

      <div class="modal-footer">
        <input type="button" class="btn btn-secondary" data-dismiss="modal" value="Cancel">
        <input type="submit" class="btn btn-secondary" value="Login">
      </div>
      </form>
    </div>
  </div>
</div>

<!-- END UNAUTHENTICATED-USER -->
</div><!-- auth -->
</td>
</tr></table>
