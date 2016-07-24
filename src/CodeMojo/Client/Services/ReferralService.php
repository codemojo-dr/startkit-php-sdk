<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Http\APIResponse;

/**
 * Class ReferralService
 * @package CodeMojo\Client\Services
 */
class ReferralService
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * ReferralService constructor.
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param $user_id
     * @return null
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function getReferralCode($user_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REFERRAL . Endpoints::REFERRAL_GENERATE;
        $url = sprintf($url, $user_id);

        $result = $this->authenticationService->getTransport()->fetch($url, array(),'PUT', array(), 0);

        if($result['code'] == APIResponse::RESPONSE_SUCCESS){
            return $result['results'];
        } else{
            return null;
        }
    }

    /**
     * @param $user_id
     * @param $referral_code
     * @return array
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function useReferralCode($user_id, $referral_code){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REFERRAL . Endpoints::REFERRAL_USE;
        $url = sprintf($url, $user_id, $referral_code);

        $result = $this->authenticationService->getTransport()->fetch($url);

        return $result['code'] == APIResponse::RESPONSE_SUCCESS;
    }

    /**
     * @param $user_id
     * @return bool
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function markActivityComplete($user_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REFERRAL . Endpoints::REFERRAL_CLAIM;
        $url = sprintf($url, $user_id);

        $result = $this->authenticationService->getTransport()->fetch($url, array(),'PUT', array(), 0);

        return $result['code'] == APIResponse::RESPONSE_SUCCESS;
    }

}