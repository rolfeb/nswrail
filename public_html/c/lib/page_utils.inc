<?php

/*
 * Return the HTML for the header bar (logo + login)
 */
function get_headerbar_html($user, $nologin = false)
{
    global $BASE_PATH;

    $template_dir   = "$BASE_PATH/c/tpl";

    $t = new HTML_Template_ITX($template_dir);
    if (!$t->loadTemplateFile("headerbar.tpl", true, true))
    {
        return "<!-- ERROR: couldn't open $template_dir/headerbar.tpl -->\n";
    }

    if (!$user->is_guest())
    {
        $t->setCurrentBlock("AUTHENTICATED-USER");
        $t->setVariable("USERNAME", $user->fullname);
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
    global $BASE_PATH, $user;

    $template_dir   = "$BASE_PATH/c/tpl";
    $config_file    = "$BASE_PATH/c/menu.cfg";

    $t = new HTML_Template_ITX($template_dir);
    if (!$t->loadTemplateFile("menu.tpl", true, true)) {
        return "<!-- ERROR: couldn't open $template_dir/menu.tpl -->\n";
    }

    $menus = array();

    $re = '{^(\d+)([LEMS]?)\|([^\|]*)\|([^\|]*)}';

    $has_role = [
        NULL  => True,
        'L' => $user->is_loggedin(),
        'E' => $user->is_editor(),
        'M' => $user->is_moderator(),
        'S' => $user->is_editor(),
    ];

    if ($fp = fopen($config_file, "r")) {
        while ($line = fgets($fp)) {
            $line = trim($line);
            if (preg_match($re, $line, $matches)) {
                list($dummy, $type, $role, $text, $url) = $matches;

                if ($type == 0) {
                    # this is a new menu item or drop-down
                    $m = [
                        'text' => $text,
                        'url' => $url,
                        'role' => $role,
                        'dropdown' => array(),
                    ];
                    array_push($menus, $m);
                } elseif ($type == 1) {
                    # add to current drop-down
                    $e = [
                        'text' => $text,
                        'url' => $url,
                        'role' => $role,
                    ];
                    $n = count($menus);
                    array_push($menus[$n-1]['dropdown'], $e);
                }
            }
        }
        fclose($fp);

        foreach ($menus as $m) {
            if (!$has_role[$m['role']]) {
                continue;
            }

            if (count($m['dropdown']) == 0) {
                $t->setVariable('ITEM-URL', $m['url']);
                $t->setVariable('ITEM-NAME', $m['text']);
                $t->parse("ITEM");
            } else {
                $t->setVariable('DROPDOWN-NAME', $m['text']);
                foreach ($m['dropdown'] as $e) {
                    if (!$has_role[$e['role']]) {
                        continue;
                    }
                    $t->setVariable('ITEM-URL', $e['url']);
                    $t->setVariable('ITEM-NAME', $e['text']);
                    $t->parse("DROPDOWN-ITEM");
                }
                $t->parse("DROPDOWN");
            }
            $t->parse("ITEM-OR-DROPDOWN");
        }

        return $t->get();
    }
    else {
        return "<!-- ERROR: couldn't open $config_file -->\n";
    }
}

/*
 * Display the given content in a standard page, defined by page-template.tpl.
 */
function display_page($title, $content, $args = null)
{
    global $BASE_PATH;
    global $user;

    $p = new HTML_Template_ITX("$BASE_PATH/c/tpl");
    $p->loadTemplateFile("page-template.tpl");

    $set_dtd = 1;
    $basic_page = 0;

    if (is_array($args)) {
        if (array_key_exists("HEAD-EXTRA", $args)) {
            $p->setVariable("HEAD-EXTRA", $args["HEAD-EXTRA"]);
        }

        if (array_key_exists("BODY-EXTRA", $args)) {
            $p->setVariable("BODY-EXTRA", $args["BODY-EXTRA"]);
        }

        if (array_key_exists("BANNER", $args)) {
            $p->setVariable("PAGE-BANNER", $args["BANNER"]);
        }

        if (array_key_exists("BASIC-PAGE", $args)) {
            $basic_page = 1;
        }

        if (array_key_exists("TRANSITIONAL-DTD", $args)) {
            $p->setVariable("DTD-TYPE", 'Transitional');
            $p->setVariable("DTD-FILE", 'xhtml1-transitional.dtd');
            $set_dtd = 0;
        }
    }

    if ($set_dtd) {
        $p->setVariable("DTD-TYPE", 'Strict');
        $p->setVariable("DTD-FILE", 'xhtml1-strict.dtd');
    }

    $p->setVariable("PAGE-TITLE", $title);
    if (!$basic_page) {
        $p->setVariable("PAGE-HEADER", get_headerbar_html($user));
        $p->setVariable("PAGE-MENU", get_menubar_html());
    }
    $p->setVariable("PAGE-CONTENT", $content);
    $p->parseCurrentBlock();
    $p->show();
}

?>
