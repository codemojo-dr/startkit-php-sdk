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
    protected $environment = Endpoints::ENV_SANDBOX;

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
        return $this->environment == Endpoints::ENV_PRODUCTION ? Endpoints::URL_PRODUCTION : ($this->environment == Endpoints::ENV_SANDBOX ? Endpoints::URL_SANDBOX : Endpoints::URL_LOCAL);
    }

}