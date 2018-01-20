<?php

function init_request()
{
    global $db, $user;

    load_config();

    /*
     * Connect to the database
     */
    $name = get_config('db-name');
    $user = get_config('db-user');
    $password = get_config('db-password');
    $db = @new mysqli('localhost', $user, $password, $name);
    if ($db->connect_errno) {
        error_page('failed to connect to database: ' . $db->connect_errno);
    }

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
    session_start();

    $user = new User($db);

    if (website_under_maintenance()) {
        exit;
    }
}

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

/*
 * Return the appropriate Google Maps API key
 */
function gmapkey($location)
{
    if (server_is_devel())
    {
        if ($location == "maps") {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxS0BZHEMAfXVUcYUNN9_uC_Ei-I6xSc0Xqbi9aEhg-JXpzWWJY-OwLg0A";
        } else if ($location == "locations") {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxRJYI67ThsExT-oAfFgYUx8p7sE5xT-oWVNdffL51p0duxAbk_cwGjvdg";
        }
    } elseif (server_is_stage()) {
        if ($location == "maps") {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxSZ-oZ6oywsDBg_sy7PqfzXPwC0VBRzx_UD8hueNJk95UxeeO9jSQex9A";
        } elseif ($location == "locations") {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxRersMDMga1cOoMrVkx50FIisu9zBSozVm4mz11I3v4ih1D4OnJ5fogAQ";
        }
    } elseif (server_is_production()) {
        if ($location == "maps") {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxSU2o1OzxMh1LriO7Z1oON-AG6IWxStXzYffV1YeZuIMlYGZCJ9YbAQQQ";
        } elseif ($location == "locations") {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxTTgtNCIP5w8vsOjyJhD2lQ5TsBvxRHonriHmfxlaLuIuU_xs_xgGVZ-g";
        }
    }
}

function get_database_timestamp()
{
    global $db;

    $stmt = $db->stmt_init();
    $stmt->prepare("
        select
            DB.last_update
        from
            r_website DB
    ")
        or dbi_error_trace("prepare failed");
        
    $stmt->execute();
    $stmt->bind_result($timestamp);

    if (!$stmt->fetch()) {
        $timestamp = 0;
    }

    $stmt->close();

    return $timestamp;
}

?>
