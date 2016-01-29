<?php

namespace CodeMojo\Client\Paginator;


use CodeMojo\Client\Contracts\IPaginator;

/**
 * Class PaginatedResults
 * @package DRewards\Client\Paginator
 */
class PaginatedResults implements IPaginator
{
    /**
     * @var
     */
    private $nextURL;

    /**
     * @var
     */
    private $prevURL;
    /**
     * @var
     */
    private $callerObject;
    /**
     * @var array
     */
    private $params;
    /**
     * @var array
     */
    private $resultSet;


    /**
     * Paginator constructor.
     * @param array $resultSet
     * @param $callback
     * @param array $params
     */
    public function __construct(array $resultSet, $callback, $params = array())
    {
        $this->resultSet = $resultSet;
        $this->nextURL = $resultSet['next_page_url'];
        $this->prevURL = $resultSet['prev_page_url'];
        $this->params = $params;
        $this->callerObject = $callback;
    }

    /**
     * @return mixed
     */
    function results()
    {
        return $this->resultSet['data'];
    }

    /**
     * @return bool
     */
    function next()
    {
        $this->params[] = $this->nextURL;
        $result = call_user_func_array($this->callerObject, $this->params);
        if($result !== false){
            $this->params = $result->params;
            $this->resultSet = $result->resultSet;
            $this->nextURL = $result->nextURL;
            $this->prevURL = $result->prevURL;
        }
        return !$this->hasReachedEndOfPage();
    }

    /**
     * @return bool
     */
    function previous()
    {
        $this->params[] = $this->prevURL;
        $result = call_user_func_array($this->callerObject, $this->params);
        if($result !== false){
            $this->params = $result->params;
            $this->resultSet = $result->resultSet;
            $this->nextURL = $result->nextURL;
            $this->prevURL = $result->prevURL;
        }
        return !$this->hasReachedBeginningOfPage();
    }

    /**
     * @return bool
     */
    function hasReachedEndOfPage()
    {
        return empty($this->resultSet['next_page_url']);
    }

    /**
     * @return bool
     */
    function hasReachedBeginningOfPage()
    {
        return empty($this->resultSet['prev_page_url']);
    }
}