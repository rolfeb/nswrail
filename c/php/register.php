<?php

require_once "site.inc";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function display_registration_form()
{
    // XXX: config()
    global $BASE_PATH;
    global $WEBSITE;
    global $ADMIN_EMAIL;

    $t = new HTML_Template_ITX("$BASE_PATH/c/tpl");
    $t->loadTemplateFile("register.tpl", true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('ADMIN-EMAIL', $ADMIN_EMAIL);
    if (isset($_SERVER['HTTP_REFERER']))
        $t->setVariable('REFERRER', $_SERVER["HTTP_REFERER"]);
    else
        $t->setVariable('REFERRER', "http://$WEBSITE/");
    $t->parseCurrentBlock();
    display_page("Registration", $t->get("CONTENT"),
        array(
            'HEAD-EXTRA' => '<script type="text/javascript" src="/c/js/register.js"></script>'
        )
    );
}

function process_registration_form()
{
    // XXX: config()
    global $BASE_PATH;
    global $WEBSITE;
    global $dbi;

    // XXX: move to caller
    $emailaddr = $_POST['username'];
    $fullname = $_POST['fullname'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $referrer = $_POST['referrer'];

    // repeat the browser-side checking
    if (strlen($emailaddr) < 5
        || strpos($emailaddr, "@") == false
        || strlen($fullname) < 5
        || strlen($password1) < 6
        || strlen($password2) < 6) {
            // XXX: combine with post-registration page
            error_page("Malformed registration request", $referrer);
    }

    // check for an existing entry
    if (User::email_address_in_use($dbi, $emailaddr)) {
        // XXX: combine with post-registration page
        error_page("Email address is already in use", $referrer);
    }

    // generate a unique activation ID
    $activate_id = md5(uniqid(rand(),true));

    // add/update a pending registration record in the user table
    $enc_password = password_hash($password1, PASSWORD_DEFAULT);
    if (!User::register_new_user($dbi, $emailaddr, $fullname, $enc_password, $activate_id, $_SERVER['REMOTE_ADDR'])) {
        // XXX: combine with post-registration page
        error_page("Failed to add user", $referrer);
    }

    // send an email to the username
    $confirm_url = "http://$WEBSITE/c/php/register.php?id=$activate_id";

    $email_content_html = <<<EOD
<p>
This email has been sent in response to an account registration at $WEBSITE.
<br/>
If you are the person that registered the account, then please click on the
following link to activate your account:
<br/>
<a href="$confirm_url">Confirm my registration</a>
<br/>
or, paste the following URL into your browser:
<br/>
$confirm_url
<br/>
If you did NOT register an account at $WEBSITE, then you can just ignore this
email; the account will be deleted. Maybe someone mis-typed their address.
</p>
EOD;

    $email_content_plaintext = <<<EOD
This email has been sent in response to an account registration at $WEBSITE.

If you are the person that registered the account, then please paste the
following URL into your browser:

$confirm_url

If you did NOT register an account at $WEBSITE, then you can just ignore this
email; the account will be deleted. Maybe someone mis-typed their address.
EOD;
    
    $mail = new PHPMailer(true);

    try {
        # $mail->SMTPDebug = 2;
        $mail->isSMTP();
        // XXX: move to config
        $mail->Host = 'mail-hub.bigpond.net.au';
        # $mail->SMTPAuth = true;
        # $mail->Username = 'user@example.com';
        # $mail->Password = 'secret';
        # $mail->SMTPSecure = 'tls';
        # $mail->Port = 587;

        // XXX: move to config
        $mail->setFrom('admin@nswrail.net', "$WEBSITE admin");
        $mail->addAddress($emailaddr, $fullname);
        $mail->addReplyTo('noreply@nswrail.net', 'Do not reply');

        $mail->isHTML(true);
        $mail->Subject = "Please confirm account registration: $WEBSITE";
        $mail->Body    = $email_content_html;
        $mail->AltBody = $email_content_plaintext;

        $mail->send();
    } catch (Exception $e) {
        // XXX: combine with post-registration page
        error_page("Error: could not send email - $mail->ErrorInfo");
    }

    // redirect to post-registration page
    $t = new HTML_Template_ITX("$BASE_PATH/c/tpl");
    $t->loadTemplateFile("register2.tpl", true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('EMAIL-ADDR', $emailaddr);
    $t->setVariable('REFERRER', $referrer);
    $t->parseCurrentBlock();
    display_page("Registration", $t->get("CONTENT"));
}

function activate_new_account($activate_code)
{
    global $dbi;
    global $WEBSITE;

    // try activating the account
    $err = User::activate_user_via_code($dbi, $activate_code);
    if ($err) {
        return $err;
    }

    // display success message
    $website_url = "http://$WEBSITE/";

    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("activate.tpl", true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('WEBSITE-URL', $website_url);
    $t->setVariable('WEBSITE', $WEBSITE);
    $t->parseCurrentBlock();
    display_page("Account activation", $t->get("CONTENT"));
}

if (isset($_POST['username'])) {
    process_registration_form();
} else if (isset($_GET['id'])) {
    $activate_code = $_GET['id'];
    $err = activate_new_account($activate_code);
    if ($err) {
        error_page("Error - activation failed: $err");
    }
} else {
    display_registration_form();
}

?>
