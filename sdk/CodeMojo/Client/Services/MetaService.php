<?php

namespace CodeMojo\Client\Services;

use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Exceptions\BalanceExhaustedException;
use CodeMojo\Client\Paginator\PaginatedResults;
use CodeMojo\OAuth2\Exception;

/**
 * Class WalletService
 * @package CodeMojo\Client\Services
 */
class MetaService {


    /**
     * @var
     */
    private $authenticationService;

    /**
     * MetaService constructor.
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }


    /**
     * Gets the stored meta data for the key provided
     * @param $key
     * @return array
     */
    public function get($key){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_META . Endpoints::META;

        $result = $this->authenticationService->getTransport()->fetch($url,array("key"=>$key));

        if($result["code"] == 200){
            $unwrapped = @unserialize($result['results']);
            $value = $unwrapped === false ? $result['results'] : $unwrapped;
            return $value;
        }else{
            return null;
        }
    }

    /**
     * Adds or Updates meta data for the key provided
     * @param $key
     * @param $value
     * @return bool
     */
    public function add($key, $value){

        $serialized = @serialize($value);
        $value = $serialized === false ? $value : $serialized;

        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_META . Endpoints::META;

        $result = $this->authenticationService->getTransport()->fetch($url,array("key"=>$key,"value"=>$value),'PUT',array(),0);

        if($result["code"] == 200){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Update meta data for the given key
     * @param $key
     * @param $value
     * @return bool
     */
    public function update($key, $value){
        return $this->add($key, $value);
    }

    /**
     * Deletes the stored meta data for the key provided
     * @param $key
     * @return bool
     */
    public function delete($key){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_META . Endpoints::META;

        $result = $this->authenticationService->getTransport()->fetch($url,array("key"=>$key),'DELETE',array(),0);

        return $result["code"] == 200;
    }



}