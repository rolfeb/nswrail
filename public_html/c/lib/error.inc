<?php

function error_page($message, $url=NULL)
{
    global $BASE_PATH;

    if (!isset($url)) {
        $url = get_config('website-url');
    }

    $template_dir   = "$BASE_PATH/c/tpl";

    $t = new HTML_Template_ITX($template_dir);
    if (!$t->loadTemplateFile("error.tpl", true, true))
        return "<!-- ERROR: couldn't open error.tpl -->\n";

    $t->setCurrentBlock('CONTENT');
    $t->setVariable('TEXT', $message);
    $t->setVariable('URL', $url);
    $t->parseCurrentBlock();

    display_page("ERROR", $t->get("CONTENT"));
    exit();
}

function noperm_page()
{
    global $BASE_PATH;

    $template_dir   = "$BASE_PATH/c/tpl";

    $t = new HTML_Template_ITX($template_dir);
    if (!$t->loadTemplateFile("noperm.tpl", true, true))
        return "<!-- ERROR: couldn't open noperm.tpl -->\n";

    $t->setCurrentBlock('CONTENT');
    $t->touchBlock('CONTENT');
    $t->parseCurrentBlock();

    display_page("ERROR", $t->get("CONTENT"));
    exit();
}

class UserError extends Exception { }
class InternalError extends Exception { }

function report_error($e)
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
    error_page($msg);
}

?>
