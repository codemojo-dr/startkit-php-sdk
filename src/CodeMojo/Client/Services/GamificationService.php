<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Http\APIResponse;

/**
 * Class GamificationService
 * @package CodeMojo\Client\Services
 */
class GamificationService
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

    /**
     * @param $user_id
     * @param $action_id
     * @param null $platform
     * @return bool
     */
    public function captureAction($user_id, $action_id, $platform = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_GAMIFICATION;

        $params = array(
            "customer_id" => $user_id, "action_id" => $action_id, "platform" => $platform
        );

        $result = $this->authenticationService->getTransport()->fetch($url, $params,'PUT', array(), 0);

        return $result['code'] == APIResponse::RESPONSE_SUCCESS;
    }

    /**
     * @param $user_id
     * @param $action_id
     * @param $category_id
     * @return bool
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function addAchievements($user_id, $action_id, $category_id = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_GAMIFICATION . Endpoints::GAMIFICATION_ACHIEVEMENTS;

        $params = array(
            "customer_id" => $user_id, "action_id" => $action_id, "id" => $category_id
        );

        $result = $this->authenticationService->getTransport()->fetch($url, $params,'PUT', array(), 0);

        return $result['code'] == APIResponse::RESPONSE_SUCCESS;
    }

    /**
     * @param $user_id
     * @return array
     */
    public function getUserStatus($user_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_GAMIFICATION . Endpoints::GAMIFICATION_SUMMARY;
        $url = sprintf($url, $user_id);

        $result = $this->authenticationService->getTransport()->fetch($url);

        return $result['results'];
    }

}