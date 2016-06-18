<?php

namespace CodeMojo\Client\Services;

use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Exceptions\BalanceExhaustedException;
use CodeMojo\Client\Models\CustomerInfo;
use CodeMojo\Client\Paginator\PaginatedResults;
use CodeMojo\OAuth2\Exception;

/**
 * Class WalletService
 * @package CodeMojo\Client\Services
 */
class DataSyncService {


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
     * @param $customer_id
     * @param $customer_info
     * @return bool
     * @throws Exception
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function syncUserData($customer_id, CustomerInfo $customer_info){

        $customer_info = json_encode($customer_info->toArray());

        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_DATASYNC . Endpoints::DATASYNC_USER;

        $result = $this->authenticationService->getTransport()->fetch($url,array("customer_id" => $customer_id, "meta"=>$customer_info),'POST',array(),0);

        if($result["code"] == 200){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Deletes the stored meta data for the key provided
     * @param $customer_id
     * @return bool
     * @throws Exception
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @internal param $key
     */
    public function deleteUserData($customer_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_DATASYNC . Endpoints::DATASYNC_USER;

        $result = $this->authenticationService->getTransport()->fetch($url,array("customer_id"=>$customer_id),'DELETE',array(),0);

        return $result["code"] == 200;
    }



}