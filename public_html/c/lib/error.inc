<?php

function error_page($message, $url=NULL)
{
    global $BASE_PATH;

    if (!isset($url)) {
        $url = get_config('website-url');
    }

    $title = 'Error';

    $tp = [
        'title' => $title,
        'text' => $message,
        'continue_url' => $url,
    ];

    $template = "$BASE_PATH/c/tpl/page-error.latte";

    $latte = new Latte\Engine;
    display_page($title, $latte->renderToString($template, $tp));
}

function noperm_page()
{
    global $BASE_PATH;

    $title = 'Error';

    $tp = [
        'title' => $title,
    ];

    $template = "$BASE_PATH/c/tpl/page-noperm.latte";

    $latte = new Latte\Engine;
    display_page($title, $latte->renderToString($template, $tp));
}

class UserError extends Exception { }
class InternalError extends Exception { }

function report_error($e, $url=NULL)
{
    if ($e instanceOf UserError) {
        $msg = 'Error: ' . $e->getMessage();
    } else if ($e instanceOf InternalError) {
        $msg = 'Internal Error: ' . $e->getMessage() . "\n";
        # $msg .= $e->getTraceAsString();
    } else {
        $msg = 'Unknown Error: ' . $e->getMessage() . "\n";
        # $msg .= $e->getTraceAsString();
    }
    error_page($msg, $url);
}

?>
