<?php

/*
 * Convert date components into human-readable form
 */
function date_cpts2text($day, $month, $year, $year_error = 0)
{
    $months = array("?", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul",
        "Aug", "Sep", "Oct", "Nov", "Dec");
    $date = "unknown";

    $month += 0;

    if ($day)
        $date = "$day-$months[$month]-$year";
    elseif ($month)
        $date = "$months[$month]-$year";
    elseif ($year)
    {
        if ($year_error == 2)
            $date = sprintf("%d0s", $year / 10);
        else
            $date = $year;
    }

    if ($year_error == -1)
        $date = "before $date";
    elseif ($year_error == -2)
        $date = "after $date";
    elseif ($year_error == 1)
        $date = "circa $date";

    return $date;
}

function locn_type2text($type)
{
    $lookup = array(
        "station"       => "Station",
        "platform"      => "Platform",
        "junction"      => "Junction",
        "siding"        => "Siding",
        "loop"          => "Loop",
        "dead end"      => "Dead End",
        "halt"          => "Halt",
        "colliery"      => "Colliery",
        "yard"          => "Yard",
        "tunnel"        => "Tunnel",
        "tank"          => "Tank",
        "border"        => "Border",
        "crossing"      => "Crossing",
        "mine"          => "Mine",
        "other"         => "Other",
        "unknown"       => "Unknown",
    );

    return array_key_exists($type, $lookup) ? $lookup[$type] : "Unknown";
}

function locn_typelist()
{
    return array("station", "platform", "junction", "siding", "loop",
        "dead end", "halt", "colliery", "yard", "tunnel", "tank", "border",
        "crossing", "mine", "other", "unknown");
}

function locn_status2text($status)
{
    $lookup = array(
        "unopened"      => "Incomplete",
        "not opened"    => "Not Opened",
        "in use"        => "In Use",
        "reused"        => "Re-used",
        "closed"        => "Closed",
        "unknown"       => "Unknown",
        "-"             => "-",
    );

    return array_key_exists($status, $lookup) ? $lookup[$status] : "Unknown";
}

function locn_statuslist()
{
    return array("unknown", "not opened", "in use", "reused", "closed");
}

function locn_eventtypelist()
{
    return array("", "ON", "RN", "MV", "CN", "PL");
}

function date_errorlist()
{
    return array(0, 1, 2, -1, -2);
}

function locn_fulltitle($location, $type)
{
    if ($type == "station" or $type == "halt")
        return "$location Station";
    else
    if ($type == "loop" and !ereg("Loop", $location))
        return "$location Loop";
    else
    if ($type == "siding" and !ereg("Siding", $location))
        return "$location Siding";
    else
    if ($type == "platform" and !ereg("Platform", $location)
        and !ereg("Siding", $location))
            return "$location Station";
    else
        return $location;
}

function tunnel_type2text($type)
{
    $lookup = array(
        "single"    => "Single",
        "double"    => "Double",
        "quad"      => "Quad",
    );

    return array_key_exists($type, $lookup) ? $lookup[$type] : "Unknown";
}

function tunnel_lengths2text($lengths)
{
    if ($lengths != "")
    {
        $lengths = preg_replace("/,/", "m,", $lengths);
        $lengths = preg_replace("/$/", "m", $lengths);
    }

    return $lengths;
}

/*
 * Mark up a block of text in HTML, preserving paragraphs
 */
function html_markup_textblock($text)
{
    if ($text == "")
        return "";

    $text = ereg_replace("(\r?\n){2,}", "</p>\n<p>", $text);
    $text = ereg_replace("\r?\n", " ", $text);

    return "<p>$text</p>";
}

function get_post($varname, $defval = NULL)
{
    if (array_key_exists($varname, $_POST))
        $v = $_POST[$varname];
    elseif (array_key_exists($varname, $_GET))
        $v = $_GET[$varname];
    else
        $v = $defval;
    return urldecode($v);
}

function post_only($varname, $defval = NULL)
{
    if (array_key_exists($varname, $_POST))
        $v = $_POST[$varname];
    else
        $v = $defval;
    return urldecode($v);
}


function quote_external($text)
{
    if (get_magic_quotes_gpc())
        $text = stripslashes($text);

    return mysql_real_escape_string($text);
}

function make_canonical_name($name)
{
    # replace _ with space
    $name = ereg_replace("_", " ", $name);

    # capitalize the first letter of each word
    $name = ucwords(strtolower($name));

    # fix for "Jct (xxx)":
    $name = preg_replace('/(\([a-z])/e', "strtoupper('$1')", $name);

    return $name;
}

function urlenc($string)
{
    return htmlentities(ereg_replace(" ", "+", $string), ENT_QUOTES, "UTF-8");
}

#
# Returns an array of changelog entries. Each element is a hash of date, text
# information.
#
function changelog($file)
{
    $text = "";
    $date = "";

    $log = array();

    foreach (file($file) as $v)
    {
        if (ereg("^#", $v) or preg_match("/^[ \t]*$/", $v))
            continue;

        if (!ereg("^[ \t]", $v))
        {
            #
            # $v is a date
            #
            if ($text != "")
            {
                # save current entry
                array_push($log, array("date" => $date, "text" => $text));
                $text = "";
            }

            $date_cpts = explode("/", $v);
            if (count($date_cpts) == 2)
            {
                $d = 0;
                list($m, $y) = $date_cpts;
            }
            else
                list($d, $m, $y) = $date_cpts;

            $date = date_cpts2text($d, $m, $y);
        }
        else
        {
            #
            # $v is a line of text
            #
            $v = ereg_replace("^[ \t]", "", $v);    # strip leading whitespace
            $text = $text . "$v ";
        }
    }
    if ($text != "")
    {
        # save current entry
        array_push($log, array("date" => $date, "text" => $text));
    }

    return $log;
}

function count_visitors()
{
    $file = "/home/rolfeb/visitors.dat";
    if (!file_exists($file))
        $file = "/work/nswrail/visitors.dat";
    if (!file_exists($file))
        $file = "visitors.dat";

    if (!file_exists($file) or !($fp = fopen($file, "r+")))
        return "???";

    $line = fgets($fp, 256);
    list ($date, $count) = explode(",", $line);
    $count += 1;
    rewind($fp);
    $str = sprintf("%s,%d\n", $date, $count);
    fputs($fp, $str);
    fclose($fp);

    return "$count";
}

?>
