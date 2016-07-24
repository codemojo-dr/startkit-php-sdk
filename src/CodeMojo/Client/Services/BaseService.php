<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Contracts\IService;
use CodeMojo\Client\Exceptions\AuthenticationException;
use CodeMojo\Client\Exceptions\FieldsMissingException;
use CodeMojo\OAuth2\Exception;
use InvalidArgumentException;


/**
 * Class BaseService
 * @package CodeMojo\Client\Services
 */
abstract class BaseService extends BaseConnector implements IService
{
    
    /**
     * @throws Exception
     */
    function onQuotaExceeded()
    {
        throw new QuotaExceededException("API Quota Exceeded", 0x06);
    }

    /**
     * @throws AuthenticationException
     */
    function onAuthenticationFailure()
    {
        throw new AuthenticationException("Invalid credentials, unable to create oauth token", 0x07);
    }

    /**
     * @param $error_info
     * @throws FieldsMissingException
     */
    function onFieldsMissing($error_info)
    {
        $data = implode(": ",$error_info);
        throw new FieldsMissingException("Some required field(s) are missing. " . $data, 0x09);
    }

    /**
     * @param $error_info
     */
    function onInvalidData($error_info)
    {
        throw new InvalidArgumentException($error_info, 0x11);
    }

    /**
     * @param $error_info
     * @throws Exception
     */
    function onError($error_info)
    {
        throw new Exception($error_info, 0x12);
    }
}