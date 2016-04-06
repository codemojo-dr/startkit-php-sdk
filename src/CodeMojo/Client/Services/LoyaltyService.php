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
     * @param null $service
     * @param null $expires_in_days
     * @param null $transaction_id
     * @param null $meta
     * @param null $tag
     * @param bool $frozen
     * @return bool
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function addLoyaltyPoints($user_id, $transaction_value, $platform = null, $service = null, $expires_in_days = null, $transaction_id = null, $meta = null, $tag = null, $frozen = false){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::LOYALTY_CALCULATE;

        $params = array(
            "customer_id" => $user_id, "transaction" => $transaction_value,
            'transaction_id'=> $transaction_id ? $transaction_id : sha1('loyalty_' . $user_id . '_' . time()),
            'hold' => $frozen ? 1 : 0, 'meta' => $meta, 'tag' => $tag, "expiry" => $expires_in_days,
            "platform" => $platform, "service" => $service
        );

        $result = $this->authenticationService->getTransport()->fetch($url, $params,'PUT', array(), 0);

        return $result['code'] == 200 ;
    }

    /**
     * @param $user_id
     * @param $transaction_value
     * @param null $platform
     * @param null $service
     * @return null
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function calculateLoyaltyPoints($user_id, $transaction_value, $platform = null, $service = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::LOYALTY_CALCULATE;

        $params = array(
            "customer_id" => $user_id, "transaction" => $transaction_value,
            "platform" => $platform, "service" => $service
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
     * @param null $service
     * @return null
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function maximumRedemption($user_id, $transaction_value, $platform = null, $service = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::REDEMPTION_CALCULATE;

        $params = array(
            "customer_id" => $user_id, "transaction" => $transaction_value, "platform" => $platform, "service" => $service
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'GET');

        if(isset($result['code']) && $result['code'] == 200) {
            $walletBalance = $this->getBalance($user_id);

            return $walletBalance >= $result['results'] ? $result['results'] : $walletBalance;
        }else{
            return null;
        }
    }

    /**
     * @param $user_id
     * @return mixed
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
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
     * @param int $redeem_from
     * @param null $platform
     * @param null $service
     * @param null $transaction_id
     * @param null $meta
     * @param null $tag
     * @return bool|null
     * @throws \CodeMojo\Client\Exceptions\BalanceExhaustedException
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     * @throws \CodeMojo\OAuth2\Exception
     */
    public function redeem($user_id, $redemption_value, $transaction_value, $redeem_from = -1, $platform = null, $service = null, $transaction_id = null, $meta = null, $tag = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY;

        $params = array(
            "customer_id" => $user_id, "value" => $redemption_value, "platform" => $platform,
            "service" => $service, "transaction" => $transaction_value, 'transaction_type' => $redeem_from,
            'transaction_id'=> $transaction_id ? $transaction_id : sha1('loyalty_' . $user_id . '_' . time()),
            'meta' => $meta, 'tag' => $tag
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'DELETE',array(),0);

        return $result['code'] == 200;
    }

    /**
     * @param $transaction_id
     * @return bool
     * @throws \CodeMojo\Client\Exceptions\ResourceNotFoundException
     */
    public function cancelTransaction($transaction_id){
        return $this->getWalletService()->cancelTransaction($transaction_id);
    }

    /**
     * @param $transaction_id
     * @return bool
     * @throws \CodeMojo\Client\Exceptions\ResourceNotFoundException
     */
    public function refund($transaction_id){
        return $this->getWalletService()->refund($transaction_id);
    }

    public function refundPartial($transaction_id, $sku_value){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_LOYALTY . Endpoints::WALLET_TRANSACTION_REFUND;
        $url = sprintf($url, $transaction_id);

        $result = $this->authenticationService->getTransport()->fetch($url, array('value' => $sku_value), 'POST', array(), 0);

        if($result["code"] == 404) {
            throw new ResourceNotFoundException("Transaction ID not found", 0x08);
            return false;
        }elseif($result["code"] == 400){
            throw new BalanceExhaustedException("Redemption value more than actual value", 0x08);
            return false;
        }

        return $result["code"] == 200;
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