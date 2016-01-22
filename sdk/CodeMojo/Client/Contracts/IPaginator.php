<?php

namespace CodeMojo\Client\Contracts;


/**
 * Interface IPaginator
 * @package CodeMojo\Client\Contracts
 */
interface IPaginator
{

    /**
     * @return mixed
     */
    function next();

    /**
     * @return mixed
     */
    function previous();

    /**
     * @return mixed
     */
    function results();

    /**
     * @return mixed
     */
    function hasReachedEndOfPage();

    /**
     * @return mixed
     */
    function hasReachedBeginningOfPage();

}