<?php

/*
 * Return the HTML for the header bar (logo + login)
 */
function get_headerbar_html($nologin = false)
{
    global $BASE_PATH;

    $template_dir   = "$BASE_PATH/c/tpl";

    $t = new HTML_Template_ITX($template_dir);
    if (!$t->loadTemplateFile("headerbar.tpl", true, true))
    {
        return "<!-- ERROR: couldn't open $template_dir/headerbar.tpl -->\n";
    }

    if (auth_loggedin())
    {
        $t->setCurrentBlock("AUTHENTICATED-USER");
        $t->setVariable("USERNAME", auth_username());
        $t->parseCurrentBlock();
    }
    else
    {
        $t->touchBlock("UNAUTHENTICATED-USER");
    }
    return $t->get();
}

/*
 * Return the HTML code for the menu bar
 */
function get_menubar_html()
{
    global $BASE_PATH;

    $template_dir   = "$BASE_PATH/c/tpl";
    $config_file    = "$BASE_PATH/l/cfg/menu.cfg";

    $t = new HTML_Template_ITX($template_dir);
    if (!$t->loadTemplateFile("menu.tpl", true, true))
    {
        return "<!-- ERROR: couldn't open $template_dir/menu.tpl -->\n";
    }

    if ($fp = fopen($config_file, "r"))
    {
        while ($buf = fgets($fp))
        {
            $buf = chop($buf);
            $arr = explode('|', $buf);
            if (count($arr) == 3)
            {
                if ($arr[0] == 0)
                {
                    $t->parse("TAB");
                    if ($arr[0] == "0L")
                        $t->setVariable("TAB-ID", "last");
                    $t->setVariable("TAB-NAME", $arr[1]);
                    $t->setVariable("TAB-URL", $arr[2]);
                }
                elseif
                (
                    $arr[0] == 1
                    ||
                    $arr[0] == 2 && auth_priv_admin()
                )
                {
                    $prefix = $arr[0] == 2 ? '*' : '';
                    $t->setVariable("ITEM-NAME", "$prefix$arr[1]");
                    $t->setVariable("ITEM-URL", $arr[2]);
                    $t->parse("ITEM");
                }

            }
        }
        fclose($fp);

        $t->parse("TAB");
        return $t->get();
    }
    else
    {
        return "<!-- ERROR: couldn't open $config_file -->\n";
    }
}

/*
 * Display the given content in a standard page, defined by page-template.tpl.
 */
function display_page($title, $content, $args = null)
{
    global $BASE_PATH;

    $p = new HTML_Template_ITX("$BASE_PATH/c/tpl");
    $p->loadTemplateFile("page-template.tpl");

    $set_dtd = 1;
    $basic_page = 0;

    if (is_array($args))
    {
        if (array_key_exists("HEAD-EXTRA", $args))
            $p->setVariable("HEAD-EXTRA", $args["HEAD-EXTRA"]);

        if (array_key_exists("BODY-EXTRA", $args))
            $p->setVariable("BODY-EXTRA", $args["BODY-EXTRA"]);

        if (array_key_exists("BANNER", $args))
            $p->setVariable("PAGE-BANNER", $args["BANNER"]);

        if (array_key_exists("BASIC-PAGE", $args))
            $basic_page = 1;

        if (array_key_exists("TRANSITIONAL-DTD", $args))
        {
            $p->setVariable("DTD-TYPE", 'Transitional');
            $p->setVariable("DTD-FILE", 'xhtml-transitional.dtd');
            $set_dtd = 0;
        }
    }

    if ($set_dtd)
    {
        $p->setVariable("DTD-TYPE", 'Strict');
        $p->setVariable("DTD-FILE", 'xhtml-strict.dtd');
    }

    $p->setVariable("PAGE-TITLE", $title);
    if (!$basic_page)
    {
        $p->setVariable("PAGE-HEADER", get_headerbar_html());
        $p->setVariable("PAGE-MENU", get_menubar_html());
    }
    $p->setVariable("PAGE-CONTENT", $content);
    $p->parseCurrentBlock();

    $p->show();
}

?>
