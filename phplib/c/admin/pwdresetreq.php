<?php
/**
 * Copyright (c) 2018 Rolfe Bozier
 */

require 'site.inc';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 *
 */
function show_form()
{
    $tp = [];

    normal_page('admin-pwdresetreq.latte', $tp);
}


/**
 * @param $template
 * @param $email_addr
 * @param $reset_url
 * @param $website
 * @return string
 */
function get_email_from_template($template, $email_addr, $reset_url, $website)
{
    $tp = [
        'email_addr' => $email_addr,
        'url' => $reset_url,
        'website' => $website,
    ];

    $template_dir = $_SERVER['TEMPLATE_DIR'];

    $latte = new Latte\Engine;
    return $latte->renderToString("$template_dir/$template", $tp);
}

/**
 * @param $emailaddr
 * @throws InternalError
 */
function process_request($emailaddr)
{
    // repeat the browser-side checking
    if (strlen($emailaddr) < 5
            || strpos($emailaddr, "@") == false) {
        throw new InternalError('Malformed password reset request');
    }

    // check for an existing entry
    if (User::email_address_in_use($emailaddr)) {

        // generate a unique activation ID
        $reset_id = md5(uniqid(rand(), true));

        // update user table with reset id
        User::save_pwdreset_id($emailaddr, $reset_id);
        Audit::addentry(Audit::A_PWDRESET, $emailaddr);

        // send an email to the username
        $pwdreset_url = get_config('website-url') . "/c/admin/pwdresetreq.php?id=$reset_id";
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
            $mail->addAddress($emailaddr);
            $mail->addReplyTo(get_config('email-noreply'), 'Do not reply');

            $mail->isHTML(true);
            $mail->Subject = "Password reset request: $website";
            $mail->Body = get_email_from_template("pwdreset-email-html.latte", $emailaddr, $pwdreset_url,
                $website);
            $mail->AltBody = get_email_from_template("pwdreset-email-plain.latte", $emailaddr, $pwdreset_url,
                $website);

            $mail->send();
        } catch (Exception $e) {
            rollback();
            throw new InternalError("could not send email - $mail->ErrorInfo");
        }
    }

    // redirect to post-pwdreset page
    $tp = [
        'title' => 'Password reset',
        'emailaddr' => $emailaddr,
    ];

    normal_page('admin-pwdresetreq-post.latte', $tp);
}

try {
    $username = param_post_string_opt("username");
    $referer = $_SERVER["HTTP_REFERER"];
    if (!isset($referer)) {
        $referer = get_config('website-url');
    }

    if ($username == '') {
        show_form();
    } else {
        $action = param_post_string_opt('action');
        $emailaddr = param_post_string('username');

        if ($action == 'Cancel') {
            header("Location: $referer");
            exit();
        }
        process_request($emailaddr);
    }

} catch (\Exception $e) {
    report_error($e);
}
