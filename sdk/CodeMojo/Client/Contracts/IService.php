<?php

namespace CodeMojo\Client\Contracts;


/**
 * Interface IService
 * @package CodeMojo\Client\Contracts
 */
interface IService
{
    /**
     * @return mixed
     */
    function onTokenFailure();

    /**
     * @return mixed
     */
    function onQuotaExceeded();


    /**
     * @return mixed
     */
    function onAuthenticationFailure();

    /**
     * @return mixed
     */
    function onFieldsMissing($error_info);

}