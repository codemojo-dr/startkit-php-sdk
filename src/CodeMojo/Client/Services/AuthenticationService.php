<?php

namespace CodeMojo\Client\Services;


use CodeMojo\Client\Endpoints;
use CodeMojo\Client\Http\HttpGuzzle;
use CodeMojo\OAuth2\Exception;
use CodeMojo\OAuth2\Storage\PersistentStorage;

/**
 * Class AuthenticationService
 * @package CodeMojo\Client\Services
 */
class AuthenticationService extends BaseService
{
    /**
     * @var String
     */
    private $client_id;
    /**
     * @var String
     */
    private $client_secret;

    /**
     * @var PersistentStorage
     */
    private $storage;
    /**
     * @var HttpGuzzle
     */
    private $transport;

    /**
     * @var null
     */
    private $tCallback = null;
    /**
     * @var null
     */
    private $qCallback = null;

    /**
     * @var null
     */
    private $aCallback = null;
    /**
     * @var null
     */
    private $callback;

    /**
     * AuthenticationService constructor.
     * @param $client_id
     * @param $client_secret
     * @param int $environment
     * @param null $callback
     */
    public function __construct($client_id, $client_secret, $environment = Endpoints::SANDBOX, $callback = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->storage = new PersistentStorage();
        if(empty($environment)){$environment = Endpoints::SANDBOX;}
        $this->setEnvironment($environment);
        if($this->storage->accessTokenMightHaveExpired($client_id, $client_secret)) {
            $this->reauthenticate();
        }
        $this->transport = new HttpGuzzle($this->storage->getAccessToken(), $this);
        $this->callback = $callback;
    }

    /**
     * @return HttpGuzzle
     * @throws Exception
     * @internal
     */
    public function getTransport(){
        if($this->storage->accessTokenMightHaveExpired($this->client_id, $this->client_secret)){
            $this->reauthenticate();
        }
        return $this->transport;
    }

    /**
     * @internal
     * @throws \QuotaExceededException
     */
    public function onQuotaExceeded()
    {
        if($this->callback) {
            call_user_func_array($this->callback,array(0x06,"api request quota exceeded"));
            return;
        }elseif($this->environment == Endpoints::LOCAL) {
            parent::onQuotaExceeded();
        }
    }


    /**
     * @internal
     * @throws \InvalidTokenException
     */
    public function onTokenFailure()
    {
        // Reauthenticate for a fresh token
        if(!$this->reauthenticate()) {

            // Invoke the callback for user to handle
            if ($this->callback) {
                call_user_func_array($this->callback, array(0x05, 'token validation failed'));
                return;
            }elseif($this->environment == Endpoints::LOCAL) {
                parent::onTokenFailure();
            }

        }
    }

    /**
     * @internal
     * @throws \AuthenticationException
     */
    public function onAuthenticationFailure()
    {
        if($this->callback){
            call_user_func_array($this->callback, array(0x07,'error in authentication'));
            return;
        }elseif($this->environment == Endpoints::LOCAL) {
            parent::onAuthenticationFailure();
        }
    }

    public function onInvalidData($error_info)
    {
        if($this->callback){
            call_user_func_array($this->callback, array(0x11, $error_info));
            return;
        }elseif($this->environment == Endpoints::LOCAL) {
            parent::onInvalidData($error_info);
        }
    }

    public function onError($error_info)
    {
        if($this->callback){
            call_user_func_array($this->callback, array(0x12, $error_info));
            return;
        }elseif($this->environment == Endpoints::LOCAL) {
            parent::onInvalidData($error_info);
        }
    }


    /**
     * @internal
     * @return bool
     * @throws Exception
     */
    private function reauthenticate()
    {
        $client = new \CodeMojo\OAuth2\Client($this->client_id, $this->client_secret);
        $result = $client->getAccessToken($this->getServerEndPoint() . Endpoints::ACCESS_TOKEN,'client_credentials',array());
        if($result['code'] == 200) {
            if (isset($result['result']['access_token'])) {
                $this->storage->storeAccessToken($this->client_id, $this->client_secret, $result['result']['access_token'], $result['result']['expires_in']);
                $this->transport = new HttpGuzzle($this->storage->getAccessToken(), $this);
                return true;
            }
        }else{
            $this->onAuthenticationFailure();
        }
        return false;
    }

}