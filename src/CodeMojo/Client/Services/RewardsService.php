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
     * @var WalletService
     */
    private $walletService;
    /**
     * @var AuthenticationService
     */
    private $authenticationService;


    /**
     * LoyaltyService constructor.
     * @param AuthenticationService $authenticationService
     * @param WalletService|null $walletService
     */
    public function __construct(AuthenticationService $authenticationService, WalletService $walletService = null)
    {
        $this->walletService = $walletService ? $walletService : new WalletService($authenticationService);
        $this->authenticationService = $authenticationService;
    }

    /**
     * @return WalletService|null
     */
    public function getWalletService(){
        return $this->walletService;
    }

    public function getAvailableRewards($user_email_phone, $app_id, $filters = array()){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REWARDS . Endpoints::REWARDS_LIST_AVAILABLE_REWARDS;
        $url = sprintf($url, $app_id);

        $params = array(
            'email' => $user_email_phone, 'phone' => $user_email_phone,
            "lat" => @$filters['lat'], "lon" => @$filters['lon'],
            'price_min' => @$filters['price_min'], 'price_max' => @$filters['price_max'],
            'category' => @$filters['category'], 'valid_till' => @$filters['valid_till'],
            'test' => @$filters['testing']
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'GET');

        if(isset($result['code']) && $result['code'] == APIResponse::RESPONSE_SUCCESS) {
            if($result['count'] > 0){
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

    public function grabReward($deliver_to, $app_id, $reward_id, $additional_info = array()){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REWARDS . Endpoints::REWARDS_GRAB;
        $url = sprintf($url, $app_id, $reward_id);

        $params = array(
            "lat" => @$additional_info['lat'], "lon" => @$additional_info['lon'],
            "email" => $deliver_to, "phone" => $deliver_to, "age" => @$additional_info['age'],
            "gender" => @$additional_info['gender'], 'test' => @$additional_info['testing']
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'POST', array(),0);

        if(isset($result['code']) && $result['code'] == APIResponse::RESPONSE_SUCCESS) {
            return $result['offer'];
        } else if($result['code'] == APIResponse::WALLET_BALANCE_EXHAUSTED) {
            throw new RewardsExhaustedException("Cannot be grabbed, all rewards exhausted");
        }

        return null;
    }

}