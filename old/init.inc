<?php
/*
 * Perform host-specific stuff
 */
$uname = posix_uname();
$host = $uname["nodename"];

if (preg_match('/\.digitalpacific\.com\.au$/', $host))
{
    $db_database = "nswrail_net_-_raildb";
    $db_user = "rolfeb_dbuser";
    $db_password = "rAj_I3uBLMV3";

    ini_set("include_path",
        "/home/rolfeb/pear/lib:.:/php/includes:/usr/share/php");
}
else
{
    $db_database = "raildb";
    $db_user = "rolfe";
    $db_password = "";

    ini_set("include_path",
        "/work/pear/lib:" . ini_get("include_path"));
}

/*
 * Connect to the database
 */
$db = mysql_connect("localhost", $db_user, $db_password);
if (!$db)
    die("failed to connect to MySQL: " . mysql_error() . "\n");

if (!mysql_select_db($db_database, $db))
    die("failed to connect to database $db_database: " . mysql_error() . "\n");

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

if (! server_is_devel())
    header("Last-Modified: $server_date");

header("Content-type: text/html; charset=utf-8");

/*
 * Manage user session
 */
// session_start();

/*
 * Load standard modules
 */
require_once "HTML/Template/ITX.php";
require_once "top.inc";
require_once "auth.inc";
require_once "error.inc";
require_once "dbutil0.inc";
require_once "menu.inc";

if (website_under_maintenance())
    exit;

/* DONE INITIALIZATION */

function website_under_maintenance()
{
    $file = "/home/rolfeb/maintenance.dat";
    if (!file_exists($file))
    $file = "maintenance.dat";

    if (!file_exists($file) or !($fp = fopen($file, "r")))
        return 0;

    $message = fgets($fp, 1024);
    fclose($fp);

    if (!$message)
    {
        $message = "
Sorry!  The NSWrail.net website is temporarily unavailable, due to
maintenance.
<p>
It will be back shortly.
";
    }

    print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
     \"http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en-US\" xml:lang=\"en-US\">
<head>
<title>NSWrail.net</title>
<link rev=\"made\" href=\"mailto:nswrail@pobox.com\" />
<meta name=\"keywords\" content=\"NSW railway history photographs maps\" />
<link type=\"image/gif\" rel=\"shortcut icon\" href=\"/images/railicon.gif\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"/common.css\" />
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<script type=\"text/javascript\" src=\"/lib/menu.js\" />
</head>
<body onload=\"fixmenu\">
";

    require_once "top.inc";
    print top();

    print "
<div class=\"message\">
$message
</div>
</body>
</html>
";

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
        if ($location == "maps")
        {
            // return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxSk-ve8lUVhz_jfoFqu7H39RrMPLBTntoaXUBhvI89eew6OXwDCy7BKXQ";
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxS0BZHEMAfXVUcYUNN9_uC_Ei-I6xSc0Xqbi9aEhg-JXpzWWJY-OwLg0A";
        }
        else
        if ($location == "locations")
        {
            // return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxQPbPKoKEYtUyCM8xthwZKEKzT_sxT6DO-hvVCUJ6yo6vanoPf0EQXPmQ";
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxRJYI67ThsExT-oAfFgYUx8p7sE5xT-oWVNdffL51p0duxAbk_cwGjvdg";
        }
    }
    else
    if (server_is_stage())
    {
        if ($location == "maps")
        {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxSZ-oZ6oywsDBg_sy7PqfzXPwC0VBRzx_UD8hueNJk95UxeeO9jSQex9A";
        }
        else
        if ($location == "locations")
        {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxRersMDMga1cOoMrVkx50FIisu9zBSozVm4mz11I3v4ih1D4OnJ5fogAQ";
        }
    }
    else
    if (server_is_production())
    {
        if ($location == "maps")
        {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxSU2o1OzxMh1LriO7Z1oON-AG6IWxStXzYffV1YeZuIMlYGZCJ9YbAQQQ";
        }
        else
        if ($location == "locations")
        {
            return "ABQIAAAAIIiL9HmMMMPPocEuM_uMFxTTgtNCIP5w8vsOjyJhD2lQ5TsBvxRHonriHmfxlaLuIuU_xs_xgGVZ-g";
        }
    }
}

function get_database_timestamp()
{
    global $db;

    $stmt = mysql_query("
        select
            DB.last_update
        from
            r_website DB
    ", $db)
        or die("prepare failed: " . mysql_error() . "\n");

    $timestamp = 0;
    if ($row = mysql_fetch_array($stmt))
        list($timestamp) = $row;

    mysql_free_result($stmt);

    return $timestamp;
}

?>
