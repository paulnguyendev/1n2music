<?php
return [
    'min' => [
        'type' => 'error',
        'msg' => 'Insufficient balance, minimum $50 required',
        'value' => 0
    ],
    'max' => [
        'type' => 'error',
        'msg' => 'The balance in the wallet is not enough',
        'value' => 0
    ],
    'account' => [
        'type' => 'error',
        'msg' => 'The user does not have a payment account',
        'value' => 0
    ],
    'system' => [
        'type' => 'error',
        'msg' => 'System Error',
        'value' => 0
    ],
    'allow' => [
        'type' => 'success',
        'msg' => 'Allow',
        'value' => 1
    ]
];
