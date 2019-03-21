<?php
/**
 * Copyright (c) 2019 Rolfe Bozier
 */

require "site.inc";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$javascript_block = /** @lang HTML */
    <<<HEREDOC
<script type="text/javascript">
function validate_form()
{
    let sender_email = document.getElementById("sender_email").value;
    let subject = document.getElementById("subject").value;
    let text = document.getElementById("text").value;

    let error = null;
    if (sender_email.length < 5) {
        error = "ERROR: sender email address is too short";
    } else if (sender_email.indexOf("@") < 0) {
        error = "ERROR: sender email address contains no '@'";
    } else if (subject === '') {
        error = "ERROR: subject must not be empty";
    } else if (text.length < 6) {
        error = "ERROR: text is too short?!";
    }
    if (error) {
        document.getElementById("error").innerHTML = error;
        return false;
    } else {
        document.getElementById("error").innerHTML = '';
    }

    return true;
}
</script>
HEREDOC;

/**
 * @param $recipient_uid
 * @param $referrer
 * @throws SecurityError
 */
function display_mailer_form($recipient_uid, $referrer)
{
    global $javascript_block;

    global $user;

    $uinfo = User::user_info_from_uid($recipient_uid);

    $tp = [
        'recipient_uid' => $recipient_uid,
        'recipient_name' => $uinfo['fullname'],
        'orig_referrer' => $referrer,
    ];

    if ($user->is_loggedin()) {
        $tp['sender_email'] = $user->username;
    } else {
        $tp['sender_email'] = '';
    }

    normal_page('util-mail-draft.latte', $tp, ['HEAD-EXTRA' => $javascript_block]);
}

/**
 * @param $sender_email
 * @param $recipient_uid
 * @param $subject
 * @param $text
 * @param $orig_referrer
 * @throws InternalError
 * @throws SecurityError
 */
function process_mailer_form($sender_email, $recipient_uid, $subject, $text, $orig_referrer)
{
    // repeat the browser-side checking
    if (strlen($sender_email) < 5
            || strpos($sender_email, "@") == false
            || strlen($subject) == 0
            || strlen($text) < 6) {
        throw new InternalError('Malformed mailer request');
    }

    // send an email to the recipient
    $website = get_config('website');
    $admin_email = get_config('email-admin');
    $uinfo = User::user_info_from_uid($recipient_uid);

    $text .= <<<HEREDOC
    
***********
This email was sent from a user on $website.

Note that your email address was not visible to the sender.
If you have any problems or complaints about this email, please contact:

    $admin_email
***********
HEREDOC;


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

        $mail->setFrom($sender_email);
        $mail->addAddress($uinfo['username'], $uinfo['fullname']);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $text;

        $mail->send();
    } catch (Exception $e) {
        rollback();
        throw new InternalError("could not send email - $mail->ErrorInfo");
    }

    // redirect to post-mailer page
    $tp = [
        'referrer' => $orig_referrer,
    ];

    normal_page('util-mail-post.latte', $tp);
}

try {
    if (param_get_integer_opt('uid') != '') {

        /* This is a request to display the registration form */
        $recipient_uid = param_get_integer('uid');

        $referrer = $_SERVER["HTTP_REFERER"];
        if (!isset($referer)) {
            $referrer = get_config('website-url');
        }

        display_mailer_form($recipient_uid, $referrer);

    } else if (param_post_string_opt('sender_email') != '') {

        /* This is a mailer form submittal */
        $sender_email = param_post_string('sender_email');
        $recipient_uid = param_post_integer('uid');
        $subject = param_post_string('subject');
        $text = param_post_string('text');
        $orig_referrer = param_post_string('orig_referrer');

        process_mailer_form($sender_email, $recipient_uid, $subject, $text, $orig_referrer);

    } else {
        throw new InternalError("invalid request");
    }
} catch (\Exception $e) {
    report_error($e);
}
