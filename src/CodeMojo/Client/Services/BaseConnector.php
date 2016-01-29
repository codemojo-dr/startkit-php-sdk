<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;

/**
 * Class BaseConnector
 * @package CodeMojo\Client\Services
 */
abstract class BaseConnector
{

    /**
     * @var
     */
    protected $environment = Endpoints::SANDBOX;

    /**
     * @param int $env
     */
    public function setEnvironment($env){
        $this->environment = $env;
    }

    /**
     * @return string
     * @internal
     */
    public function getServerEndPoint(){
        return $this->environment == Endpoints::PRODUCTION ? Endpoints::ENDPOINT_PRODUCTION : ($this->environment == Endpoints::SANDBOX ? Endpoints::ENDPOINT_SANDBOX : Endpoints::ENDPOINT_LOCAL);
    }

}