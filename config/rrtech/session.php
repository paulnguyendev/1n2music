<?php
$studio = rrt_get_config_by('core', 'prefix', 'studio');
$admin = rrt_get_config_by('core', 'prefix', 'admin');
$user = rrt_get_config_by('core', 'prefix', 'user');

return [
    $studio => [
        'session' => "info_{$studio}",
        'redirect' => "public/studio/home/index",
        'login' => "public/auth/signIn",
    ],
    $admin => [
        'session' => "info_{$admin}",
        'redirect' => "{$admin}/dashboard/index",
        'login' => "{$admin}/auth/login",
    ],
    $user => [
        'session' => "info_{$user}",
        'redirect' => "{$user}/home/index",
        'login' => "{$user}/auth/login",
    ],
];
