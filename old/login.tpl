<!-- BEGIN MAIN -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>{TITLE}</title>
<link type="image/gif" rel="shortcut icon" href="/images/railicon.gif" />
<link rel="stylesheet" type="text/css" href="/common.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body class="loginpage">

<!-- BEGIN CONTROLS -->
<!-- END CONTROLS -->

<div id="loginbox">

<!-- BEGIN LOGIN -->
<form method="post" action="login.php" enctype="multipart/form-data">
<table class="simple">
<tr>
    <td colspan="2"><span id="login-title">NSWRail.net</span></td>
</tr>
<tr>
    <td class="property">Username:</td>
    <td><input type="text" name="username" size="20" /></td>
</tr>
<tr>
    <td class="property">Password:</td>
    <td><input type="passwd" name="password" size="20" /></td>
</tr>
<tr>
    <td colspan="2" align="right">
        <input type="submit" name="button" value="Cancel" />
        <input type="submit" name="button" value="Login" />
    </td>
</tr>
<!-- BEGIN MESSAGE-BLOCK -->
<tr>
    <td colspan="2"><span id="login-msg">{MESSAGE}</span></td>
</tr>
<!-- END MESSAGE-BLOCK -->
</table>
<input type="hidden" name="action" value="login" />
<input type="hidden" name="redirect" value="{REDIRECT}" />
</form>
<!-- END LOGIN -->

</div>

</body>
</html>
<!-- END MAIN -->
