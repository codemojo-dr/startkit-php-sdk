<?php

namespace CodeMojo\Client\Services;

use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Enums\WalletTransactionTypes;
use CodeMojo\Client\Exceptions\BalanceExhaustedException;
use CodeMojo\Client\Exceptions\QuotaExceededException;
use CodeMojo\Client\Exceptions\ResourceNotFoundException;
use CodeMojo\Client\Http\APIResponse;
use CodeMojo\Client\Paginator\PaginatedResults;
use CodeMojo\OAuth2\Exception;

/**
 * Class WalletService
 * @package CodeMojo\Client\Services
 */
class WalletService {

    private $maps = array(
        'created_at' => 'timestamp',
        'transaction_value' => 'value',
        'meta_key' => 'id',
        'on_hold' => 'frozen'
    );

    /**
     * @var
     */
    private $authenticationService;

    /**
     * WalletService constructor.
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }


    /**
     * Get the wallet balance of a user
     * @param $user_id
     * @param int $type
     * @return float
     * @throws Exception
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function getBalance($user_id, $type = -1){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_CREDITS_BALANCE;
        $url = sprintf($url, $user_id, $type);
        $result = $this->authenticationService->getTransport()->fetch($url);

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
            return $result['results'];
        }else{
            return 0;
        }
    }

    /**
     * Get details about a particular transaction
     * @param $transaction_id
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getTransactionDetail($transaction_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_TRANSACTION;
        $url = sprintf($url,$transaction_id);
        $result = $this->authenticationService->getTransport()->fetch($url);

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
            // Wrap it in a array since maskData takes an Array of Array
            $maskWrapper = array($result['results']);
            $this->maskData($maskWrapper);
            return $maskWrapper[0];
        }else{
            return 0;
        }
    }

    /**
     * Get all transactions for a particular user
     * @param $user_id
     * @param int $count
     * @param null $paginated_url
     * @param int $page
     * @return PaginatedResults
     * @throws Exception
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function getTransactionDetailsForUser($user_id, $count = 10, $paginated_url = null, $page = 1){
        if($paginated_url) {
            $url = $paginated_url;
        }else {
            $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET
                . Endpoints::WALLET_TRANSACTIONS_USER . ($paginated_url ? '' : '?page=' . (int) $page);
            $url = sprintf($url, $user_id, $count);
        }

        $result = $this->authenticationService->getTransport()->fetch($url);

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
            $this->maskData($result['results']['data']);
            $paginatedResult = new PaginatedResults($result['results'],array($this,'getTransactionDetailsForUser'),array($user_id,$count));
            return $paginatedResult;
        }else{
            return new PaginatedResults(array(),null,null);
        }

    }

    /**
     * Get all transactions from all the users
     * @param int $count
     * @param null $paginated_url
     * @param int $page
     * @return PaginatedResults
     * @throws Exception
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function getAllTransactions($count = 10, $paginated_url = null, $page = 1){
        if($paginated_url) {
            $url = $paginated_url;
        }else{
            $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET
                . Endpoints::WALLET_TRANSACTIONS_ALL . ($paginated_url ? '' : '?page=' . (int) $page);
            $url = sprintf($url, $count);
        }

        $result = $this->authenticationService->getTransport()->fetch($url);

        if($result["code"] == APIResponse::RESPONSE_SUCCESS){
            $this->maskData($result['results']['data']);
            $paginatedResult = new PaginatedResults($result['results'],array($this,'getAllTransactions'),array($count));
            return $paginatedResult;
        }else{
            return new PaginatedResults(array(),null,null);
        }
    }

    /**
     * @param $user_id
     * @param $value_to_remove
     * @param int $deduct_from
     * @param null $transaction_id
     * @param null $meta_data
     * @param null $tag
     * @return bool
     * @throws BalanceExhaustedException
     * @throws Exception
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function deductBalance($user_id, $value_to_remove, $deduct_from = WalletTransactionTypes::REDEEM_COMBINED, $transaction_id = null, $meta_data = null, $tag = null){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_CREDITS;

        $params = array(
            'customer_id' => $user_id, 'value'=>$value_to_remove,
            'transaction_id'=> $transaction_id,
            'transaction_type'=> $deduct_from,
            'meta' => $meta_data, 'tag' => $tag
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'DELETE',array(),0);

        if($result["code"] == APIResponse::WALLET_BALANCE_EXHAUSTED){
            throw new BalanceExhaustedException("Not enough balance", 0x08);
            return false;
        }

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;
    }

    /**
     * Unfreeze / Unhold a frozen transaction
     * @param $transaction_id
     * @return bool
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function unFreezeTransaction($transaction_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_TRANSACTION_UNFREEZE;

        $params = array(
            'transaction_id'=>$transaction_id
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'POST',array(),0);

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;

    }

    /**
     * Add a balance which is Frozen / On Hold to a particular user's wallet.
     * The credits are counted under the @getBalance call as long as its frozen
     * @param $user_id
     * @param $value_to_add
     * @param null $transaction_id
     * @param null $meta_data
     * @param null $tag
     * @return bool
     */
    public function addFrozenBalance($user_id, $value_to_add, $transaction_type = WalletTransactionTypes::TRANSACTIONAL, $transaction_id = null, $meta_data = null, $tag = null){
        return $this->addBalance($user_id,$value_to_add, $transaction_type, 0, $transaction_id,$meta_data,$tag,true);
    }

