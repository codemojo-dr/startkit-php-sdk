<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Http\APIResponse;
use CodeMojo\Client\Paginator\PaginatedResults;

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

    /**
     * @param $user_id
     * @param null $paginated_url
     * @param int $page
     * @return PaginatedResults
     */
    public function getSignedUpUsersList($user_id, $paginated_url = null, $page = 1){
        if($paginated_url) {
            $url = $paginated_url;
        }else {
            $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REFERRAL . Endpoints::REFERRAL_SIGNUP_LIST;
            $url = sprintf($url, $user_id);
        }

        $result = $this->authenticationService->getTransport()->fetch($url);

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
            $paginatedResult = new PaginatedResults($result['results'], array($this,'getSignedUpUsersList'), array($user_id, 10));
            return $paginatedResult;
        }else{
            return new PaginatedResults(array(),null,null);
        }
    }

    /**
     * @param $user_id
     * @return int
     */
    public function getSalesGeneratedByUserReferral($user_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_REFERRAL . Endpoints::REFERRAL_SIGNUP_SALE;
        $url = sprintf($url, $user_id);

        $result = $this->authenticationService->getTransport()->fetch($url);

        return $result['code'] == APIResponse::RESPONSE_SUCCESS? $result['results']: 0;
    }
}