<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;

/**
 * Class LoyaltyService
 * @package CodeMojo\Client\Services
 */
class LoyaltyService
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
     * Add loyalty points to users wallet
     * @param $user_id
     * @param $transaction_value
     * @param null $platform
     * @param null $expires_in_days
     * @param null $transaction_id
     * @param null $meta
     * @param bool $frozen
     * @return bool
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function addLoyaltyPoints($user_id, $transaction_value, $platform = null, $expires_in_days = null, $transaction_id = null, $meta = null, $frozen = false){
        $result = $this->calculateLoyaltyPoints($user_id, $transaction_value, $platform, $expires_in_days, $transaction_id, $meta, $frozen);
        if(!empty($result)) {
            return $this->walletService->addBalance($user_id, $result['award'], @$result['expires_in_days'],
                $transaction_id ? $transaction_id : 'loyalty_' . $result['id'] . '_' . time(), $meta, "Loyalty points credited", $frozen);
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $transaction_value
     * @param null $platform
     * @param null $expires_in_days
     * @param null $transaction_id
     * @param null $meta
     * @param bool|false $frozen
     * @return array
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function calculateLoyaltyPoints($user_id, $transaction_value, $platform = null, $expires_in_days = null, $transaction_id = null, $meta = null, $frozen = false){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::LOYALTY_CALCULATE;

        $params = array(
            "customer_id" => $user_id, "value" => $transaction_value,
            "expiry" => $expires_in_days, "platform" => $platform
        );

        $result = $this->authenticationService->getTransport()->fetch($url, $params,'GET', array(), 0);

        if($result['code'] == 200) {
            return $result['results'];
        }else{
            return null;
        }
    }


    /**
     * @param $user_id
     * @param $transaction_value
     * @param null $platform
     * @return null
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function maximumRedemption($user_id, $transaction_value, $platform = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::REDEMPTION_CALCULATE;

        $params = array(
            "customer_id" => $user_id, "value" => $transaction_value, "platform" => $platform
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'GET');

        if(isset($result['code']) && $result['code'] == 200) {
            return $result['results'];
        }else{
            return null;
        }
    }

    public function getUserBrief($user_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::LOYALTY_SUMMARY;
        $url = sprintf($url, $user_id);

        $result = $this->authenticationService->getTransport()->fetch($url);

        $result['results']['balance'] = $this->walletService->getBalance($user_id);

        return $result['results'];
    }

    /**
     * @param $user_id
     * @param $redemption_value
     * @param $transaction_value
     * @param null $platform
     * @param null $meta
     * @return bool|null
     * @throws \CodeMojo\Client\Exceptions\BalanceExhaustedException
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function redeem($user_id, $redemption_value, $transaction_value, $platform = null, $meta = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::REDEMPTION_CALCULATE;

        $params = array(
            "customer_id" => $user_id, "value" => $transaction_value, "platform" => $platform
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'GET');

        if(isset($result['code']) && $result['code'] == 200) {
            $value = $result['results'];
            $value = $redemption_value >= $value ? $value : $redemption_value;
        }else{
            return null;
        }

        return $this->walletService->deductBalance($user_id,$value,'redemption_' . time(), $meta,'Loyalty redemption');
    }

    /**
     * @param $transaction_id
     * @return bool
     */
    public function unfreeze($transaction_id){
        return $this->walletService->unFreezeTransaction($transaction_id);
    }

    /**
     * Get the balance in the loyalty wallet of the user
     * @param $user_id
     * @return float
     */
    public function getBalance($user_id){
        return $this->walletService->getBalance($user_id);
    }
}