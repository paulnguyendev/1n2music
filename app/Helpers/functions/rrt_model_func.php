<?php

function rrt_get_login_info($prefix = "studio", $key = "")
{
    #_prefix: admin, studio...
    $result = null;
    $session = rrt_get_config_by('session', $prefix, 'session');
    $loginInfo = $session ? session()->get($session) : [];
    $loginInfo = $loginInfo ? $loginInfo->toArray() : [];
    $resultHasKey = $key ? $loginInfo[$key] : "";
    $result = $resultHasKey  ? $resultHasKey : $loginInfo;
    return $result;
}
function rrt_get_user_info($user_id, $key = "")
{
}


function rrt_get_balance_for_user($user_id, $type, $format = 0)
{
    return $user_id ? '3000000' : '';
}
