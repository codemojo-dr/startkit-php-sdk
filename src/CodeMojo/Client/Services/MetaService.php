<?php

namespace CodeMojo\Client\Services;

use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Http\APIResponse;

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

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
            $unwrapped = @json_decode($result['results']['value']);
            $value = $unwrapped === null ? $result['results']['value'] : $unwrapped;
            return array("value" => $value, "validity" => $result['results']['validity']);
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
    public function add($key, $value, $valid_for_minutes = null){
        if(is_object($value) || is_array($value)){
            $value = json_encode($value);
        }

        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_META . Endpoints::META;

        $result = $this->authenticationService->getTransport()->fetch($url,array("key"=>$key,"value"=>$value,"validity"=>$valid_for_minutes),'PUT',array(),0);

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
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

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;
    }



}