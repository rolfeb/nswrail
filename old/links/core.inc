<?php

require_once "../init.inc";
require_once "../util.inc";

function show_links($title, $intro, $links)
{
    $verify = quote_external(get_post("verify"));

    $t = new HTML_Template_ITX(".");
    $t->loadTemplateFile("core.tpl");
    $t->setCurrentBlock("CONTROLS");
    $t->setVariable("TOP", top());
    $t->setVariable("MENU", menu());
    $t->parseCurrentBlock();

    usort($links, "by_text");

    foreach ($links as $l)
    {
        $t->setCurrentBlock("LINK");
        $t->setVariable("URL", $l[0]);
        $t->setVariable("TEXT", htmlentities($l[1]));
        $t->setVariable("DESCRIPTION", $l[2]);

        if ($verify)
        {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $l[0]);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_HEADER, 0);
            curl_exec($c);
            $errnum = curl_errno($c);
            curl_close($c);

            $t->setVariable("STATUS", $errnum != "0" ? "[<b>BROKEN</b>]" : "[OK]");
        }

        $t->parseCurrentBlock();
    }

    $t->setCurrentBlock("MAIN");
    $t->setVariable("INTRODUCTION", htmlentities($intro));
    $t->setVariable("TITLE", htmlentities($title));
    $t->parseCurrentBlock();

    $t->show();
}

function by_text($a, $b)
{
    return strcmp($a[1], $b[1]);
}

?>
