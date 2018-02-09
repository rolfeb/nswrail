<?php

require_once "site.inc";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function display_registration_form()
{
    $tp = [
        'admin_email' => get_config('email-admin'),
    ];

    if (isset($_SERVER['HTTP_REFERER']))
        $tp['referrer'] = $_SERVER["HTTP_REFERER"];
    else
        $tp['referrer'] = get_config('website-url');

    $latte = new Latte\Engine;
    display_page($title, $latte->renderToString('register.latte', $tp),
        [
            'HEAD-EXTRA' => '<script type="text/javascript" src="/c/register/register.js"></script>'
        ]
    );
}

function get_email_from_template($template, $website, $url)
{
    $tp = [
        'website' => $website;
        'url' => $url;
    ];

    $latte = new Latte\Engine;
    return $latte->renderToString($template, $tp);
}

function process_registration_form($emailaddr, $fullname, $password1, $password2, $referrer)
{
    // repeat the browser-side checking
    if (strlen($emailaddr) < 5
            || strpos($emailaddr, "@") == false
            || strlen($fullname) < 5
            || strlen($password1) < 6
            || strlen($password2) < 6) {
        throw new InternalError('Malformed registration request');
    }

    // check for an existing entry
    if (User::email_address_in_use($emailaddr)) {
        throw new UserError('Email address is already in use');
    }

    // generate a unique activation ID
    $activate_id = md5(uniqid(rand(),true));

    // add/update a pending registration record in the user table
    $enc_password = password_hash($password1, PASSWORD_DEFAULT);
    User::register_new_user($emailaddr, $fullname, $enc_password, $activate_id, $_SERVER['REMOTE_ADDR']);
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
        $mail->Body    = get_email_from_template("email-html.tpl", $website, $confirm_url);
        $mail->AltBody = get_email_from_template("email-plain.tpl", $website, $confirm_url);

        $mail->send();
    } catch (Exception $e) {
        rollback();
        throw new InternalError("could not send email - $mail->ErrorInfo");
    }

    // redirect to post-registration page
    $tp = [
        'email_addr' => $emailaddr;
        'referrer' => $referrer;
    ];

    $latte = new Latte\Engine;
    display_page('Registration', $latte->renderToString('post-register.latte', $tp));
}

function activate_new_account($activate_code)
{
    # try activating the account
    User::activate_user_via_code($activate_code);
    Audit::addentry(Audit::A_ACTIVATE, $activate_code);

    # display success message
    $tp = [
        'website_url' => get_config('website-url');
        'website' => get_config('website');
    ];

    $latte = new Latte\Engine;
    display_page('Account activation', $latte->renderToString('post-activate.latte', $tp));
}

try {
    if (isset($_POST['username'])) {
        #
        # This is a registration form submittal
        #
        if (!isset($_POST['fullname']) || !isset($_POST['password1']) || !isset($_POST['password2'])) {
            throw new InternalError('missing parameter');
        }
        $emailaddr = $_POST['username'];
        $fullname = $_POST['fullname'];
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];
        $referrer = $_POST['referrer'];

        process_registration_form($emailaddr, $fullname, $password1, $password2, $referrer);
    } else if (isset($_GET['id'])) {
        #
        # This is a registration activation
        #
        $activate_code = $_GET['id'];

        activate_new_account($activate_code);
    } else {
        #
        # This is a request to display the registration form
        #
        display_registration_form();
    }
} catch (\Exception $e) {
    report_error($e);
}

?>
