<?php

function add_header($tp)
{
    global $user;

    if (!$user->is_guest()) {
        $tp['opt_username'] = $user->fullname;
    }

    return $tp;
}

function add_menu($tp)
{
    global $BASE_PATH, $user;

    $config_file    = "$BASE_PATH/c/menu.cfg";

    $has_role = [
        NULL  => True,
        'L' => $user->is_loggedin(),
        'E' => $user->is_editor(),
        'M' => $user->is_moderator(),
        'S' => $user->is_editor(),
    ];

    if ($fp = fopen($config_file, "r")) {
        $menus = [];
        $re = '{^(\d+)([LEMS]?)\|([^\|]*)\|([^\|]*)}';

        while ($line = fgets($fp)) {
            $line = trim($line);
            if ($line == '' or $line[0] == '#') {
                continue;
            }
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

        $tp['menuitems'] = [];

        foreach ($menus as $m) {
            if (!$has_role[$m['role']]) {
                continue;
            }

            if (count($m['dropdown']) == 0) {
                $tp['menuitems'][] = [
                    'u_item' => [
                            'url' => $m['url'],
                            'name' => $m['text'],
                        ],
                ];
            } else {
                $items = [];
                foreach ($m['dropdown'] as $e) {
                    if (!$has_role[$e['role']]) {
                        continue;
                    }
                    $items[] = [
                        'url' => $e['url'],
                        'name' => $e['text'],
                    ];
                }
                $tp['menuitems'][] = [
                    'u_dropdown' => [
                            'name' => $m['text'],
                            'items' => $items,
                        ],
                ];
            }
        }
    }

    return $tp;
}

function display_page($title, $content, $args = null)
{
    global $BASE_PATH;

    $tp = [
        'title' => $title,
    ];

    $basic_page = false;
    if (is_array($args)) {
        if (array_key_exists("HEAD-EXTRA", $args)) {
            $tp['opt_head_extra'] = $args["HEAD-EXTRA"];
        }

        if (array_key_exists("BODY-EXTRA", $args)) {
            $tp['opt_body_extra'] = $args["BODY-EXTRA"];
        }

        if (array_key_exists("BASIC-PAGE", $args)) {
            $basic_page = true;
        }
    }

    if (!$basic_page) {
        $tp = add_header($tp);
        $tp = add_menu($tp);
    }
    $tp['page_content'] = $content;

    $latte = new Latte\Engine;
    $latte->render($_SERVER['TEMPLATE_DIR'] . "/page-normal.latte", $tp);
}

function normal_page($template, $parameters, $args = null)
{
    $template_dir = $_SERVER['TEMPLATE_DIR'];

    $c_latte = new Latte\Engine;
    $content = $c_latte->renderToString("$template_dir/$template", $parameters);

    $tp = [
        'title' => 'NSWrail.net',
        'page_content' => $content,
    ];

    if (array_key_exists('title', $parameters)) {
        $tp['title'] = $parameters['title'];
    };

    $basic_page = false;
    if (is_array($args)) {
        if (array_key_exists("HEAD-EXTRA", $args)) {
            $tp['opt_head_extra'] = $args["HEAD-EXTRA"];
        }

        if (array_key_exists("BODY-EXTRA", $args)) {
            $tp['opt_body_extra'] = $args["BODY-EXTRA"];
        }

        if (array_key_exists("BASIC-PAGE", $args)) {
            $basic_page = true;
        }
    }

    if (!$basic_page) {
        $tp = add_header($tp);
        $tp = add_menu($tp);
    }

    $latte = new Latte\Engine;
    $latte->render("$template_dir/page-normal.latte", $tp);
}

function normal_page_wrapper($run_function, $template, $args=NULL)
{
    try {
        normal_page($template, $run_function(), $args);
    } catch (\Exception $e) {
        report_error($e);
    }
}

?>
