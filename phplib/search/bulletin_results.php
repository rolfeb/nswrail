<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

require "site.inc";

/**
 * @return array
 * @throws SecurityError
 */
function run_search_bulletin_results()
{
    /** @var mysqli $db */
    global $db;

    $tp = [
        'title' => "ARHS Bulletin Search Results",
    ];

    $title_keywords = param_get_string_opt("titlekeywords");
    $title_join = param_get_string_opt("titlejoin");
    $author_keywords = param_get_string_opt("authorkeywords");
    $synopsis_keywords = param_get_string_opt("synopsiskeywords");
    $synopsis_join = param_get_string_opt("synopsisjoin");
    $volume = param_get_integer_opt("volume");
    # $volume_type = param_get_string_opt("volumetype");
    $issue = param_get_integer_opt("issue");
    $month = param_get_integer_opt("month");
    $year = param_get_integer_opt("year");


    $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug",
        "Sep", "Oct", "Nov", "Dec");

    $where_clause = '';
    $bind_types = '';
    $match_sql = [];
    $bind_params = [];

    if ($title_keywords) {
        #
        # Include title keywords in seach
        #
        $keyword_list = explode(" ", strtolower($title_keywords));

        for ($i = 0; $i < count($keyword_list); $i++) {
            $match_sql[] = 'locate(?, lower(BI.title)) != 0';
            $bind_params[] = &$keyword_list[$i];
            $bind_types = $bind_types . 's';
        }

        if ($title_join == 'Any of') {
            $subclause = implode(" or ", $match_sql);
        } else {
            $subclause = implode(" and ", $match_sql);
        }

        if ($where_clause) {
            $where_clause = "$where_clause and ";
        }
        $where_clause = "$where_clause( $subclause )";
    }

    if ($author_keywords) {
        #
        # Include author keywords in seach
        #
        $keyword_list = explode(" ", strtolower($author_keywords));

        for ($i = 0; $i < count($keyword_list); $i++) {
            $match_sql[] = 'locate(?, lower(BI.author)) != 0';
            $bind_params[] = &$keyword_list[$i];
            $bind_types = $bind_types . 's';
        }

        $subclause = implode(" and ", $match_sql);

        if ($where_clause) {
            $where_clause = "$where_clause and ";
        }
        $where_clause = "$where_clause( $subclause )";
    }

    if ($synopsis_keywords) {
        #
        # Include synopsis keywords in seach
        #
        $keyword_list = explode(" ", strtolower($synopsis_keywords));

        for ($i = 0; $i < count($keyword_list); $i++) {
            $match_sql[] = 'locate(?, lower(BI.notes)) != 0';
            $bind_params[] = &$keyword_list[$i];
            $bind_types = $bind_types . 's';
        }

        if ($synopsis_join == "Any of")
            $subclause = implode(" or ", $match_sql);
        else
            $subclause = implode(" and ", $match_sql);

        if ($where_clause)
            $where_clause = "$where_clause and ";
        $where_clause = "$where_clause( $subclause )";
    }

    if ($volume !== NULL) {
        #
        # Include volume in search
        #
        $subclause = 'BI.volume = ?';
        $bind_params[] = &$volume;
        $bind_types = $bind_types . 'i';

        if ($where_clause)
            $where_clause = "$where_clause and ";
        $where_clause = "$where_clause( $subclause )";
    }

    if ($issue !== NULL) {
        #
        # Include issue in search
        #
        $subclause = "BI.issue = ?";
        $bind_params[] = &$issue;
        $bind_types = $bind_types . 'i';

        if ($where_clause)
            $where_clause = "$where_clause and ";
        $where_clause = "$where_clause( $subclause )";
    }

    if ($month !== NULL) {
        #
        # Include month in search
        #
        $subclause = "BI.month = ?";
        $bind_params[] = &$month;
        $bind_types = $bind_types . 'i';

        if ($where_clause)
            $where_clause = "$where_clause and ";
        $where_clause = "$where_clause( $subclause )";
    }

    if ($year !== NULL) {
        #
        # Include year in search
        #
        $subclause = "BI.year = ?";
        $bind_params[] = &$year;
        $bind_types = $bind_types . 'i';

        if ($where_clause)
            $where_clause = "$where_clause and ";
        $where_clause = "$where_clause( $subclause )";
    }

    array_unshift($bind_params, $bind_types);

    if ($where_clause)
        $where_clause = "where $where_clause";

    $stmt = $db->stmt_init();
    /** @noinspection SyntaxError */
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
            201
    ");

    call_user_func_array(array($stmt, 'bind_param'), $bind_params);

    $stmt->execute();
    $stmt->bind_result($art_title, $author, $notes, $volume, $issue, $month, $year,
        $pages);

    $rows = [];
    while ($stmt->fetch()) {
        $row = [
            'month' => $months[$month-1],
            'year' => $year,
            'volume' => $volume,
            'issue' => $issue,
            'article_title' => $art_title,
            'author' => $author,
            'pages' => $pages,
        ];
        if ($notes) {
            $row['opt_text'] = $notes;
        }
        
        $rows[] = $row;
    }
    $stmt->close();

    $nrows = count($rows);
    if ($nrows > 0) {
        if ($nrows > 200) {
            $tp['opt_warning'] = "Too many matches; results have been truncated";
            array_splice($rows, 200);
        }
        $tp['opt_results'] = $rows;
    } else {
        $tp['opt_warning'] = "No results matched the search criteria.";
    }

    return $tp;
}

normal_page_wrapper('run_search_bulletin_results', 'search-bulletin-results.latte');
