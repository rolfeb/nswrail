<?php

require_once "site.inc";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function display_registration_form()
{
    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("register.tpl", true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('ADMIN-EMAIL', get_config('email-admin'));
    if (isset($_SERVER['HTTP_REFERER']))
        $t->setVariable('REFERRER', $_SERVER["HTTP_REFERER"]);
    else
        $t->setVariable('REFERRER', get_config('website-url'));
    $t->parseCurrentBlock();
    display_page("Registration", $t->get("CONTENT"),
        array(
            'HEAD-EXTRA' => '<script type="text/javascript" src="/c/register/register.js"></script>'
        )
    );
}

function fill_in_email_template($template, $website, $url)
{
    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile($template, true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('WEBSITE', $website);
    $t->setVariable('URL', $url);
    $t->parseCurrentBlock();
    return $t->get("CONTENT");
}

function process_registration_form($emailaddr, $fullname, $password1, $password2, $referrer)
{
    // repeat the browser-side checking
    if (strlen($emailaddr) < 5
            || strpos($emailaddr, "@") == false
            || strlen($fullname) < 5
            || strlen($password1) < 6
            || strlen($password2) < 6) {
        return 'Malformed registration request';
    }

    // check for an existing entry
    if (User::email_address_in_use($emailaddr)) {
        return 'Email address is already in use';
    }

    // generate a unique activation ID
    $activate_id = md5(uniqid(rand(),true));

    // add/update a pending registration record in the user table
    $enc_password = password_hash($password1, PASSWORD_DEFAULT);
    if (!User::register_new_user($emailaddr, $fullname, $enc_password, $activate_id, $_SERVER['REMOTE_ADDR'])) {
        return 'Failed to add user';
    }
    Audit::addentry(Audit::A_REGISTER, $emailaddr);

    // send an email to the username
    $confirm_url = get_config('website-url') . "/c/register/register.php?id=$activate_id";
    $website = get_config('website');

    $mail = new PHPMailer(true);

    try {
        # $mail->SMTPDebug = 2;
        $mail->isSMTP();

        $mail->Host = get_config('smtp-server');
        if (get_config('smtp-issecure')) {
            $mail->SMTPAuth = true;
            $mail->Username = get_config('smtp-username');
            $mail->Password = get_config('smtp-password');
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
        }

        $mail->setFrom(get_config('email-admin'), "$website admin");
        $mail->addAddress($emailaddr, $fullname);
        $mail->addReplyTo(get_config('email-noreply'), 'Do not reply');

        $mail->isHTML(true);
        $mail->Subject = "Please confirm account registration: $website";
        $mail->Body    = fill_in_email_template("email-html.tpl", $website, $confirm_url);
        $mail->AltBody = fill_in_email_template("email-plain.tpl", $website, $confirm_url);

        $mail->send();
    } catch (Exception $e) {
        return "Error: could not send email - $mail->ErrorInfo";
    }

    // redirect to post-registration page
    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("post-register.tpl", true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('EMAIL-ADDR', $emailaddr);
    $t->setVariable('REFERRER', $referrer);
    $t->parseCurrentBlock();
    display_page("Registration", $t->get("CONTENT"));
}

function activate_new_account($activate_code)
{
    // try activating the account
    $err = User::activate_user_via_code($activate_code);
    if ($err) {
        return $err;
    }
    Audit::addentry(Audit::A_ACTIVATE, $activate_code);

    // display success message

    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("post-activate.tpl", true, true);
    $t->setCurrentBlock('CONTENT');
    $t->setVariable('WEBSITE-URL', get_config('website-url'));
    $t->setVariable('WEBSITE', get_config('website'));
    $t->parseCurrentBlock();
    display_page("Account activation", $t->get("CONTENT"));
}

if (isset($_POST['username'])) {
    if (!isset($_POST['fullname']) || !isset($_POST['password1']) || !isset($_POST['password2'])) {
        error_page('Error - parameter error');
    }
    $emailaddr = $_POST['username'];
    $fullname = $_POST['fullname'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $referrer = $_POST['referrer'];

    $err = process_registration_form($emailaddr, $fullname, $password1, $password2, $referrer);
    if ($err) {
        error_page("Error - registration failed: $err");
    }

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
