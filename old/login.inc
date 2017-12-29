<div id="login">

<?php

if (auth_loggedin())
{
    $user = auth_login_user();
    print "Hello, $user! "
    print "[<a href="/logout.php">logout</a>]\n";
}
else
{
    print "Hello, guest! "
    print "[<a href="/login.php">login</a>]\n";
}

</div>

?>
