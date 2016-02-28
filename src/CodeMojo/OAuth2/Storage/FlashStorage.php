<?php

namespace CodeMojo\OAuth2\Storage;

/**
 * Class FlashStorage
 * @package DRewards\OAuth2\Storage
 */
class FlashStorage {

    /**
     * @var mixed
     */
    private $data;
    /**
     * @var string
     */
    private $path;

    /**
     * TokenStorage constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $token
     * @param $expiry
     * @return int
     */
    public function storeAccessToken($id, $secret, $token, $expiry){
        $this->data['oauth2_access_token'] = $token;
        $this->data['oauth2_expiry'] = time() + $expiry;
        $this->data['affinity'] = sha1($id . $secret . $token . $this->data['oauth2_expiry']);
    }

    /**
     * @return string
     */
    public function getAccessToken(){
        if(!isset($this->data['oauth2_access_token'])){
            return "";
        }
        return $this->data['oauth2_access_token'];
    }

    /**
     * @param $id
     * @param $secret
     * @return bool
     */
    public function accessTokenMightHaveExpired($id, $secret){
        return false;
    }


}