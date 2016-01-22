<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Contracts\IService;
use CodeMojo\Client\Exceptions\FieldsMissingException;


/**
 * Class BaseService
 * @package CodeMojo\Client\Services
 */
abstract class BaseService extends BaseConnector implements IService
{

    /**
     * @throws Exception
     */
    function onTokenFailure()
    {
        throw new \InvalidTokenException("Token invalid/expired", 0x05);
    }

    /**
     * @throws Exception
     */
    function onQuotaExceeded()
    {
        throw new \QuotaExceededException("API Quota Exceeded", 0x06);
    }

    function onAuthenticationFailure()
    {
        throw new \AuthenticationException("Invalid credentials, unable to create oauth token", 0x07);
    }

    function onFieldsMissing($error_info)
    {
        $data = implode(": ",$error_info);
        throw new FieldsMissingException("Some required field(s) are missing. " . $data, 0x09);
    }
}