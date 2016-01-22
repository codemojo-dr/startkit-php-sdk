<?php

use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Exceptions\Exceptions;
use CodeMojo\Client\Services\AuthenticationService;
use CodeMojo\Client\Services\LoyaltyService;

require_once '../sdk/autoload.php';

const CLIENT_ID     = 'sample@codemojo.io';
const CLIENT_SECRET = 'PLB6DHP7VcykRDdvloi2X9tEq3FvsIBhtdn7UdeQ';

$authService = new AuthenticationService(CLIENT_ID, CLIENT_SECRET, Endpoints::SANDBOX, function($type){
    switch($type){
        case Exceptions::AUTHENTICATION_EXCEPTION:
            echo 'Authentication Exception';
            break;
        case Exceptions::BALANCE_EXHAUSTED_EXCEPTION:
            echo 'Low balance';
            break;
        case Exceptions::FIELDS_MISSING_EXCEPTION:
            echo 'Fields missing';
            break;
        case Exceptions::QUOTA_EXCEPTION:
            echo 'Quota Exhausted Exception';
            break;
        case Exceptions::TOKEN_EXCEPTION:
            echo 'Invalid token Exception';
            break;
        default:
            echo 'Unknown exception';
            break;
    }
});

$loyaltyService = new LoyaltyService($authService);
