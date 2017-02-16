<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Exceptions\RewardsExhaustedException;
use CodeMojo\Client\Http\APIResponse;

/**
 * Class RewardsService
 * @package CodeMojo\Client\Services
 */
class RewardsService
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    private $app_id;

    /**
     * LoyaltyService constructor.
     * @param AuthenticationService $authenticationService
     * @param $app_id
     */
    public function __construct(AuthenticationService $authenticationService, $app_id)
    {
        $this->authenticationService = $authenticationService;
        $this->app_id = $app_id;
    }

    public function getAvailableRewards($user_email_phone, $filters = array()){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REWARDS . Endpoints::REWARDS_LIST_AVAILABLE_REWARDS;
        $url = sprintf($url, $this->app_id);

        $filters['email'] = $user_email_phone; $filters['phone'] = $user_email_phone;
        $params = $filters;

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'GET');

        if(isset($result['code']) && $result['code'] == APIResponse::RESPONSE_SUCCESS) {
            if(isset($result['count']) && @$result['count'] > 0){
                return $result['results'];
            } else {
                throw new RewardsExhaustedException("No rewards available at this moment");
            }
        }else{
            return null;
        }
    }

    public function isAvailableForRegion($lat, $lon){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REWARDS . Endpoints::REWARDS_REGION_AVAILABILITY;
        $url = sprintf($url);

        $params = array(
            'lat' => $lat, 'lon' => $lon
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'GET');

        return isset($result['code']) && $result['code'] == APIResponse::RESPONSE_SUCCESS;
    }

    public function grabReward($customer_id, $deliver_to, $reward_id, $additional_info = array()){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REWARDS . Endpoints::REWARDS_GRAB;
        $url = sprintf($url, $this->app_id, $reward_id);

        $additional_info['email'] = $deliver_to; $additional_info['phone'] = $deliver_to;
        $additional_info['customer_id'] = $customer_id;
        $params = $additional_info;

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'POST', array(),0);

        if(isset($result['code']) && $result['code'] == APIResponse::RESPONSE_SUCCESS) {
            return $result['offer'];
        } else if($result['code'] == APIResponse::WALLET_BALANCE_EXHAUSTED) {
            throw new RewardsExhaustedException("Cannot be grabbed, all rewards exhausted");
        }

        return null;
    }

    public function trackSession($additional_info = array()){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REWARDS . Endpoints::REWARDS_SESSION;
        $url = sprintf($url, $this->app_id);

        $result = $this->authenticationService->getTransport()->fetch($url, $additional_info,'POST', array(),0);

        return $result['code'] == APIResponse::RESPONSE_SUCCESS;
    }
}