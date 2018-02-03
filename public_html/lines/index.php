<?php

require "site.inc";

function define_tab($t, $label, $id, $is_active)
{
    $t->setCurrentBlock("NAV-TAB");
    $t->setVariable("LABEL", $label);
    $t->setVariable("ID", $id);
    $t->setVariable("ACTIVE", $is_active ? "active" : "");
    $t->parseCurrentBlock();
}

function print_category($t, $category, $params)
{
    global $db;

    list($state, $region, $traffic) = $params;

    if (!is_null($traffic)) {
        $where_extra = ' and R.traffic = ?';
    } else {
        $where_extra = '';
    }

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            R.line_name,
            R.description
        from
            r_line R
        where
            R.line_state = ?
            and
            R.region = ? $where_extra
        order by
            R.description
    ")
        or dbi_error_trace("prepare failed");

    if (!is_null($traffic)) {
        $stmt->bind_param("sss", $state, $region, $traffic);
    } else {
        $stmt->bind_param("ss", $state, $region);
    }

    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($line, $description);

    while ($stmt->fetch())
    {
        $line_class = get_line_status_class($state, $line);

        $t->setCurrentBlock("LINE");
        $t->setVariable("LINE-URL", "show.php?" . urlenc("name=$state:$line"));
        $t->setVariable("LINE-NAME", $description);
        $t->setVariable("LINE-CLASS", "line-$line_class");
        $t->parseCurrentBlock();
    }
    $stmt->close();

    $t->setCurrentBlock("CATEGORY");
    $t->setVariable("CATEGORY-NAME", $category);
    $t->parseCurrentBlock();
}

function fill_tab($t, $tab_id, $layout, $is_active)
{
    $t->setCurrentBlock("TAB-PANE");
    $t->setVariable("ACTIVE", $is_active ? "active" : "");
    $t->setVariable("ID", $tab_id);

    foreach ($layout as $column) {
        foreach ($column as $category) {
            list($heading, $params) = $category;
            print_category($t, $heading, $params);
        }
        $t->parse("COLUMN");
    }

    $t->parse("TAB-PANE");
}

$title = "NSW Railway Network";

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("index.tpl", true, true);

$lines_nsw = [
    [
        [ 'Trunk Lines',        [ 'NSW', 'T', NULL ] ],
        [ 'Northern Region',    [ 'NSW', 'N', NULL ] ],
    ],
    [
        [ 'Southern Region',    [ 'NSW', 'S', NULL ] ],
    ],
    [
        [ 'Western Region',     [ 'NSW', 'W', NULL ] ],
        [ 'Victorian',          [ 'VIC', 'W', NULL ] ],
        [ 'South Australian',   [ 'SA', 'T', NULL ] ],
        [ 'Queensland',         [ 'QLD', 'S', NULL ] ],
    ],
];

$lines_sydney = [
    [
        [ 'Passenger Lines',    [ 'NSW', 'SY', 'PASS' ] ],
    ],
    [
        [ 'Freight Lines',      [ 'NSW', 'SY', 'GOODS' ] ],
    ],
    [
        [ 'Coal Lines',         [ 'NSW', 'SY', 'COAL' ] ],
    ],
];

$lines_newcastle = [
    [
        [ 'Passenger Lines',    [ 'NSW', 'NC', 'PASS' ] ],
        [ 'Freight Lines',      [ 'NSW', 'NC', 'GOODS' ] ],
    ],
    [
        [ 'South Maitland Railway', [ 'NSW', 'NC', 'SMR' ] ],
    ],
    [
        [ 'Colliery Lines',     [ 'NSW', 'NC', 'COAL' ] ],
    ],
];

# define the tabs
define_tab($t, 'NSW regional', 'nsw', true);
define_tab($t, 'Sydney network', 'sydney', false);
define_tab($t, 'Newcastle network', 'newcastle', false);

fill_tab($t, 'nsw', $lines_nsw, true);
fill_tab($t, 'sydney', $lines_sydney, false);
fill_tab($t, 'newcastle', $lines_newcastle, false);

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
