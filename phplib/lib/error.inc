<?php

function error_page($message, $url=NULL)
{
    if (!isset($url)) {
        $url = get_config('website-url');
    }

    $tp = [
        'title' => 'Error',
        'text' => $message,
        'continue_url' => $url,
    ];

    normal_page('page-error.latte', $tp);
}

function noperm_page()
{
    $tp = [
        'title' => 'Error',
    ];

    normal_page('page-noperm.latte', $tp);
}

class UserError extends Exception { }
class InternalError extends Exception { }
class SecurityError extends Exception { }

function report_error($e, $url=NULL)
{
    if ($e instanceOf UserError) {
        $msg = 'Error: ' . $e->getMessage();
    } else if ($e instanceOf InternalError) {
        $msg = 'Internal Error: ' . $e->getMessage() . "\n";
        # $msg .= $e->getTraceAsString();
    } else {
        $msg = 'Unknown Error: ' . $e->getMessage() . "\n";
        $msg .= $e->getTraceAsString();
    }
    error_page($msg, $url);
}

?>
