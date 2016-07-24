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
    function onQuotaExceeded();


    /**
     * @return mixed
     */
    function onAuthenticationFailure();

    /**
     * @return mixed
     */
    function onFieldsMissing($error_info);

    /**
     * @return mixed
     */
    function onInvalidData($error_info);

    /**
     * @param $error_info
     * @return mixed
     */
    public function onError($error_info);

}