    /**
     * Add balance to a particular user's wallet
     * @param $user_id
     * @param $value_to_add
     * @param int $transaction_type
     * @param int|null $expires_in_days
     * @param null $transaction_id
     * @param null $meta_data
     * @param null $tag
     * @param bool|false $frozen
     * @return bool
     * @throws QuotaExceededException
     */
    public function addBalance($user_id, $value_to_add, $transaction_type = WalletTransactionTypes::TRANSACTIONAL, $expires_in_days = 0, $transaction_id = null, $meta_data = null, $tag = null, $frozen = false){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_CREDITS;

        $params = array(
            'customer_id' => $user_id, 'value'=>$value_to_add, 'transaction_type' => $transaction_type,
            'transaction_id'=>$transaction_id, 'hold' => $frozen ? 1 : 0,
            'meta' => $meta_data, 'tag' => $tag, 'expiry' => $expires_in_days
        );

        $result = $this->authenticationService->getTransport()->fetch($url,$params,'PUT',array(),0);

        if($result["code"] == APIResponse::OVERFLOW){
            throw new QuotaExceededException("Wallet value quota exeeded");
        }

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;
    }

    private function maskData(array &$data){
        foreach($data as &$model){
            foreach($this->maps as $key => $mask){
                if(isset($model[$key])){
                    $model[$mask] = $model[$key];
                    unset($model[$key]);
                }
            }
        }
    }

    /**
     * @param $transaction_id
     * @return bool
     * @throws Exception
     * @throws ResourceNotFoundException
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function refund($transaction_id){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_TRANSACTION_REFUND;
        $url = sprintf($url, $transaction_id);

        $result = $this->authenticationService->getTransport()->fetch($url, array(), 'PUT', array(), 0);

        if($result["code"] == APIResponse::RESOURCE_NOT_FOUND) {
            throw new ResourceNotFoundException("Transaction ID not found", 0x08);
            return false;
        }

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;
    }

    /**
     * @param $transaction_id
     * @param $refund_value
     * @return bool
     * @throws BalanceExhaustedException
     * @throws Exception
     * @throws ResourceNotFoundException
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function refundPartial($transaction_id, $refund_value){
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_TRANSACTION_REFUND;
        $url = sprintf($url, $transaction_id);

        $result = $this->authenticationService->getTransport()->fetch($url, array('value' => $refund_value), 'POST', array(), 0);

        if($result["code"] == APIResponse::RESOURCE_NOT_FOUND) {
            throw new ResourceNotFoundException("Transaction ID not found", 0x08);
            return false;
        }elseif($result["code"] == APIResponse::WALLET_BALANCE_EXHAUSTED){
            throw new BalanceExhaustedException("Redemption value more than actual value", 0x08);
            return false;
        }

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;
    }

    /**
     * @param $transaction_id
     * @return bool
     * @throws Exception
     * @throws ResourceNotFoundException
     * @throws \CodeMojo\Client\Http\InvalidArgumentException
     */
    public function cancelTransaction($transaction_id) {
        $url = $this->authenticationService->getServerEndPoint() . Endpoints::VERSION . Endpoints::BASE_WALLET . Endpoints::WALLET_TRANSACTION;
        $url = sprintf($url, $transaction_id);

        $result = $this->authenticationService->getTransport()->fetch($url, array(), 'DELETE', array(), 0);

        if($result["code"] == APIResponse::RESOURCE_NOT_FOUND) {
            throw new ResourceNotFoundException("Transaction ID not found", 0x08);
            return false;
        }

        return $result["code"] == APIResponse::RESPONSE_SUCCESS;
    }

}