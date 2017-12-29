<?php

require_once "site.inc";

$title = "ARHS Bulletin Search Results";

$title_keywords = quote_external(get_post("titlekeywords"));
$title_join = quote_external(get_post("titlejoin"));
$author_keywords = quote_external(get_post("authorkeywords"));
$synopsis_keywords = quote_external(get_post("synopsiskeywords"));
$synopsis_join = quote_external(get_post("synopsisjoin"));
$volume = quote_external(get_post("volume"));
$volume_type = quote_external(get_post("volumetype"));
$issue = quote_external(get_post("issue"));
$month = quote_external(get_post("month"));
$year = quote_external(get_post("year"));

$t = new HTML_Template_ITX(".");
$t->loadTemplateFile("bulletin_results.tpl");

global $dbi;

$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
    "Sep", "Oct", "Nov", "Dec");

$where_clause = "";

if ($title_keywords)
{
    #
    # Include title keywords in seach
    #
    $list = explode(" ", $title_keywords);
    for ($i = 0; $i < count($list); $i++)
        $list[$i] = "locate(lower('$list[$i]'), lower(BI.title)) != 0";

    if ($title_join == "Any of")
        $subclause = implode(" or ", $list);
    else
        $subclause = implode(" and ", $list);

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($author_keywords)
{
    #
    # Include author keywords in seach
    #
    $list = explode(" ", $author_keywords);
    for ($i = 0; $i < count($list); $i++)
        $list[$i] = "locate(lower('$list[$i]'), lower(BI.author)) != 0";

    $subclause = implode(" and ", $list);

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($synopsis_keywords)
{
    #
    # Include synopsis keywords in seach
    #
    $list = explode(" ", $synopsis_keywords);
    for ($i = 0; $i < count($list); $i++)
        $list[$i] = "locate(lower('$list[$i]'), lower(BI.notes)) != 0";

    if ($synopsis_join == "Any of")
        $subclause = implode(" or ", $list);
    else
        $subclause = implode(" and ", $list);

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($volume)
{
    #
    # Include volume in search
    #
    $subclause = "BI.volume = $volume";

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($issue)
{
    #
    # Include issue in search
    #
    $subclause = "BI.issue = $issue";

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($month)
{
    #
    # Include month in search
    #
    $subclause = "BI.month = $month";

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($year)
{
    #
    # Include year in search
    #
    $subclause = "BI.year = $year";

    if ($where_clause)
        $where_clause = "$where_clause and ";
    $where_clause = "$where_clause( $subclause )";
}

if ($where_clause)
    $where_clause = "where $where_clause";

$stmt = $dbi->stmt_init();

$stmt->prepare("
    select
        BI.title,
        BI.author,
        BI.notes,
        BI.volume,
        BI.issue,
        BI.month,
        BI.year,
        BI.pages
    from
        r_bulletin_index BI
    $where_clause
    order by
        BI.seqno
    limit
        200
")
   or dbi_error_trace("prepare failed");

$stmt->execute();
$stmt->bind_result($title, $author, $notes, $volume, $issue, $month, $year,
    $pages);

while ($stmt->fetch())
{
    $title = htmlentities($title);
    $notes = htmlentities($notes);

    $t->setCurrentBlock("ARTICLE");
    $t->setVariable("ART_TITLE", $title);
    $t->setVariable("AUTHOR", $author);
    if ($notes)
        $t->setVariable("TEXT", $notes);
    $t->setVariable("VOLUME", $volume);
    $t->setVariable("ISSUE", $issue);
    $t->setVariable("MONTH", $months[$month-1]);
    $t->setVariable("YEAR", $year);
    $t->setVariable("PAGES", $pages);
    $t->parseCurrentBlock();
}
$stmt->close();

$t->setCurrentBlock("CONTENT");
$t->setVariable("TITLE", $title);
$t->setVariable("QUERY", "$where_clause");
$t->parseCurrentBlock();

display_page($title, $t->get("CONTENT"));

?>
