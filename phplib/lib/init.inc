<?php

/**
 * @throws SecurityError
 */
function init_request()
{
    /** @var mysqli $db */
    global $db;
    global $user;

    load_config();

    /*
     * Connect to the database
     */
    $name = get_config('db-name');
    $user = get_config('db-user');
    $password = get_config('db-password');
    $db = @new mysqli('localhost', $user, $password, $name);
    if ($db->connect_errno) {
        print('failed to connect to database: ' . $db->connect_errno);
        exit;
    }
    # force errors to be exceptions
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    /*
     * Manage browser caching
     */
    $server_timestamp = get_database_timestamp();
    $server_date = gmdate('D, d M Y H:i:s', $server_timestamp) . ' GMT';

    if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]))
    {
        $cached_date = preg_replace('/;.*$/', '',
            $_SERVER["HTTP_IF_MODIFIED_SINCE"]);

        if ($cached_date == $server_date)
        {
            header("HTTP/1.0 304 Not Modified");
            exit;
        }
    }

    if (! server_is_devel()) {
        header("Last-Modified: $server_date");
    }

    header("Content-type: text/html; charset=utf-8");

    /*
     * Manage user session
     */
    $s = session_status();
    if ($s == PHP_SESSION_NONE) {
        session_start();
    }

    /*
    session_start();
    */

    $user = new User($db);

    if (website_under_maintenance()) {
        exit;
    }
}

/**
 * @return int
 */
function website_under_maintenance()
{
    global $BASE_PATH;

    $file = $BASE_PATH . "/maintenance.dat";

    if (!file_exists($file)) {
        return 0;
    }

    $message = implode('<br>', file($file));

    if (!$message) {
        $message = "
Sorry!  This website is temporarily unavailable, due to maintenance.
<p>
It will be back shortly.
";
    }

    error_page($message, "/");

    return 1;
}

function server_is_devel()
{
    return (
        $_SERVER["SERVER_NAME"] == "nswrail-devel"
        ||
        $_SERVER["SERVER_NAME"] == "nswrail-devel.bozier.net"
    );
}

function server_is_stage()
{
    return
    (
        $_SERVER["SERVER_NAME"] == "nswrail-stage"
        ||
        $_SERVER["SERVER_NAME"] == "nswrail-stage.bozier.net"
    );
}

function server_is_production()
{
    return ($_SERVER["SERVER_NAME"] != "nswrail-proto"
        && $_SERVER["SERVER_NAME"] != "nswrail-stage");
}

function get_database_timestamp()
{
    /** @var mysqli $db */
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            DB.last_update
        from
            r_website DB
    ");
        
    $stmt->execute();
    $stmt->bind_result($timestamp);

    if (!$stmt->fetch()) {
        $timestamp = 0;
    }

    $stmt->close();

    return $timestamp;
}
