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

    normal_page('register-form.latte', $tp,
        [
            'HEAD-EXTRA' => '<script type="text/javascript" src="/c/register/register.js"></script>'
        ]
    );
}

function get_email_from_template($template, $website, $url)
{
    $tp = [
        'website' => $website,
        'url' => $url,
    ];

    $template_dir = $_SERVER['TEMPLATE_DIR'];

    $latte = new Latte\Engine;
    return $latte->renderToString("$template_dir/$template", $tp);
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
        $mail->Body    = get_email_from_template("register-email-html.latte", $website, $confirm_url);
        $mail->AltBody = get_email_from_template("register-email-plain.latte", $website, $confirm_url);

        $mail->send();
    } catch (Exception $e) {
        rollback();
        throw new InternalError("could not send email - $mail->ErrorInfo");
    }

    // redirect to post-registration page
    $tp = [
        'title' => 'Registration',
        'email_addr' => $emailaddr,
        'referrer' => $referrer,
    ];

    normal_page('register-post-register.latte', $tp);
}

function activate_new_account($activate_code)
{
    # try activating the account
    User::activate_user_via_code($activate_code);
    Audit::addentry(Audit::A_ACTIVATE, $activate_code);

    # display success message
    $tp = [
        'title' => 'Account activation',
        'website_url' => get_config('website-url'),
        'website' => get_config('website'),
    ];

    normal_page('register-post-activate.latte', $tp);
}

try {
    if (param_post_string_opt('username') != '') {
        #
        # This is a registration form submittal
        #
        $emailaddr = param_post_string('username');
        $fullname = param_post_string('fullname');
        $password1 = param_post_string('password1');
        $password2 = param_post_string('password2');
        $referrer = param_post_string('referrer');

        process_registration_form($emailaddr, $fullname, $password1, $password2, $referrer);
    } else if (param_get_string_opt('id') != '') {
        #
        # This is a registration activation
        #
        $activate_code = param_get_string_opt('id');

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
