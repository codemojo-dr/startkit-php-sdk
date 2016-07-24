<?php

namespace CodeMojo\Client\Http;


class APIResponse
{
    const RESPONSE_SUCCESS = 200;
    const RESPONSE_FAILURE = 400;

    const ACCESS_DENIED = 401;

    const SERVER_EXCEPTION = 500;
    const SERVER_BAD_GATEWAY = 502;
    const SERVER_QUERY_EXCEPTION = 406;

    const SERVICE_NOT_SETUP = 424;
    const SERVICE_REMOTE_EXCEPTION = 444;
    const SERVICE_QUOTA_EXCEEDED = 509;

    const RESOURCE_NOT_FOUND = 404;
    const INVALID_MISSING_FIELDS = 412;

    const REFERRAL_USED_EARNED = 410;
    const WALLET_BALANCE_EXHAUSTED = 411;
    const DUPLICATE_ACTION = 416;
    const OVERFLOW = 413;
}