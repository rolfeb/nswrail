<?php

function is_intstr($s)
{
    return filter_var($s, FILTER_VALIDATE_INT) !== FALSE;
}

function is_stringN($s, $n)
{
    if ($s == '') {
        return FALSE;
    }
    $x = explode(':', $s);
    if (count($x) != $n) {
        return FALSE;
    }
    foreach ($x as $item) {
        if (!is_string($item)) {
            return FALSE;
        }
    }
    return TRUE;
}

function is_string2($s) { return is_stringN($s, 2); }
function is_string3($s) { return is_stringN($s, 3); }

function check($key, $checker, $is_get, $is_optional)
{
    $dict = $is_get ? $_GET : $_POST;
    $method = $is_get ? "get" : "post";

    if (!array_key_exists($key, $dict)) {
        if ($is_optional) {
            if ($checker == 'is_intstr') {
                return NULL;
            } else if ($checker == 'is_string') {
                return '';
            } else if ($checker == 'is_string2') {
                return ':';
            } else if ($checker == 'is_string3') {
                return '::';
            }
        }
        throw new SecurityError("missing '$method' parameter: $key");
    }
    $v = urldecode($dict[$key]);
    if (!$checker($v)) {
        throw new SecurityError("bad type for '$method' parameter: $key=[$v]");
    }

    if ($checker == 'is_intstr') {
        return intval($v);
    }

    return $v;
}

function param_string($key, $is_get, $is_optional)
{
    return check($key, 'is_string', $is_get, $is_optional);
}

function param_integer($key, $is_get, $is_optional)
{
    return check($key, 'is_intstr', $is_get, $is_optional);
}

function param_string2($key, $is_get, $is_optional)
{
    return check($key, 'is_string2', $is_get, $is_optional);
}

function param_string3($key, $is_get, $is_optional)
{
    return check($key, 'is_string3', $is_get, $is_optional);
}

/* string */

function param_get_string($key)
{
    return param_string($key, TRUE, FALSE);
}

function param_get_string_opt($key)
{
    return param_string($key, TRUE, TRUE);
}

function param_post_string($key)
{
    return param_string($key, FALSE, FALSE);
}

function param_post_string_opt($key)
{
    return param_string($key, FALSE, TRUE);
}

/* integer */

function param_get_integer($key)
{
    return param_integer($key, TRUE, FALSE);
}

function param_get_integer_opt($key)
{
    return param_integer($key, TRUE, TRUE);
}

function param_post_integer($key)
{
    return param_integer($key, FALSE, FALSE);
}

function param_post_integer_opt($key)
{
    return param_integer($key, FALSE, TRUE);
}

/* string:string */

function param_get_string2($key)
{
    return explode(':', param_string2($key, TRUE, FALSE));
}

function param_get_string2_opt($key)
{
    return explode(':', param_string2($key, TRUE, TRUE));
}

function param_post_string2($key)
{
    return explode(':', param_string2($key, FALSE, FALSE));
}

function param_post_string2_opt($key)
{
    return explode(':', param_string2($key, FALSE, TRUE));
}

/* string:string:string */

function param_get_string3($key)
{
    return explode(':', param_string3($key, TRUE, FALSE));
}

function param_get_string3_opt($key)
{
    return explode(':', param_string3($key, TRUE, TRUE));
}

function param_post_string3($key)
{
    return explode(':', param_string3($key, FALSE, FALSE));
}

function param_post_string3_opt($key)
{
    return explode(':', param_string3($key, FALSE, TRUE));
}

?>
