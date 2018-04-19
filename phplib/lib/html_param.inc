<?php
/**
 * Copyright (c) 2018. Rolfe Bozier
 */

/**
 * @param $s
 * @return bool
 */
function is_intstr($s)
{
    return filter_var($s, FILTER_VALIDATE_INT) !== FALSE;
}

/**
 * @param $s
 * @param $n
 * @return bool
 */
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

/**
 * @param $s
 * @return bool
 */
function is_string2($s) { return is_stringN($s, 2); }

/**
 * @param $s
 * @return bool
 */
function is_string3($s) { return is_stringN($s, 3); }

/**
 * @param $key
 * @param $checker
 * @param $is_get
 * @param $is_optional
 * @return int|null|string
 * @throws SecurityError
 */
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
    $v = $dict[$key];
    if (!$checker($v)) {
        throw new SecurityError("bad type for '$method' parameter: $key=[$v]");
    }

    if ($checker == 'is_intstr') {
        return intval($v);
    }

    return $v;
}

/**
 * @param $key
 * @param $is_get
 * @param $is_optional
 * @return int|null|string
 * @throws SecurityError
 */
function param_string($key, $is_get, $is_optional)
{
    return check($key, 'is_string', $is_get, $is_optional);
}

/**
 * @param $key
 * @param $is_get
 * @param $is_optional
 * @return int|null|string
 * @throws SecurityError
 */
function param_integer($key, $is_get, $is_optional)
{
    return check($key, 'is_intstr', $is_get, $is_optional);
}

/**
 * @param $key
 * @param $is_get
 * @param $is_optional
 * @return int|null|string
 * @throws SecurityError
 */
function param_string2($key, $is_get, $is_optional)
{
    return check($key, 'is_string2', $is_get, $is_optional);
}

/**
 * @param $key
 * @param $is_get
 * @param $is_optional
 * @return int|null|string
 * @throws SecurityError
 */
function param_string3($key, $is_get, $is_optional)
{
    return check($key, 'is_string3', $is_get, $is_optional);
}

/* string */

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_get_string($key)
{
    return param_string($key, TRUE, FALSE);
}

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_get_string_opt($key)
{
    return param_string($key, TRUE, TRUE);
}

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_post_string($key)
{
    return param_string($key, FALSE, FALSE);
}

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_post_string_opt($key)
{
    return param_string($key, FALSE, TRUE);
}

/* integer */

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_get_integer($key)
{
    return param_integer($key, TRUE, FALSE);
}

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_get_integer_opt($key)
{
    return param_integer($key, TRUE, TRUE);
}

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_post_integer($key)
{
    return param_integer($key, FALSE, FALSE);
}

/**
 * @param $key
 * @return int|null|string
 * @throws SecurityError
 */
function param_post_integer_opt($key)
{
    return param_integer($key, FALSE, TRUE);
}

/* string:string */

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_get_string2($key)
{
    return explode(':', param_string2($key, TRUE, FALSE));
}

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_get_string2_opt($key)
{
    return explode(':', param_string2($key, TRUE, TRUE));
}

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_post_string2($key)
{
    return explode(':', param_string2($key, FALSE, FALSE));
}

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_post_string2_opt($key)
{
    return explode(':', param_string2($key, FALSE, TRUE));
}

/* string:string:string */

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_get_string3($key)
{
    return explode(':', param_string3($key, TRUE, FALSE));
}

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_get_string3_opt($key)
{
    return explode(':', param_string3($key, TRUE, TRUE));
}

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_post_string3($key)
{
    return explode(':', param_string3($key, FALSE, FALSE));
}

/**
 * @param $key
 * @return array
 * @throws SecurityError
 */
function param_post_string3_opt($key)
{
    return explode(':', param_string3($key, FALSE, TRUE));
}
